<?php

namespace App\Services\SpeechToText;

use App\Models\SpeechRecording;

class SimulatedSpeechToTextService implements SpeechToTextInterface
{
    /**
     * Transcribe speech recording into structured transcript with timestamps and uncertainty markers.
     */
    public function transcribe(SpeechRecording $recording): TranscriptResultDto
    {
        $session = $recording->session;
        $isIndonesian = ($session && $session->language === 'id');
        $duration = max(5, $recording->duration_seconds ?: 60);

        if ($isIndonesian) {
            $rawSegments = [
                [0.0, 8.5, 'Terima kasih dewan juri yang terhormat dan rekan-rekan debater sekalian.', 0.98, false],
                [8.5, 19.0, 'Sebagai perwakilan dari pihak pembuka, mosi hari ini menuntut kita meninjau dampak struktural yang nyata.', 0.94, false],
                [19.0, 31.5, 'Argumen pertama kami berfokus pada urgensi alokasi sumber daya bagi kelompok yang paling rentan.', 0.91, false],
                [31.5, 43.0, 'Ketika mekanisme pasar konvensional gagal memberikan jaminan akses, intervensi negara menjadi keniscayaan moral.', 0.88, false],
                [43.0, 56.0, 'Klaim tim oposisi mengenai efisiensi fiskal kerap mengabaikan biaya eksternalitas jangka panjang yang timbul.', 0.76, true],
                [56.0, 68.0, 'Oleh karena itu, kami tegaskan bahwa proposisi ini memberikan komparasi manfaat yang jauh lebih eksklusif.', 0.95, false],
            ];
        } else {
            $rawSegments = [
                [0.0, 8.5, 'Thank you, Madam Speaker, esteemed adjudicators, and members of the house.', 0.98, false],
                [8.5, 19.0, 'Standing before you today, the proposition firmly believes that this policy addresses an acute structural disparity.', 0.95, false],
                [19.0, 31.5, 'Our first substantive point centers on the principle of redistributive justice and systemic vulnerability.', 0.92, false],
                [31.5, 43.0, 'When unfettered market incentives consistently underserve critical public goods, state intervention is not merely justifiable, but imperative.', 0.89, false],
                [43.0, 56.0, 'The opposition will undoubtedly cite capital flight and fiscal strain, yet empirical counter-evidence remains noticeably inconclusive.', 0.74, true],
                [56.0, 68.0, 'For these paramount reasons, we take great pride in proposing this motion.', 0.96, false],
            ];
        }

        // Scale timestamps to the actual recording duration
        $baseTotalTime = 68.0;
        $scale = $duration / $baseTotalTime;

        $segments = [];
        $wordsCount = 0;
        $hasUncertainty = false;

        foreach ($rawSegments as $index => [$start, $end, $text, $confidence, $uncertain]) {
            $scaledStart = round($start * $scale, 2);
            $scaledEnd = round($end * $scale, 2);
            $wordsInSegment = str_word_count($text);
            $wordsCount += $wordsInSegment;

            if ($uncertain) {
                $hasUncertainty = true;
            }

            $segments[] = new TranscriptSegmentDto(
                segmentOrder: $index + 1,
                startTimeSeconds: $scaledStart,
                endTimeSeconds: $scaledEnd,
                text: $text,
                confidence: $confidence,
                isUncertain: $uncertain,
            );
        }

        $fullRawText = implode(' ', array_map(fn (TranscriptSegmentDto $s) => $s->text, $segments));
        $minutes = max(0.1, $duration / 60);
        $wpm = (int) round($wordsCount / $minutes);

        return new TranscriptResultDto(
            rawText: $fullRawText,
            language: $isIndonesian ? 'id' : 'en',
            confidenceScore: $hasUncertainty ? 0.915 : 0.965,
            wordsCount: $wordsCount,
            wordsPerMinute: $wpm,
            hasUncertainty: $hasUncertainty,
            audioQualityWarning: $hasUncertainty ? 'Beberapa frasa terdeteksi dengan tingkat kepercayaan menengah karena intonasi cepat atau modulasi audio.' : null,
            asrProvider: 'simulated_stt',
            segments: $segments,
        );
    }
}
