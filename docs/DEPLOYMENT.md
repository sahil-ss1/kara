# Production Deployment Guide

This guide covers deploying Kara to production using Docker or traditional server setup.

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js (LTS version)
- MySQL/MariaDB or PostgreSQL
- Web server (Nginx or Apache)
- SSL certificate (required for HubSpot OAuth)

## Docker Deployment

### 1. Environment Setup

Create a `.env` file. If `.env.example` exists, copy it:

```bash
cp .env.example .env
```

**Note:** If `.env.example` doesn't exist, create `.env` manually with the following required variables (see README.md for complete list):
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- Database credentials (`DB_*`)
- OAuth credentials (`HUBSPOT_*`, `GOOGLE_*`)
- `GROQ_API_KEY`
- Mail configuration (`MAIL_*`)
- Cache/Queue/Session drivers

Configure all required environment variables, especially:
- `APP_ENV=production`
- `APP_DEBUG=false`
- Database credentials
- OAuth credentials (HubSpot, Google)
- Mail configuration

### 2. Database Configuration

Update `docker-deploy/docker-compose.yml` with your database credentials:

```yaml
environment:
  MYSQL_USER: ${MYSQL_USER:-your_user}
  MYSQL_PASSWORD: ${MYSQL_PASSWORD:-your_password}
  MYSQL_DATABASE: ${MYSQL_DATABASE:-kara}
  MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-your_root_password}
```

Or use environment variables from `.env` file.

### 3. Build and Start Containers

```bash
cd docker-deploy
docker-compose build --no-cache
docker-compose up -d
```

### 4. Run Migrations

```bash
docker-compose exec app php artisan migrate --force
```

**Note:** Do NOT run `migrate:fresh --seed` in production as it will delete all data.

### 5. Optimize for Production

```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec exec app php artisan view:cache
docker-compose exec app php artisan optimize
```

### 6. Set Permissions

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## Traditional Server Deployment

### 1. Server Requirements

- PHP 8.2+ with extensions: pdo_mysql, mbstring, zip, exif, pcntl, bcmath, gd
- Composer
- Node.js and npm
- MySQL/MariaDB or PostgreSQL
- Nginx or Apache with SSL

### 2. Clone and Install

```bash
git clone https://github.com/creativagr/kara.git
cd kara
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Configure database credentials
- Set OAuth credentials
- Configure mail settings

### 4. Database Setup

```bash
php artisan migrate --force
```

**Important:** Only run migrations, not seeders in production.

### 5. Storage and Permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
mkdir -p storage/app/public/avatars
chmod -R 775 storage/app/public/avatars
```

### 6. Production Optimizations

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 7. Queue Worker Setup

For production, use a queue worker:

```bash
php artisan queue:work --daemon
```

Or use Supervisor to manage the queue worker (see `docs/kara-worker.conf` for example).

### 8. Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /path/to/kara/public;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Example

Enable mod_rewrite and configure `.htaccess`:

```apache
<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /path/to/kara/public

    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key

    <Directory /path/to/kara/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Post-Deployment Verification

### 1. Health Check

```bash
curl https://your-domain.com/health
```

Should return JSON with status `"status": "ok"`.

### 2. Verify Environment

```bash
php artisan about
```

Check that:
- Environment shows `production`
- Debug mode is `false`
- All required extensions are installed

### 3. Test Critical Features

- User authentication
- HubSpot OAuth connection
- Google Calendar connection
- Data synchronization
- AI briefing generation

### 4. Monitor Logs

```bash
tail -f storage/logs/laravel.log
```

Check for any errors or warnings.

## Scheduled Tasks

Set up Laravel scheduler in cron:

```bash
* * * * * cd /path/to/kara && php artisan schedule:run >> /dev/null 2>&1
```

## Backup Strategy

1. **Database Backups:**
   - Use Laravel Backup package (already included)
   - Configure in `config/backup.php`
   - Set up automated daily backups

2. **File Backups:**
   - Backup `storage/app/public/avatars`
   - Backup uploaded files

3. **Configuration Backup:**
   - Backup `.env` file securely
   - Backup SSL certificates

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] SSL certificate installed and valid
- [ ] File permissions set correctly (775 for storage, 644 for files)
- [ ] `.env` file not accessible via web
- [ ] Rate limiting enabled
- [ ] CSRF protection enabled
- [ ] Regular security updates applied

## Monitoring

### Health Endpoint

Monitor `/health` endpoint:
- Database connectivity
- Cache connectivity
- Storage writability

### Log Monitoring

Set up log monitoring for:
- Error logs
- API failures
- Authentication issues
- Queue failures

### Performance Monitoring

Monitor:
- Response times
- Database query performance
- API call rates
- Queue processing times

## Troubleshooting

### Common Issues

1. **500 Error:**
   - Check `storage/logs/laravel.log`
   - Verify file permissions
   - Check `.env` configuration

2. **Database Connection Failed:**
   - Verify database credentials in `.env`
   - Check database server is running
   - Verify network connectivity

3. **OAuth Not Working:**
   - Verify SSL certificate is valid
   - Check OAuth redirect URIs match exactly
   - Verify OAuth credentials are correct

4. **Queue Not Processing:**
   - Ensure queue worker is running
   - Check queue connection in `.env`
   - Verify database queue table exists

## Rollback Procedure

If deployment fails:

1. Restore previous code version
2. Restore database backup if migrations failed
3. Clear caches: `php artisan cache:clear && php artisan config:clear`
4. Restart services

## Support

For deployment issues, check:
- Application logs: `storage/logs/laravel.log`
- Web server error logs
- Database logs
- System logs
