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

- Task aktif: `F4 — Learning path` [DONE] -> Melanjutkan ke `F5 — Practice setup`.
- App Shell (F2) & Auth/Onboarding (F3): Lulus 100% di lokal dan CI GitHub Actions.
- Learning Path (F4):
  - Database migrations & models: `learning_modules`, `user_learning_progress`.
  - Database seeder: `LearningModuleSeeder` (5 tahap silabus kompetisi debat WUDC).
  - Controllers: `HomeController` dan `LearnController`.
  - Dynamic Vue Pages: `Home.vue` (learning path progress, streak, XP, empty vs populated recent session states) dan `Learn.vue`.
  - Global test setup: configured `RefreshDatabase` in `tests/Pest.php`.
  - Verification: `npm run build` sukses, `docker run --rm argumenta:ci php artisan test` (23 tests, 228 assertions passed).
