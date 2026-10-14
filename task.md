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

- Task aktif: `F7 — Transcript and evaluation` [IN_PROGRESS] (F6 — Speech recording selesai, tests passing 37/37).
- Speech Recording (F6):
  - Database migrations & models: `speech_recordings` table with private disk tracking (`debate_session_id`, `file_path`, `mime_type`, `file_size_bytes`, `duration_seconds`, `upload_status`, `storage_disk`).
  - Storage: Private filesystem disk configured (`storage/app/private`).
  - Controller & Routes: `SpeechRecordingController` (`show`, `store` with max 50MB audio validation, `stream` strictly authorizing session ownership).
  - Vue Pages: `Practice/Record.vue` featuring live Web Audio API volume visualizer, dynamic stopwatch with POI window & grace period indicators, state machine (`idle` -> `recording` -> `paused` -> `stopped` -> `uploading` -> `uploaded` -> `failed`), audio preview player, and $\ge 44$px touch targets.
  - Verification: `npm run build` sukses, `docker run --rm argumenta:ci php artisan test` (37 tests, 310 assertions passed).

