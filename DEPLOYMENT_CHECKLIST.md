# cPanel Deployment Checklist

## Before You Start
- [ ] All files uploaded to cPanel
- [ ] Have cPanel login credentials ready
- [ ] Domain admin.executiveairportcars.com is registered and pointing to your server

## Step 1: Database Setup (5 minutes)
- [ ] Create MySQL database in cPanel
- [ ] Create database user
- [ ] Add user to database with ALL PRIVILEGES
- [ ] Note down: DB name, DB user, DB password

## Step 2: Configure .env File (2 minutes)
- [ ] Update DB_DATABASE with your database name
- [ ] Update DB_USERNAME with your database user
- [ ] Update DB_PASSWORD with your database password
- [ ] Verify APP_URL=https://admin.executiveairportcars.com
- [ ] Verify APP_ENV=production
- [ ] Verify APP_DEBUG=false

## Step 3: Domain Setup in cPanel (3 minutes)
- [ ] Go to Subdomains or Addon Domains
- [ ] Add admin.executiveairportcars.com
- [ ] Set Document Root to: /path/to/your/public_html/admin.executiveairportcars.com/public
- [ ] IMPORTANT: Document root must end with /public

## Step 4: Set Permissions (2 minutes)
- [ ] Set storage folder permissions to 775
- [ ] Set bootstrap/cache folder permissions to 775

## Step 5: Run Migrations (5 minutes)

### Option A: Using Terminal/SSH
```bash
cd /home/username/public_html/admin.executiveairportcars.com
php artisan migrate --force
php artisan db:seed --force
```

### Option B: Using migrate.php Script
- [ ] Edit migrate.php and change MIGRATION_PASSWORD
- [ ] Upload migrate.php to root folder
- [ ] Visit: https://admin.executiveairportcars.com/migrate.php?password=your_password
- [ ] Wait for completion
- [ ] DELETE migrate.php immediately

## Step 6: Clear Caches (2 minutes)
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

## Step 7: SSL Certificate (5 minutes)
- [ ] Go to SSL/TLS Status in cPanel
- [ ] Run AutoSSL for admin.executiveairportcars.com
- [ ] Wait for certificate to install
- [ ] Verify HTTPS works

## Step 8: Final Testing (10 minutes)
- [ ] Visit https://admin.executiveairportcars.com
- [ ] Login page loads correctly
- [ ] Try logging in
- [ ] Check all pages load
- [ ] Verify CSS/JS loads correctly
- [ ] Test broadcast messages
- [ ] Test all main features

## Security Final Check
- [ ] APP_DEBUG=false in .env
- [ ] Delete migrate.php (if used)
- [ ] Delete any test files
- [ ] Storage permissions are 775 (NOT 777)
- [ ] .env file is not publicly accessible

## Post-Launch
- [ ] Set up automated backups in cPanel
- [ ] Add to monitoring/uptime service
- [ ] Document admin credentials securely
- [ ] Test database backups work

---

## Quick Troubleshooting

**500 Error:**
- Check storage permissions (775)
- Verify .env database credentials
- Check cPanel error logs

**404 Error:**
- Verify document root points to /public folder
- Check .htaccess files exist

**Blank Page:**
- Temporarily set APP_DEBUG=true
- Check PHP version (must be 8.1+)
- Check error logs

**CSS Not Loading:**
- Clear browser cache
- Verify APP_URL in .env
- Run: php artisan storage:link

---

## Contact Info
- Server: cPanel at executiveairportcars.com
- Domain: https://admin.executiveairportcars.com
- Support: Contact your hosting provider for server issues

## Estimated Total Time: 30-45 minutes
