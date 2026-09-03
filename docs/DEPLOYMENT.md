# Production deployment and verification

## Requirements

Use the PHP version and extensions required by `composer.json`, Composer 2, Node/npm for the asset build, a supported MySQL/MariaDB/PostgreSQL database, a process supervisor for queue workers, cron, TLS termination, and persistent writable `storage/` and `bootstrap/cache/` directories.

## Environment and secrets

Set `APP_ENV=production`, `APP_DEBUG=false`, a newly generated `APP_KEY`, and the real HTTPS `APP_URL`. Set `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `SESSION_HTTP_ONLY=true`, and `SESSION_SAME_SITE=lax`. Use durable database/cache/session/queue services; do not use `array` drivers in production. Store database, SMTP, cloud-storage, and monitoring credentials only in the deployment secret store—never in source control.

Configure `MAIL_MAILER=smtp` (or an approved provider), `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`/`MAIL_SCHEME`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`. Reset-password and workspace-invitation delivery must be tested against that provider before launch.

## Release procedure

1. Take and verify a database backup, then review the migration list.
2. Install locked dependencies: `composer install --no-dev --prefer-dist --optimize-autoloader` and `npm ci && npm run build`.
3. Before running `php artisan migrate --force`, review workspace ownership and membership for legacy Client, Project, Task, and TeamMember records. A new/empty database migrates normally and creates no default workspace or user; onboarding creates the first workspace. A legacy database containing those records stops safely rather than guessing workspace ownership or membership. Do not bypass that stop: prepare and approve an explicit, non-destructive mapping first. Never use `migrate:fresh`, `migrate:refresh`, wipe, reset, truncate, or drop operations.
4. Run `php artisan storage:link`, then cache configuration/routes/views only after environment variables are final: `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`.
5. Restart PHP workers and a supervised `php artisan queue:work --tries=3 --timeout=90` process. Configure cron: `* * * * * php /path/to/artisan schedule:run`.
6. Check the non-sensitive `/up` health endpoint and application/queue logs. Roll back application code independently; do not run destructive migration rollback without an approved recovery plan.

## Web and platform controls

Force HTTPS at the proxy/web server, forward trusted proxy headers only from the platform proxy, set a secure domain cookie scope, disable directory listing, and permit only `public/` as document root. Laravel sends frame, MIME-sniffing, referrer, permissions, and HTTPS HSTS headers. Configure backup retention/restoration drills, uptime/error monitoring, disk/database capacity alerts, log rotation, and TLS renewal at the platform layer.

## PWA and UI manual verification

On an HTTPS staging deployment, test at 375, 390, 430, 768, 1280, 1440, and 1920px: keyboard focus, validation errors, menu/drawer, switcher, autocomplete, modal, Kanban scrolling, light/dark mode, and reduced motion. In browser DevTools: install the PWA, reload after a service-worker update, go offline and verify only the generic offline page appears, then login/logout and switch accounts. Confirm Cache Storage contains static assets/offline page only—never HTML, API, dashboard, notification, client, project, task, or workspace responses.

## Ongoing operations

Run `php artisan test`, `./vendor/bin/pint --test`, and production asset builds in CI. Run `composer audit` and `npm audit --omit=dev` when registry connectivity is available; review advisories before deliberately upgrading dependencies. Monitor failed jobs, password-reset/invitation mail, authentication failures, backup success, and migration status.
