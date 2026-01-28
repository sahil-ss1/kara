<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Requirements

- **PHP**: ^8.2 (Laravel 12 requirement)
- **Laravel**: ^12.0
- **Livewire**: ^3.0 (stable)
- **Node.js**: Latest LTS version
- **Composer**: Latest version

## Installation

Installation Steps:

- git clone https://github.com/creativagr/kara.git
- composer install
- npm install
- copy .env.example .env
- create DB
- php artisan key:generate
- php artisan storage:link
- php artisan migrate:fresh --seed
- mkdir storage/app/public/avatars
- php artisan config:cache
- npm run build (for production) or npm run dev (for development)

## Upgrade Notes (Laravel 12 & Livewire 3 Stable)

This project has been upgraded from Laravel 10 to Laravel 12 and Livewire 3 beta to stable. Key changes:

- **PHP Requirement**: Upgraded from PHP 8.1 to PHP 8.2+
- **Laravel Framework**: Upgraded to Laravel 12
- **Livewire**: Migrated from beta to stable version 3.0
- **Vite**: Upgraded from v3 to v5
- **Component Updates**: Migrated deprecated `$listeners` to `#[On()]` attributes

### Breaking Changes

- PHP 8.2+ is now required
- Some third-party packages may need updates - verify compatibility during `composer update`
- Axios upgraded from 0.27 to 1.7 (major version change)

### Testing

After upgrading dependencies, run:
- `php artisan test` - Run automated test suite
- Manual testing of all Livewire components (Dashboard, Goals, Tasks, Notifications)
- Verify HubSpot and Google Calendar integrations

## Production Deployment

### Environment Variables

Before deploying to production, ensure all required environment variables are set in your `.env` file. See `.env.example` for a complete list of required variables.

**Critical Production Settings:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `LOG_LEVEL=error` (or `warning` for more verbose logging)
- `QUEUE_CONNECTION=database` or `redis` (recommended for production)

**Required API Credentials:**
- `HUBSPOT_CLIENT_ID` and `HUBSPOT_CLIENT_SECRET`
- `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`
- `GROQ_API_KEY` (for AI features)

### Production Optimizations

After deployment, run these commands to optimize performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
composer install --no-dev --optimize-autoloader
npm run build
```

### Health Check Endpoint

The application includes a health check endpoint at `/health` for monitoring:

```bash
curl https://your-domain.com/health
```

Returns JSON with status of:
- Database connectivity
- Cache connectivity
- Storage writability

### Docker Deployment

See `docs/DEPLOYMENT.md` for detailed Docker deployment instructions.

## SSL

SSL is required for HubSpot OAuth login. The application must be served over HTTPS.

### Local Development (XAMPP)

For local development with XAMPP:

1. Generate SSL certificate using OpenSSL or use a tool like `mkcert`
2. Configure Apache to use SSL on port 443
3. Update `APP_URL` in `.env` to use `https://localhost` or your local domain
4. Add certificate exception in your browser

### Production

For production:
- Use a valid SSL certificate (Let's Encrypt recommended)
- Configure your web server (Nginx/Apache) to serve HTTPS
- Ensure all HTTP traffic redirects to HTTPS
- Update `APP_URL` in `.env` to your production HTTPS URL

## Environment Variables

Key environment variables (see `.env.example` for complete list):

**Application:**
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local, staging, production)
- `APP_DEBUG` - Debug mode (false for production)
- `APP_URL` - Application URL

**Database:**
- `DB_CONNECTION` - Database driver (mysql, pgsql, sqlite)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**OAuth:**
- `HUBSPOT_CLIENT_ID`, `HUBSPOT_CLIENT_SECRET`, `HUBSPOT_REDIRECT_URI`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`

**AI Service:**
- `GROQ_API_KEY` - Groq API key for AI features

**Mail:**
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`

**Cache/Queue:**
- `CACHE_DRIVER` - Cache driver (file, redis, memcached)
- `QUEUE_CONNECTION` - Queue driver (sync, database, redis)
- `SESSION_DRIVER` - Session driver (file, database, redis)




## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
