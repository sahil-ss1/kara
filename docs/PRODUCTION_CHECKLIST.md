# Production Deployment Checklist

Use this checklist to ensure your Kara deployment is production-ready.

## Pre-Deployment Checklist

### Code Quality
- [ ] All debug code removed or wrapped in environment checks
- [ ] No `var_dump()`, `dd()`, or `dump()` statements in code
- [ ] No commented-out debug code
- [ ] Error handling properly implemented
- [ ] Sensitive data not exposed in error messages

### Configuration
- [ ] `.env.example` file created with all required variables
- [ ] Production `.env` file configured with:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` set to production domain
  - [ ] Database credentials configured
  - [ ] OAuth credentials (HubSpot, Google) configured
  - [ ] Mail configuration set up
  - [ ] Cache driver configured (Redis recommended)
  - [ ] Queue driver configured (database or Redis)
  - [ ] Session driver configured
  - [ ] `LOG_LEVEL` set appropriately (error or warning)

### Security
- [ ] SSL certificate installed and valid
- [ ] HTTPS enforced (HTTP redirects to HTTPS)
- [ ] Strong database passwords set
- [ ] OAuth redirect URIs match production URLs exactly
- [ ] File permissions set correctly:
  - [ ] Storage directories: 775
  - [ ] Bootstrap cache: 775
  - [ ] Files: 644
- [ ] `.env` file not accessible via web
- [ ] Rate limiting configured for API endpoints
- [ ] CSRF protection enabled

### Database
- [ ] Database created and accessible
- [ ] Migrations ready (tested in staging)
- [ ] Database seeder does NOT delete production data
- [ ] Backup strategy in place

### Docker (if using)
- [ ] Dockerfile uses PHP 8.2+
- [ ] Docker compose uses environment variables (not hardcoded credentials)
- [ ] Production optimizations added to Dockerfile
- [ ] `composer install --no-dev` in Dockerfile

## Deployment Steps

### 1. Code Deployment
- [ ] Code pulled/cloned to production server
- [ ] Dependencies installed: `composer install --no-dev --optimize-autoloader`
- [ ] Frontend assets built: `npm run build`
- [ ] `.env` file created and configured

### 2. Database Setup
- [ ] Database migrations run: `php artisan migrate --force`
- [ ] **DO NOT** run `migrate:fresh --seed` in production
- [ ] Database backup taken before migrations

### 3. Application Setup
- [ ] Application key generated: `php artisan key:generate`
- [ ] Storage link created: `php artisan storage:link`
- [ ] Storage directories created with correct permissions
- [ ] Avatars directory created: `mkdir -p storage/app/public/avatars`

### 4. Production Optimizations
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views cached: `php artisan view:cache`
- [ ] Application optimized: `php artisan optimize`

### 5. Queue Worker Setup
- [ ] Queue worker configured (Supervisor or systemd)
- [ ] Queue worker started and running
- [ ] Failed jobs table exists

### 6. Web Server Configuration
- [ ] Nginx/Apache configured
- [ ] SSL certificate installed
- [ ] Document root set to `public` directory
- [ ] PHP-FPM configured and running

## Post-Deployment Verification

### Health Checks
- [ ] Health endpoint accessible: `curl https://your-domain.com/health`
- [ ] Health endpoint returns `"status": "ok"`
- [ ] Database connectivity verified
- [ ] Cache connectivity verified
- [ ] Storage writability verified

### Functionality Tests
- [ ] User can log in
- [ ] HubSpot OAuth connection works
- [ ] Google Calendar OAuth connection works
- [ ] Data synchronization works
- [ ] AI briefing generation works
- [ ] File uploads work (avatars)
- [ ] All critical user flows tested

### Performance Checks
- [ ] Page load times acceptable
- [ ] API response times acceptable
- [ ] Database queries optimized
- [ ] Cache working properly

### Monitoring Setup
- [ ] Application logs configured and rotating
- [ ] Error tracking set up (if using service like Sentry)
- [ ] Uptime monitoring configured
- [ ] Health check endpoint monitored
- [ ] Database monitoring configured

### Security Verification
- [ ] No debug information exposed
- [ ] Error pages don't show stack traces
- [ ] Sensitive data not logged
- [ ] Rate limiting working
- [ ] CSRF protection working
- [ ] SSL certificate valid and not expiring soon

## Ongoing Maintenance

### Regular Tasks
- [ ] Monitor application logs daily
- [ ] Review error logs weekly
- [ ] Check disk space usage
- [ ] Verify backups are running
- [ ] Update dependencies monthly (test in staging first)
- [ ] Review security updates

### Backup Verification
- [ ] Database backups running
- [ ] File backups running
- [ ] Backup restoration tested
- [ ] Backup retention policy in place

### Performance Monitoring
- [ ] Monitor response times
- [ ] Monitor database performance
- [ ] Monitor queue processing
- [ ] Monitor API usage (HubSpot, Google, Groq)

## Rollback Plan

If deployment fails:
- [ ] Previous code version available
- [ ] Database backup available
- [ ] Rollback procedure documented
- [ ] Team knows rollback steps

## Documentation

- [ ] Deployment guide created (`docs/DEPLOYMENT.md`)
- [ ] Production checklist created (this file)
- [ ] README updated with production instructions
- [ ] Environment variables documented
- [ ] Troubleshooting guide available

## Sign-Off

- [ ] All pre-deployment checks completed
- [ ] Deployment steps completed
- [ ] Post-deployment verification passed
- [ ] Monitoring set up
- [ ] Team notified of deployment

**Deployed by:** _________________  
**Date:** _________________  
**Environment:** Production  
**Version:** _________________
