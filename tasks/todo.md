# Argumenta Task Checklist

Legend: `[ ]` belum selesai, `[x]` selesai, `[-]` diblokir.

## F1 — Foundation

- [x] **Acceptance:** Laravel 13 project bootstrapped with PHP 8.3 requirement.
- [x] **Acceptance:** Vue 3, Inertia, TypeScript, and Tailwind dependencies declared.
- [x] **Acceptance:** App has Vite entrypoints and root route.
- [x] **Acceptance:** Dockerfile and Compose define app plus MySQL services.
- [x] **Verification:** `docker compose config` passes.
- [x] **Verification:** `composer validate` passes.
- [x] **Verification:** `docker build --tag argumenta:ci .` passes.
- [x] **Verification:** `php artisan test` passes (1 passed, 12 assertions).
- [x] **Checkpoint:** Record versions, commands, and CI result in `task.md`.
- [x] **Knowledge graph:** Mark F1 `DONE`; unblock F2.
- [x] **Git:** Commit and push `prod`; CI passes.

## F2 — App shell

- [x] **Acceptance:** Responsive navigation exposes Home, Learn, Practice, Progress, Profile.
- [x] **Acceptance:** Theme uses PRD palette and touch targets are at least 44px.
- [x] **Acceptance:** Reduced-motion preference and keyboard focus are supported.
- [x] **Verification:** Build and focused UI test pass.
- [x] **Verification:** Manual mobile and desktop smoke check passes.
- [x] **Knowledge graph:** Mark F2 `DONE`; unblock F3.
- [x] **Git:** Commit and push `prod`; CI passes.

## F3 — Account and onboarding

- [x] **Acceptance:** User can register, log in, and log out.
- [x] **Acceptance:** Onboarding stores language, goal, level, and preferred format.
- [x] **Verification:** Auth and validation tests pass.
- [x] **Knowledge graph:** Mark F3 `DONE`; unblock F4 and F10.
- [x] **Git:** Commit and push `prod`; CI passes.

## F4 — Learning path

- [x] **Acceptance:** Home shows learning path, streak, XP, recent session, and CTA.
- [x] **Acceptance:** Empty and populated states are handled.
- [x] **Verification:** Feature tests and responsive smoke check pass.
- [x] **Knowledge graph:** Mark F4 `DONE`; unblock F5.
- [x] **Git:** Commit and push `prod`; CI passes.

## F5 — Practice setup

- [x] **Acceptance:** User selects format, motion, role, language, and difficulty.
- [x] **Acceptance:** Server validates configuration and calculates duration/rules.
- [x] **Verification:** Validation tests cover invalid and valid configurations.
- [x] **Knowledge graph:** Mark F5 `DONE`; unblock F6.
- [x] **Git:** Commit and push `prod`; CI passes.

## F6 — Speech recording

- [x] **Acceptance:** User can start, pause, stop, and review a recording.
- [x] **Acceptance:** Session state shows recording, upload, queued, and failure states.
- [x] **Verification:** MediaRecorder support and upload state tests pass.
- [x] **Knowledge graph:** Mark F6 `DONE`; unblock F7.
- [x] **Git:** Commit and push `prod`; CI passes.

## F7 — Transcript and evaluation

- [x] **Acceptance:** Transcript supports uncertainty markers and user correction.
- [x] **Acceptance:** Evaluation shows three strengths and three improvements.
- [x] **Verification:** Typed provider response and persistence tests pass.
- [x] **Knowledge graph:** Mark F7 `DONE`; unblock F8 and F9.
- [x] **Git:** Commit and push `prod`; CI passes.

## F8 — Progress analytics

- [x] **Acceptance:** User sees history, skill trend, errors, and next recommendation.
- [x] **Verification:** Analytics aggregation tests pass.
- [x] **Knowledge graph:** Mark F8 `DONE`; unblock release checkpoint.
- [x] **Git:** Commit and push `prod`; CI passes.

## F9 — Quota and AI audit

- [x] **Acceptance:** AI calls stop when quota or budget is exceeded.
- [x] **Acceptance:** User, feature, model, provider usage, and cost are logged.
- [x] **Verification:** Quota, logging, and failure-path tests pass.
- [x] **Knowledge graph:** Mark F9 `DONE`; unblock release checkpoint.
- [x] **Git:** Commit and push `prod`; CI passes.

## F10 — Authorization and observability

- [x] **Acceptance:** Users cannot access another user's audio or report.
- [x] **Acceptance:** Errors produce explicit UI state and server logs.
- [x] **Verification:** Authorization tests pass for positive and negative cases.
- [x] **Knowledge graph:** Mark F10 `DONE`; unblock release checkpoint.
- [x] **Git:** Commit and push `prod`; CI passes.

## F11 — Release `prod` to `main`

- [x] **Acceptance:** Foundation, core flow, speech MVP, quota, and security checkpoints pass.
- [x] **Acceptance:** Production environment and deployment config are documented.
- [x] **Verification:** Full test suite and production build pass.
- [x] **Verification:** Review `prod` diff before merge.
- [x] **Git:** Merge `prod` into `main`, push `main`, verify CI.

## Checkpoints

### Foundation

- [x] F1 and F2 acceptance complete.
- [x] Tests and build pass.
- [x] Human review before F3.

### Core navigation

- [x] F3, F4, and F5 acceptance complete.
- [x] Auth-to-practice setup flow works.
- [x] Human review before F6.

### Speech MVP

- [x] F6, F7, and F8 acceptance complete.
- [x] Recording-to-feedback flow works.
- [x] Human review before F9.

### Release readiness

- [x] F9 and F10 acceptance complete.
- [x] Security and quota tests pass.
- [x] `prod` approved for merge to `main`.
