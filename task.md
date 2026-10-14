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

- Task aktif: `F10 — Authorization and observability` [IN_PROGRESS] (F9 — Quota and AI audit selesai; tests passing 53/53).
- Quota & AI Audit (F9):
  - Database migrations & models: `user_credits`, `credit_transactions`, `ai_usage_logs` tracking user credits, plan tiers, atomic debits/refunds, AI model, provider, tokens, duration, cost, and error status.
  - Services & Exceptions: `QuotaGuardService` (atomic credit lock, debit, and refund), `AiUsageAuditService` (AI token, cost, and provider audit), `QuotaExceededException` (HTTP 429).
  - Integration: `DebateEvaluationOrchestrator` guards credit consumption before AI execution, logs tokens/cost upon success, refunds credits and logs `provider_error` on failure paths, and redirects with error in `EvaluationController`.
  - Verification: `npm run build` sukses (3.91s), `docker run --rm argumenta:ci php artisan test` (53 tests, 455 assertions passed).




