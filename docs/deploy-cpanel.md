# Deploying on cPanel Shared Hosting

1. **Upload**: Upload the built package (from `builder/`) or repository to your domain root, pointing the DocumentRoot to `public/`.
2. **Environment**: Copy `.env.example` to `.env`, set `APP_ENV=production`, `DB_*` credentials, and generate `APP_KEY`:
   ```bash
   php bin/erp key:generate
   ```
3. **Permissions**: Ensure `storage/` (logs, cache, rate-limits) is writable (`chmod -R 775 storage/`).
4. **Database**: Run core and enabled module migrations:
   ```bash
   php bin/erp migrate
   ```
5. **Cron Job (Outbox Worker)**: Add a cPanel cron job running every minute:
   ```cron
   * * * * * /usr/local/bin/php /home/USER/erp/bin/erp events:work >/dev/null 2>&1
   ```
