# ConnectMind – Multi-Tenant Deployment Guide

## Prerequisites

- PHP 8.2+
- MySQL 8+
- Composer, Node/npm
- Supervisor (for queue workers)
- Cron (for scheduler)

## 1. Landlord database

Create the central (landlord) database and run migrations:

```bash
mysql -u root -p -e "CREATE DATABASE connectmind_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Set in `.env`:

- `DB_CONNECTION=mysql`
- `DB_DATABASE=connectmind_landlord`
- `DB_USERNAME` / `DB_PASSWORD`

Run landlord migrations (creates `users`, `tenants`, `sessions`, `jobs`, etc.):

```bash
php artisan migrate
```

## 2. Tenant creation

Tenant databases are created automatically on user registration. Each tenant gets:

- A dedicated MySQL database (name: `TENANT_DB_PREFIX` + slug, e.g. `connectmind_tenant_john_abc12345`)
- Tables from `database/migrations/tenant/` (contacts, tags, interactions, reminders, roles, permissions)

No manual step required; the `TenantCreated` event and `CreateTenantDatabase` listener handle it.

## 3. Queue worker (Supervisor)

Replace `/path/to/ai-prm-saas` with your app path.

```bash
sudo cp supervisor/connectmind-worker.conf /etc/supervisor/conf.d/
# Edit and set command path and stdout_logfile
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start connectmind-worker:*
```

## 4. Scheduler (cron)

Single cron entry:

```bash
* * * * * cd /path/to/ai-prm-saas && php artisan schedule:run >> /dev/null 2>&1
```

This runs:

- `reminders:process --days=30` daily at 09:00

## 5. Web server

Point document root to `public/`. Use Laravel’s recommended nginx/Apache config (e.g. `public` as root, all requests to `index.php`).

## 6. Production checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` set
- [ ] Landlord DB created and migrated
- [ ] `php artisan config:cache` and `php artisan route:cache`
- [ ] Queue worker running (Supervisor)
- [ ] Cron for scheduler
- [ ] `OPENAI_API_KEY` set if using AI features
- [ ] WebSocket driver configured (e.g. Pusher) if using real-time notifications
