# ConnectMind – AI Personal Relationship Manager

**Multi-tenant SaaS to manage contacts, track interactions, and use AI for summaries, reminders, and relationship insights.**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🚀 Features

- **Multi-tenancy** – Database per tenant; central landlord DB; tenant created on registration.
- **Contacts** – Add, edit, delete contacts; tags; last interaction date; relationship strength score.
- **Interactions** – Meeting notes, call logs, email summaries; AI summarization via **queue jobs**.
- **AI** – Summarize notes, “stay in touch” message, follow-up reminder suggestions, relationship health score.
- **Reminder engine** – **Cron-based** “You haven’t contacted X in N days” alerts.
- **REST API** – Full API for contacts, interactions, tags, reminders, and AI endpoints.
- **Real-time** – WebSocket-ready notifications (e.g. Pusher); notification class included.
- **Role-based** – Tenant-level roles and permissions (tables and models in place).

---

## 🏗 Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade, TailwindCSS, Alpine.js, Vite
- **Database:** MySQL (landlord + one database per tenant)
- **Real-time:** Laravel Broadcasting (Pusher / Soketi compatible)
- **Queue:** Laravel Queue (database driver); Supervisor for workers
- **Third-party:** OpenAI API (optional), Laravel Sanctum (API auth)

---

## 📸 Screenshots

<!-- Add your screenshots here -->
<!-- ![Dashboard](docs/screenshots/dashboard.png) -->
<!-- ![Contacts](docs/screenshots/contacts.png) -->

---

## ⚙️ Installation Guide (Local Setup)

### 1. Clone repository

```bash
git clone https://github.com/your-org/ai-prm-saas.git
cd ai-prm-saas
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`: set `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Optionally set `OPENAI_API_KEY` for AI features.

### 4. Database setup

Create the landlord database:

```bash
mysql -u root -p -e "CREATE DATABASE connectmind_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Migrations

```bash
php artisan migrate
```

Tenant tables are created automatically when a user registers (no separate tenant migration step).

### 6. Queue worker setup

In a separate terminal:

```bash
php artisan queue:work
```

### 7. WebSocket server setup (optional)

Set `BROADCAST_CONNECTION=pusher` and `PUSHER_*` in `.env`, then use Laravel Echo in the frontend. Or use Soketi and configure `config/broadcasting.php` accordingly.

### 8. Scheduler setup

For local development:

```bash
php artisan schedule:work
```

For production, add to crontab: `* * * * * cd /path/to/ai-prm-saas && php artisan schedule:run >> /dev/null 2>&1`

### 9. Run development server

```bash
npm run dev
```

In another terminal:

```bash
php artisan serve
```

Open `http://localhost:8000` and register a new user. A tenant database is created automatically.

---

## 🌐 Environment Configuration

| Variable | Description |
|---------|-------------|
| `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Landlord MySQL connection. |
| `TENANT_DB_PREFIX` | Prefix for tenant database names (e.g. `connectmind_tenant_`). |
| `OPENAI_API_KEY` | OpenAI API key; required for AI features. |
| `OPENAI_MODEL` | Model name (default: `gpt-4o-mini`). |
| `OPENAI_MAX_DAILY_TOKENS_PER_TENANT` | Daily token cap per tenant. |
| `QUEUE_CONNECTION` | Use `database` or `redis`. |
| `BROADCAST_CONNECTION` | Set to `pusher` (or your driver) for WebSockets. |
| `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER` | Required when using Pusher. |

---

## 📡 Real-Time / AI / Background Processing

- **Events:** `TenantCreated` → creates tenant DB and runs tenant migrations. `InteractionCreated` → dispatches AI summarization job.
- **Queue jobs:** `SummarizeInteractionNotesJob` (summarize interaction notes), `GenerateRelationshipScoreJob` (relationship health). Run with `php artisan queue:work`.
- **Broadcasting:** `ReminderDueNotification` is sent via the configured broadcast driver; use Laravel Echo on the client for real-time notifications.
- **Scheduler:** `reminders:process --days=30` runs daily at 09:00 to list contacts not contacted in 30 days.

---

## 📂 Project Structure Overview

| Path | Purpose |
|------|---------|
| `app/Models/` | Landlord: `User`, `Tenant`. Tenant: `TenantModel`, `Contact`, `Tag`, `Interaction`, `Reminder`, `Role`, `Permission`. |
| `app/Services/` | `TenantManager`, `OpenAIService`, `ContactService`, `InteractionService`. |
| `app/Repositories/` | Repository pattern: `ContactRepository`, `InteractionRepository`; interfaces in `Contracts/`. |
| `app/Events/` | `TenantCreated`, `InteractionCreated`. |
| `app/Listeners/` | `CreateTenantDatabase`, `DispatchSummarizeInteractionJob`. |
| `app/Jobs/` | `SummarizeInteractionNotesJob`, `GenerateRelationshipScoreJob`. |
| `database/migrations/` | Landlord tables. |
| `database/migrations/tenant/` | Tenant tables (contacts, tags, interactions, reminders, roles, permissions). |

---

## 🧪 Testing Instructions

Run the test suite:

```bash
composer test
```

Or:

```bash
./vendor/bin/phpunit
```

---

## 🤝 Contributing Guide

1. **Fork** the repository on GitHub.
2. **Create a branch:** `git checkout -b feature/short-description`.
3. **Commit:** Use clear messages, e.g. `feat: add contact export`, `fix: tenant migration on SQLite`.
4. **Push** and open a **Pull Request** against the default branch. Describe the change and reference any issues.
5. **Good first issues:** Look for issues labeled `good first issue` or `help wanted` to get started.

---

## 🛠 Roadmap

- In-app WebSocket client and notification UI.
- Deeper RBAC (e.g. Spatie Laravel Permission).
- Optional SQLite tenant mode for small deployments.
- More AI actions (e.g. suggested next steps per contact).

---

## 📄 License

MIT. See [LICENSE](LICENSE).

---

## 👤 Author

**Your Name** – [GitHub profile](https://github.com/your-username)

If this project is useful to you, consider giving it a **star**.
