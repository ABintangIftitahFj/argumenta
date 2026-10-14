<?php

namespace App\Services\Analytics;

use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\EvaluationIssue;
use App\Models\User;

class ProgressAnalyticsService
{
    /**
     * Compute comprehensive progress analytics aggregated from persisted database records.
     */
    public function getUserAnalytics(User $user): array
    {
        // 1. Overall user statistics
        $completedSessionsQuery = DebateSession::where('user_id', $user->id)
            ->where('status', 'completed');

        $totalSpeeches = $completedSessionsQuery->count();
        $totalPracticeSeconds = (int) $completedSessionsQuery->sum('duration_seconds');

        $evaluationsQuery = Evaluation::where('user_id', $user->id)
            ->where('status', 'completed');

        $averageScore = $evaluationsQuery->count() > 0
            ? round((float) $evaluationsQuery->avg('overall_score'), 1)
            : 0.0;

        $matterAverage = $evaluationsQuery->count() > 0
            ? round((float) $evaluationsQuery->avg('matter_score'), 1)
            : 0.0;

        $mannerAverage = $evaluationsQuery->count() > 0
            ? round((float) $evaluationsQuery->avg('manner_score'), 1)
            : 0.0;

        $methodAverage = $evaluationsQuery->count() > 0
            ? round((float) $evaluationsQuery->avg('method_score'), 1)
            : 0.0;

        $user->loadMissing('profile');
        $streak = $user->profile?->streak_count ?? 0;
        $totalXp = $user->profile?->total_xp ?? 0;

        // 2. Speech history (most recent first)
        $historySessions = DebateSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['evaluation', 'latestRecording'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        $history = $historySessions->map(function (DebateSession $session) {
            return [
                'session_id' => $session->id,
                'motion' => $session->motion_text,
                'format' => $session->format,
                'role' => $session->role,
                'language' => $session->language,
                'overall_score' => $session->evaluation ? (float) $session->evaluation->overall_score : null,
                'duration_seconds' => $session->duration_seconds,
                'formatted_duration' => sprintf(
                    '%dm %02ds',
                    intdiv($session->duration_seconds, 60),
                    $session->duration_seconds % 60
                ),
                'date' => $session->created_at->translatedFormat('d M Y, H:i'),
                'evaluation_url' => route('practice.evaluation', $session->id),
            ];
        })->values()->all();

        // 3. Skill trends across chronological evaluations
        $chronologicalEvaluations = Evaluation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->take(20)
            ->get();

        $skillTrends = $chronologicalEvaluations->map(function (Evaluation $evaluation) {
            return [
                'evaluation_id' => $evaluation->id,
                'debate_format' => $evaluation->debate_format,
                'role' => $evaluation->role,
                'overall_score' => (float) $evaluation->overall_score,
                'matter_score' => (float) $evaluation->matter_score,
                'manner_score' => (float) $evaluation->manner_score,
                'method_score' => (float) $evaluation->method_score,
                'language_score' => $evaluation->language_score !== null ? (float) $evaluation->language_score : null,
                'date' => $evaluation->created_at->format('d/m'),
            ];
        })->values()->all();

        // 4. Error frequency analysis from evaluation issues
        $evaluationIds = $chronologicalEvaluations->pluck('id');
        $issues = EvaluationIssue::whereIn('evaluation_id', $evaluationIds)->get();

        $errorCategoryCounts = [];
        $errorCategorySamples = [];

        foreach ($issues as $issue) {
            $cat = $issue->category;
            if (! isset($errorCategoryCounts[$cat])) {
                $errorCategoryCounts[$cat] = 0;
                $errorCategorySamples[$cat] = [];
            }
            $errorCategoryCounts[$cat]++;
            if (count($errorCategorySamples[$cat]) < 2) {
                $errorCategorySamples[$cat][] = [
                    'title' => $issue->title,
                    'quote' => $issue->original_quote,
                    'fix' => $issue->suggested_fix,
                    'severity' => $issue->severity,
                ];
            }
        }

        arsort($errorCategoryCounts);

        $errorFrequency = [];
        foreach ($errorCategoryCounts as $category => $count) {
            $errorFrequency[] = [
                'category' => $category,
                'count' => $count,
                'label' => match ($category) {
                    'arel' => 'Struktur AREL & Kausalitas',
                    'matter' => 'Substansi & Kuantifikasi Bukti',
                    'manner' => 'Retorika & Tempo Bicara',
                    'method' => 'Signposting & Urutan Clash',
                    'language' => 'Kekayaan Diksi & Grammar',
                    'logic_fallacy' => 'Kesesatan Logika (Fallacy)',
                    default => ucfirst($category),
                },
                'samples' => $errorCategorySamples[$category] ?? [],
            ];
        }

        // 5. Next drill recommendation
        $latestEvaluation = $chronologicalEvaluations->last();
        $nextRecommendation = $latestEvaluation
            ? $latestEvaluation->next_drill_recommendation
            : 'Mulai pidato pertama Anda untuk mendapatkan analisis kurikulum terpersonalisasi.';

        return [
            'stats' => [
                'total_speeches' => $totalSpeeches,
                'total_practice_seconds' => $totalPracticeSeconds,
                'formatted_practice_time' => sprintf(
                    '%d jam %d menit',
                    intdiv($totalPracticeSeconds, 3600),
                    intdiv($totalPracticeSeconds % 3600, 60)
                ),
                'average_score' => $averageScore,
                'matter_average' => $matterAverage,
                'manner_average' => $mannerAverage,
                'method_average' => $methodAverage,
                'streak' => $streak,
                'xp_points' => $totalXp,
            ],
            'history' => $history,
            'skill_trends' => $skillTrends,
            'error_frequency' => $errorFrequency,
            'next_recommendation' => $nextRecommendation,
        ];
    }
}
