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

- Task aktif: `F3 — Account and onboarding` [DONE] -> Melanjutkan ke `F4 — Learning path`.
- App Shell (F2): Lulus 100% di lokal dan CI GitHub Actions (Run 38593751797 lulus).
- Account & Onboarding (F3):
  - Model `User` + `Profile` (1:1 relation), migration `profiles` table.
  - Controllers: `RegisterController`, `LoginController`, `OnboardingController`.
  - Vue Pages: `Auth/Register.vue`, `Auth/Login.vue`, `Onboarding/Setup.vue` (4-step interactive wizard).
  - AppLayout dynamic state: auth/guest detection, user card, logout action.
  - Dependencies: added `fakerphp/faker` & `mockery/mockery` for robust Pest testing.
  - Verification: `npm run build` sukses, `docker run --rm argumenta:ci php artisan test` (19 tests, 155 assertions passed).
