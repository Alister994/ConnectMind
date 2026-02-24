# Production checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` generated and set
- [ ] Landlord database created (MySQL); `php artisan migrate` run
- [ ] `php artisan config:cache`, `php artisan route:cache`
- [ ] Queue worker running (Supervisor); see `docs/DEPLOYMENT.md`
- [ ] Scheduler cron entry added
- [ ] `OPENAI_API_KEY` set if using AI features
- [ ] WebSocket / broadcast driver configured if using real-time notifications
- [ ] File permissions: `storage` and `bootstrap/cache` writable by web server
