# Argumenta Execution Tracker

Status: `IN_PROGRESS`
Branch kerja: `prod`
Plan detail: [tasks/plan.md](tasks/plan.md)
Checklist aktif: [tasks/todo.md](tasks/todo.md)
Source: [plan/prd.md](plan/prd.md)

## Operating rules

- Kerjakan hanya di branch `prod`.
- Jangan mulai task sebelum dependency selesai.
- Centang acceptance dan verification setelah bukti tersedia.
- Update knowledge graph setelah setiap task selesai.
- Push `prod` menjalankan CI. Merge ke `main` hanya setelah checkpoint lulus.
- Secret hanya melalui GitHub Actions Secrets atau environment lokal.

## Checklist global

- [x] Task aktif memiliki satu output yang jelas.
- [x] Acceptance criteria terukur dan sudah dicentang.
- [x] Verification command dijalankan dan hasil dicatat.
- [x] Knowledge graph diperbarui.
- [x] Commit dibuat di `prod`.
- [x] Push ke GitHub berhasil dan CI lulus.

## Current execution

- Task aktif: `F8 — Progress analytics` [IN_PROGRESS] (F7 — Transcript and evaluation selesai, tests passing 44/44).
- Transcript & Evaluation (F7):
  - Database migrations & models: `transcripts`, `transcript_segments`, `evaluations`, `evaluation_issues`.
  - Services: Typed DTOs (`TranscriptResultDto`, `TranscriptSegmentDto`, `EvaluationResultDto`, `EvaluationIssueDto`), `SpeechToTextInterface` (`SimulatedSpeechToTextService`), `AiJudgeInterface` (`DebateAiJudgeService`), `DebateEvaluationOrchestrator` (transactional pipeline).
  - Controller & Routes: `EvaluationController` (`process`, `show`, `updateTranscript`), `practice.process`, `practice.evaluation`, `practice.transcript.update`.
  - Vue Pages: `Practice/Evaluation.vue` featuring WUDC debate scorecard (Matter, Manner, Method, separate Language score), 3 strengths & 3 improvements, quote-based AREL critique annotations, interactive synchronized audio player, ASR uncertainty markers, and debater transcript correction mode.
  - Verification: `npm run build` sukses (3.72s), `docker run --rm argumenta:ci php artisan test` (44 tests, 365 assertions passed).


