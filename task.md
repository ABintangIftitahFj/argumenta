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

- Task aktif: `F2 — App shell` [DONE] -> Siap checkpoint Foundation & lanjut `F3 — Account and onboarding`.
- Foundation (F1): Lulus 100% di lokal dan CI GitHub Actions (Run 38592280494 lulus).
- App Shell (F2):
  - Responsive app shell (Mobile bottom navigation + Desktop sidebar): Home, Learn, Practice, Coach, Progress, Profile.
  - Duolingo-inspired theme palette (Teal green `#20B87A`, accent orange, lime, cream background `#F9FAF3`), touch targets minimal 44px (`min-h-[44px] min-w-[44px]`).
  - Aksesibilitas: skip-link ke `#main-content`, `prefers-reduced-motion: reduce`, focus-visible indicators.
  - Verification: `npm run build` sukses, `docker run --rm argumenta:ci php artisan test` (7 tests, 84 assertions passed).
