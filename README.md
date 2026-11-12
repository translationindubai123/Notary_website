# Al Syed Documents Clearing Services website

Server-rendered Laravel and Blade website for publishing notary service information and receiving inquiries. The service catalogue is database-backed; the repository does not hardcode the full service list. Public service content is escaped as plain text. Demo content is unpublished by default and must be reviewed before publication.

## Runtime

- Framework: Laravel 13.x
- PHP: 8.3 or later.
- Database: MySQL/MariaDB in production; SQLite is configured for automated tests.
- Frontend: Blade, Bootstrap 5.3 CSS via CDN, local CSS and small vanilla JavaScript files served directly from `public/`.
- No frontend build step, Node process, React, Vue, Inertia, or Livewire is required.

See [DEPLOYMENT.md](DEPLOYMENT.md) for the PHP hosting, production configuration, update, backup/restore, and launch procedures.

## Local setup

1. Install the PHP extensions required by Laravel (including PDO, `pdo_mysql` or `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, and XML support).
2. Copy `.env.example` to `.env`, set `APP_URL`, and configure MySQL credentials or use SQLite for local development.
3. For SQLite, create `database/database.sqlite` (PowerShell: `New-Item database\database.sqlite -ItemType File`) and set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite` in `.env`.
4. Install PHP dependencies: `composer install`.
5. Generate the application key: `php artisan key:generate`.
6. Run migrations and optional demonstration content: `php artisan migrate --seed`.
7. Create an admin interactively: `php artisan notary:admin`. The command prompts for the name, email, and a confirmed password (minimum 12 characters); the password is not passed on the command line. There is no public registration route.
8. For uploaded service images, run `php artisan storage:link`.
9. Start local PHP serving with `php artisan serve` and open the printed URL. Frontend files are already in `public/`; Vite is not needed to run the website.

The default seeder creates the initial service categories and two clearly marked, unpublished sample services, with placeholder document/process/FAQ/related-service data. It does not publish sample legal guidance. The seeder is safe to run more than once.

## Content administration

- Sign in at `/admin/login`.
- Review submitted messages under Inquiries; this area requires the same authenticated admin session.
- Use Categories to create, nest, sort, activate, or update category SEO. A category cannot be deleted while it contains services or child categories.
- Use Services to create and edit service pages, structured documents, process steps, FAQs, related-service links, image alt text, SEO fields, and publication state.
- Active content with a publication time in the future is not public. Only published services appear in public listings and the sitemap.
- Service body fields are plain text and are escaped in public HTML. Do not paste HTML or treat content as legal advice without appropriate review.
- Search the public catalogue at `/notary-services`; administrators can also search and filter records from the dashboard.
- Enter the remaining catalogue through the admin service editor. CSV import is not implemented.

## Configuration

Set these values in the private `.env` file; actual business contact information is intentionally not supplied:

- `SITE_NAME`, `SITE_DESCRIPTION`
- `SITE_PHONE`, `SITE_WHATSAPP`, `SITE_EMAIL`, `SITE_INQUIRIES_EMAIL`
- `SITE_ADDRESS`, `SITE_OFFICE_HOURS`
- `MOJ_PAGE_TITLE`, `MOJ_PAGE_INTRODUCTION`

These environment values are initial defaults; administrators can manage the business details and About/MOJ page copy at `/admin/settings`.

Set `APP_URL` to the canonical HTTPS domain. Configure SMTP with Laravel's `MAIL_*` settings and set `SITE_INQUIRIES_EMAIL` to receive contact notifications. Inquiries are stored before notification is attempted and are available to signed-in administrators under `/admin/inquiries`. The contact form optionally links the inquiry to a currently published service. If notification delivery fails, the sender is informed and the exception is logged; ensure SMTP is tested before launch.

## Deployment on PHP hosting

1. Select hosting compatible with the installed Laravel line and required PHP extensions. Use a supported PHP release that is compatible with the chosen Laravel version; do not assume that a PHP upgrade alone upgrades Laravel.
2. Create a MySQL/MariaDB database and a restricted database user.
3. Upload the application and install production PHP dependencies with `composer install --no-dev --optimize-autoloader`.
4. Create a private `.env` from `.env.example`. Set production `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, HTTPS `APP_URL`, database credentials, contact settings, and SMTP credentials. Never publish `.env`.
5. Configure the web server document root to the application's `public` directory. Do not expose the project root or `.env`.
6. Run `php artisan migrate --force`. Run `php artisan db:seed --force` only if the sample categories are wanted; sample service records remain unpublished.
7. Run `php artisan notary:admin` through a secure CLI/SSH session to create the first administrator. Do not create administrator accounts through public registration.
8. Run `php artisan storage:link`. Ensure `storage` and `bootstrap/cache` are writable by the PHP process. Restrict uploads to the validated image formats configured in the admin form.
9. Enable HTTPS. Configure trusted proxies only for the actual hosting proxy chain. Ensure secure cookies/session configuration is appropriate for production.
10. Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`. Re-run relevant cache commands after configuration or code updates. No queue worker is required by this implementation.
11. Back up the database and `storage/app/public` before releases. For MySQL, use `mysqldump --single-transaction -h DB_HOST -u DB_USER -p DB_NAME > backup.sql`; restore with `mysql -h DB_HOST -u DB_USER -p DB_NAME < backup.sql`. Archive `storage/app/public` separately and test restoring both backups in a non-production environment.
12. For updates, back up first, deploy code and Composer dependencies, run migrations, clear/rebuild caches, and verify public pages, admin sign-in, contact submission, and sitemap before closing the maintenance window.

## SEO and launch checklist

- Review each service and category's publication status, canonical URL, title, meta description, and image alternative text.
- Verify business identity and contact information before launch; do not add unverified addresses, approvals, ratings, or claims.
- The XML sitemap is available at `/sitemap.xml`; it includes active categories and published services. `/robots.txt` disallows admin routes and points to the configured sitemap URL.
- Service and category slug changes automatically redirect the old public URL to the current one while the content remains in the database.
- Confirm the production canonical domain and HTTPS redirects, then submit the sitemap in Google Search Console.
- Configure Google Analytics only if desired and after selecting a privacy-compliant consent and retention approach. The static JavaScript reports phone/WhatsApp `contact_click` events only when a `gtag` function is present; analytics credentials are not required by the website.
- Set up and verify Google Business Profile separately; this website does not create or verify a profile or guarantee ranking, eligibility, approval, indexing, or leads.

Production deployment does not require Node.js/npm or a frontend build. The website serves its committed local CSS and vanilla JavaScript directly from `public/`; use only the PHP/Composer deployment steps above.

## Testing

Run the feature and unit suite with `php artisan test`. PHPUnit uses in-memory SQLite and covers public pages, search, category/service publication behavior, session-protected administration and inquiry review, repeatable service content, contact submissions, duplicate slugs, and sitemap filtering.
