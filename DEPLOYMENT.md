# Production deployment guide

This guide targets a conventional Laravel-compatible PHP host (including Cloudways-style hosting). The project is a Laravel 13.x application and uses server-rendered Blade; do not deploy it as a Node frontend.

## Requirements

- PHP 8.3 or later, plus PHP extensions for PDO/MySQL, OpenSSL, Mbstring, Fileinfo, Tokenizer, XML, Ctype, JSON, and BCMath.
- Composer 2.
- MySQL or MariaDB.
- HTTPS-capable web server configured to serve the Laravel `public` directory.
- SMTP credentials only if email notifications are desired.
- Node.js/npm are not required on the production server.

## Initial deployment

1. Create a MySQL/MariaDB database and a dedicated database user with only the privileges needed by the application.
2. Upload the application outside the publicly served directory where possible. Set the website document root to the application's `public` directory. Never point the web root at the project root.
3. Install dependencies from the project directory:

   ```sh
   composer install --no-dev --optimize-autoloader
   ```

   There is no `npm install`, `npm run build`, Vite manifest, Node runtime, or frontend build step in the production deployment path. The Blade pages link directly to the existing files in `public/css` and `public/js`; these are served as static assets by the web server.

4. Create a private `.env` from `.env.example`. Configure at least:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://dubainotaryservice.ae
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

   Generate `APP_KEY` with `php artisan key:generate` in the deployed environment. Keep `.env` out of source control and web-accessible paths.

5. The supplied business defaults are Al Syed Documents Clearing Services, +971 55 625 1709, and Almas Tower - Dubai Lake Avenue - Street 66 - Dubai - United Arab Emirates. Verify these details before launch; update them in `.env` or at `/admin/settings` if they change. Configure SMTP separately for inquiry notifications.
6. Run migrations:

   ```sh
   php artisan migrate --force
   ```

   Run `php artisan db:seed --force` only when the starter categories are wanted. The sample service remains unpublished.

7. Create an administrator through a trusted SSH/CLI session:

   ```sh
   php artisan notary:admin
   ```

   Enter a unique email and a confirmed password of at least 12 characters at the prompts. Do not put an administrator password into a command argument or source file.

8. Create the public storage link and set permissions:

   ```sh
   php artisan storage:link
   ```

   The PHP worker needs write access to `storage` and `bootstrap/cache`. The public web server must not serve `.env`, `vendor`, or application source files.

9. Enable HTTPS and configure trusted proxies only for the actual proxy/load-balancer chain used by the host. Ensure session cookies are secure over HTTPS and review the host's PHP upload limits.
10. Warm production caches:

    ```sh
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

    No queue worker, Redis, Docker, Node process, PM2, or separate frontend/API server is required.

## Email and contact form

Set Laravel `MAIL_*` SMTP values and `SITE_INQUIRIES_EMAIL` before launch if inquiry notifications are required. Submit a real test inquiry after SMTP is configured. Contact submissions are stored in `contact_inquiries`; use the database backup process below to preserve them.

The contact endpoint uses CSRF validation, server-side validation, a honeypot field, and Laravel request throttling. A service-of-interest selector only lists currently published services; its service reference is nullable and is cleared if the service is later deleted. Submissions are stored in `contact_inquiries` before notification is attempted. Authenticated administrators can search the inbox at `/admin/inquiries`. If SMTP notification fails, the saved inquiry remains available and the sender is told that the email notification failed; the exception is logged. Confirm the host's trusted proxy settings so rate limiting sees the intended client addresses.

## Updating the application

1. Back up the database and uploaded images before deployment.
2. Put the site into maintenance mode if the hosting flow permits:

   ```sh
   php artisan down
   ```

3. Deploy the reviewed code and run `composer install --no-dev --optimize-autoloader`.
4. Run database migrations and rebuild caches:

   ```sh
   php artisan migrate --force
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. Verify home, contact, category/service pages, `/admin/login`, an admin edit/preview, `/sitemap.xml`, and `/robots.txt`.
6. Return the site to service:

   ```sh
   php artisan up
   ```

## Backup and restore

Create a consistent MySQL dump without embedding the password in shell history:

```sh
mysqldump --single-transaction -h DB_HOST -u DB_USER -p DB_NAME > backup.sql
```

The tool prompts for the database password. Back up `storage/app/public` separately because it contains service images. Keep backups encrypted and access-controlled.

Restore into the intended database only after verifying the target:

```sh
mysql -h DB_HOST -u DB_USER -p DB_NAME < backup.sql
```

Restore the matching `storage/app/public` files and verify image links. Periodically rehearse restore in a non-production environment. A database dump does not replace application code, `.env`, or uploaded-file backups.

## Launch and search visibility

- Check `APP_DEBUG=false`, HTTPS, canonical `APP_URL`, session security, database access, permissions, and SMTP.
- Replace all placeholder copy with approved company and service information. Do not publish sample content or unverified legal requirements.
- Verify business name, phone, WhatsApp, email, address, and hours in `/admin/settings`. The site deliberately starts without real contact information.
- Review title, meta description, Open Graph fields, canonical URL, publication state, and image alt text for every category and published service. Static public pages also provide page-specific descriptions and canonicals. Canonical URLs over 255 characters are rejected to match the database column.
- Inspect `/robots.txt` and submit `/sitemap.xml` to Google Search Console after confirming the canonical production domain. The sitemap contains public static pages, active categories, and published services; records with a custom canonical URL are omitted.
- Changing a service or category slug records its previous slug and permanently redirects requests for that old public URL to the current slug. Redirect aliases are retained while the content exists and are removed when that content is deleted.
- The contact form persists inquiries independently of email delivery. Configure SMTP and `SITE_INQUIRIES_EMAIL`, then test both form submission and notification delivery before launch.
- Administrators can review submitted inquiries at `/admin/inquiries`; access is protected by the same authenticated admin middleware. Apply the new `service_id` contact migration during update deploys before using the service selector.
- Confirm the production server runs the PHP application only; no Node.js process or frontend compilation is needed at runtime or deployment.
- Add analytics only after deciding on consent, privacy, and retention requirements. Contact-click events are emitted only if a Google Analytics `gtag` function is configured.
- Set up and verify any Google Business Profile independently. The application does not create or verify a profile and makes no ranking, indexing, eligibility, approval, or lead guarantees.
