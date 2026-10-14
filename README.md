# Argumenta — AI Debate Coach & Adjudication PWA

Argumenta is a modern, mobile-first Progressive Web Application (PWA) designed to coach parliamentary debaters (British Parliamentary & Asian Parliamentary formats) through evidence-based speech evaluation, structured AREL (Assertion, Reasoning, Evidence, Linkback) argumentation feedback, and gamified skill progression.

---

## 🌟 Key Features

- **Mobile-First Responsive Shell**: Bottom navigation with touch targets $\ge 44$px on mobile, responsive desktop sidebar, bilingual EN/ID toggle, streak, and XP counters.
- **Account & Onboarding**: Email/password authentication, user profiles, and an interactive 4-step wizard to personalize target formats, languages, and debate goals.
- **WUDC Learning Path**: 5-stage progressive curriculum covering Foundations, Matter Construction, AREL Mechanism, Role Fulfillment & Extension, and Adjudication & Comparative Weighting.
- **Debate Rule Engine & Setup**:
  - **British Parliamentary (BP)**: 8 roles (PM, LO, DPM, DLO, MG, MO, GW, OW), 7-minute speeches with POI window (1:00–6:00).
  - **Asian Parliamentary (AP)**: 6 substantive roles (7-minute with POI window) and 2 reply roles (4-minute, no POIs).
  - Interactive 15-minute prep timer, role-specific strategic briefs, and scratchpad.
- **Speech Recording Studio**: Web Audio API `AnalyserNode` live audio meter, stopwatch with POI window bell cues, private audio storage, and client-side playback.
- **AI Speech Evaluation & Scorecard**:
  - Speech-to-text pipeline with uncertainty markers.
  - WUDC standard speaker scoring (65–85) with separate language proficiency rating.
  - Exactly 3 strengths and 3 actionable improvements.
  - Timestamped AREL critique annotations mapped to speech quotes.
  - Debater transcript editing and correction tool.
- **Progress & Performance Analytics**: Aggregate debate statistics, score evolution trend charts, rubric distribution breakdown, recurring error diagnosis, and recommended next drills.
- **Quota & AI Audit Guardrails**: Atomic credit consumption, automatic refunds on processing failures, token and cost audit logging, and 429 quota protection.
- **Strict Authorization & Observability**: Session-scoped audio and scorecard access control, structured security logging, and user-friendly error views.

---

## 🛠️ Technology Stack

- **Backend**: Laravel 13, PHP 8.3 CLI, Composer 2
- **Frontend**: Vue 3 (Composition API), Inertia.js v2, TypeScript, Tailwind CSS v4
- **Database**: MySQL 8.4 LTS
- **Runtime & Containers**: Multi-stage Dockerfile, Docker Compose
- **Testing**: Pest PHP / PHPUnit with 58 automated feature tests

---

## 🚀 Quick Start (Local Development with Docker)

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/)

### 1. Clone & Start Containers

```bash
git clone https://github.com/ABintangIftitahFj/argumenta.git
cd argumenta

# Build and start services (Application and MySQL)
docker compose up -d --build
```

### 2. Initialize Database & Seeders

```bash
docker compose exec app php artisan migrate --seed
```

### 3. Access Application

Open your browser at:
- Web Application: [http://localhost:8000](http://localhost:8000)

### 4. Run Automated Tests

```bash
docker compose exec app php artisan test
```

---

## 📖 Production Deployment

For detailed production deployment instructions including cPanel shared hosting, Supervisor queue workers, and S3-compatible storage configuration, refer to the [Production Deployment Guide](docs/deployment.md).

---

## 🔒 Security & Privacy

- Audio recordings are saved strictly on private storage disks (`storage/app/private/` or private cloud buckets) and are never exposed via public URLs.
- Streaming endpoints strictly enforce debater ownership via authorization checks and log unauthorized attempts.

---

## 📄 License

Open-source under the [MIT License](LICENSE).
