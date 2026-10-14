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

- Task aktif: `F5 — Practice setup` [DONE] -> Checkpoint Core Navigation selesai; siap review & lanjut ke Phase 3 `F6 — Speech recording`.
- App Shell (F2), Auth (F3), & Learning Path (F4): Lulus 100% di lokal dan CI GitHub Actions.
- Practice Setup (F5):
  - Database migrations & models: `motions`, `debate_sessions`.
  - Service: `DebateRuleEngine` (BP 8 roles, AP 8 roles incl. 4-minute Reply speeches, 7-minute substantives, POI window 1:00-6:00).
  - Database seeder: `MotionSeeder` (mosi bilingual lintas tema).
  - Controller: `PracticeSetupController` (`create`, `store`, `prepare`).
  - Vue Pages: `Practice.vue` (motion picker, custom motion, dynamic BP/AP role grid, speech rules preview) dan `Practice/Prepare.vue` (15m prep timer, scratchpad, brief).
  - Verification: `npm run build` sukses, `docker run --rm argumenta:ci php artisan test` (31 tests, 282 assertions passed).
