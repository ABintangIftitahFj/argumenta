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

- Task aktif: `F9 — Quota and AI audit` [IN_PROGRESS] (Phase 3: Speech MVP selesai: F6, F7, F8 lulus; tests passing 48/48).
- Progress Analytics (F8):
  - Service: `ProgressAnalyticsService` aggregating user metrics from database records without triggering repetitive LLM calls (speech counts, duration, average scorecard, matter/manner/method distributions, streak count, total XP, timeline skill trends, error frequency analysis with code examples, and personalized next drill recommendation).
  - Controller & Routes: `ProgressController` invoked at `/progress` with authenticated debater analytics and graceful guest shell preview.
  - Vue Pages: `Progress.vue` rendering 4 core KPI cards, next recommendation CTA card, rubric breakdown progress bars, chronological skill trend cards, common error frequency breakdown, and paginated speech history cards linking to evaluations.
  - Verification: `npm run build` sukses (3.72s), `docker run --rm argumenta:ci php artisan test` (48 tests, 435 assertions passed).



