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
 
- Task aktif: `F11 — Release prod to main` [IN_PROGRESS].
- Release Preparation (F11):
  - Documented production environment configuration (`.env.example`) including database, queues, private recordings disk, S3/R2 storage, and Gemini/SumoPod AI provider keys.
  - Authored comprehensive `README.md` and `docs/deployment.md` covering Dockerized deployment, cPanel/Linux server operations, Supervisor/Cron queue setups, directory permissions, and health verification.
  - Updated CI workflow `.github/workflows/push.yml` to execute automated test suite in Docker on push.
  - Full test suite verified: 58 passed (473 assertions) in Docker container.
  - Ready for merge to `main` and release push.




