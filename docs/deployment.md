# Argumenta — Production Deployment & Operations Guide

This guide details the requirements, configuration, and step-by-step procedures for deploying **Argumenta (AI Debate Coach PWA)** to production environments, including Dockerized servers and traditional Linux/cPanel shared hosting.

---

## 1. System Requirements

| Component | Minimum Version | Recommended / Production |
|---|---|---|
| **PHP** | 8.3 | 8.3 (CLI & FPM) with extensions: `pdo_mysql`, `intl`, `mbstring`, `fileinfo`, `pcntl`, `bcmath`, `curl` |
| **Node.js** | 20.x | 24.x (LTS) for compiling frontend assets via Vite |
| **Database** | MySQL 8.0+ / MariaDB 10.5+ | MySQL 8.4 LTS (utf8mb4 encoding) |
| **Web Server** | Nginx / Apache 2.4 | Nginx with HTTP/2 and SSL |
| **Process Manager** | Systemd / Supervisor / Cron | Supervisor (or cPanel Cron for shared hosting) |
| **Storage** | Local SSD | Private SSD or S3-compatible object storage (SumoPod S3 / Cloudflare R2) |

---

## 2. Architecture & Security Model

- **Frontend Assets**: Pre-compiled static assets (`public/build`) served directly by the web server. No Node.js runtime is needed in production.
- **Web Root**: Must point strictly to the `public/` directory (`/var/www/html/public` or `/home/{user}/public_html`). The project root, `.env`, and `storage/` must never be directly accessible from the web.
- **Private Audio Storage**: User debate recordings are saved to `storage/app/private/recordings/{user_id}/` (or a private S3 bucket). Audio streaming is strictly authorized through `GET /practice/{session}/audio` which verifies debater session ownership.
- **Atomic Quotas & Audit**: AI usage is metered per-session with atomic reservation and automatic refund upon unrecoverable provider failures.

---

## 3. Environment Configuration (`.env`)

Copy `.env.example` to `.env` and configure the following required settings:

```bash
cp .env.example .env
php artisan key:generate
```

### Essential Production Keys

```ini
APP_NAME=Argumenta
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=argumenta_production
DB_USERNAME=argumenta_user
DB_PASSWORD=your_secure_password

# Sessions & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# Queue Worker (handles asynchronous STT and AI evaluation)
QUEUE_CONNECTION=database

# Storage Disks
FILESYSTEM_DISK=local
RECORDINGS_DISK=private

# Optional External S3 / R2 Object Storage
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=argumenta-private-recordings
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false

# AI Provider Credentials
AI_PROVIDER=gemini        # 'gemini' | 'sumopod' | 'simulated'
AI_MODEL_JUDGE=gemini-1.5-pro
AI_MODEL_COACH=gemini-1.5-flash
GEMINI_API_KEY=your_gemini_api_key
SUMOPOD_API_KEY=your_sumopod_api_key
SUMOPOD_BASE_URL=https://api.sumopod.com/v1
```

---

## 4. Deployment Methods

### Option A: Docker Deployment (Recommended)

The repository provides a multi-stage `Dockerfile` and `docker-compose.yml`.

1. **Build and start the application containers**:
   ```bash
   docker compose up -d --build
   ```

2. **Run database migrations and seed default motions & modules**:
   ```bash
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan db:seed --force
   ```

3. **Verify running containers**:
   ```bash
   docker compose ps
   ```

---

### Option B: Linux / cPanel Shared Hosting

1. **Upload / Clone the Repository**:
   Place the project files outside the web root (e.g., `/home/username/argumenta`).

2. **Configure Document Root**:
   Point your domain's Document Root to `/home/username/argumenta/public`.

3. **Install Dependencies and Build Assets**:
   ```bash
   # Install PHP dependencies without dev packages
   composer install --no-dev --optimize-autoloader --no-interaction

   # Install frontend packages and compile production bundle
   npm ci
   npm run build
   ```

4. **Directory Permissions**:
   Ensure write permissions for the web server user (`www-data` or cPanel user):
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

5. **Run Migrations & Seeds**:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Create Storage Link**:
   ```bash
   php artisan storage:link
   ```

7. **Optimize Framework Caches**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Queue Worker / Cron Configuration**:
   - **For VPS / Dedicated (Supervisor)**:
     ```ini
     [program:argumenta-worker]
     process_name=%(program_name)s_%(process_num)02d
     command=php /home/username/argumenta/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
     autostart=true
     autorestart=true
     user=username
     numprocs=2
     redirect_stderr=true
     stdout_logfile=/home/username/argumenta/storage/logs/worker.log
     ```
   - **For cPanel Cron**:
     Add a cron job running every minute:
     ```bash
     * * * * * php /home/username/argumenta/artisan schedule:run >> /dev/null 2>&1
     ```

---

## 5. Verification & Health Check

After deployment, verify the installation:

1. **Run full automated test suite**:
   ```bash
   php artisan test
   ```
2. **Verify HTTP status and PWA manifests**:
   - `GET /` returns `200 OK` with Inertia page payload.
   - `GET /manifest.webmanifest` returns valid PWA configuration.
   - Unauthorized audio streaming `GET /practice/{session}/audio` returns `403 Forbidden` and logs a security warning.
