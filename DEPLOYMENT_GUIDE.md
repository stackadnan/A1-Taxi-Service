# Laravel cPanel Deployment Guide for admin.executiveairportcars.com

## Prerequisites Completed ✓
- Laravel application files uploaded to cPanel
- .env file configured for production
- Root .htaccess created to redirect to public folder

## Step-by-Step Deployment Instructions

### 1. Database Setup (IMPORTANT - Do This First!)

In cPanel:
1. Go to **MySQL® Databases**
2. Create a new database (e.g., `execairp_airport_services`)
3. Create a new database user with a strong password
4. Add the user to the database with ALL PRIVILEGES
5. Note down:
   - Database name
   - Database username
   - Database password

### 2. Update .env File with Database Credentials

Edit `.env` file and update these lines with your actual cPanel database details:
```
DB_DATABASE=execairp_airport_services  (your actual database name)
DB_USERNAME=execairp_dbuser           (your actual database user)
DB_PASSWORD=YourStrongPassword123     (your actual database password)
```

### 3. Domain Configuration

**Option A: If admin.executiveairportcars.com is a subdomain:**
1. In cPanel, go to **Subdomains**
2. Create subdomain: `admin`
3. Set Document Root to: `/home/your_username/public_html/admin.executiveairportcars.com/public`
4. Click Create

**Option B: If admin.executiveairportcars.com is an addon domain:**
1. In cPanel, go to **Addon Domains**
2. New Domain Name: `admin.executiveairportcars.com`
3. Document Root: `/home/your_username/public_html/admin.executiveairportcars.com/public`
4. Click "Add Domain"

**CRITICAL:** The document root MUST point to the `public` folder, not the root folder!

### 4. File Permissions

Set the correct permissions via cPanel File Manager or SSH:
```bash
# Storage and bootstrap/cache folders need to be writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

In File Manager:
- Right-click on `storage` folder → Change Permissions → 775
- Right-click on `bootstrap/cache` folder → Change Permissions → 775

### 5. Run Database Migrations

Via cPanel Terminal (if available) or SSH:
```bash
cd /home/your_username/public_html/admin.executiveairportcars.com
php artisan migrate --force
php artisan db:seed --force  (if you want to seed data)
```

If Terminal is not available, you can use a temporary migration script:
- Upload `migrate.php` (see below) to your root folder
- Visit: https://admin.executiveairportcars.com/migrate.php
- DELETE the file after migration is complete!

### 6. Clear and Optimize Cache

Via Terminal/SSH:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. SSL Certificate (HTTPS)

In cPanel:
1. Go to **SSL/TLS Status**
2. Find `admin.executiveairportcars.com`
3. Click "Run AutoSSL" (if available)
4. Or use **Let's Encrypt** if your host supports it

### 8. Final Verification

Visit: https://admin.executiveairportcars.com

You should see your application's login page.

## Troubleshooting

### Issue: 500 Internal Server Error
- Check `.env` file exists and is readable
- Verify database credentials are correct
- Check storage folder permissions (775)
- Check error logs in cPanel → Error Logs

### Issue: 404 Not Found
- Verify document root points to `/public` folder
- Check `.htaccess` exists in both root and public folders
- Ensure mod_rewrite is enabled (contact host if not)

### Issue: Blank White Page
- Set `APP_DEBUG=true` temporarily in `.env` to see errors
- Check PHP version (must be 8.1 or higher)
- Review error logs

### Issue: CSS/JS Not Loading
- Run `php artisan storage:link`
- Check `APP_URL` in `.env` matches your domain exactly
- Clear browser cache

## Security Checklist

✓ APP_ENV=production
✓ APP_DEBUG=false
✓ Strong database password
✓ APP_KEY is set (unique encryption key)
✓ SSL certificate installed
✓ Remove any test/temporary files
✓ Secure file permissions (never 777)

## Directory Structure on Server

```
/home/your_username/public_html/admin.executiveairportcars.com/
├── .htaccess              (redirects to public/)
├── .env                   (production config)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                ← Domain points HERE
│   ├── .htaccess
│   ├── index.php
│   └── ...
├── resources/
├── routes/
├── storage/              (must be writable)
└── vendor/
```

## Post-Deployment

1. **Test all features thoroughly**
2. **Remove .env.example** (security)
3. **Set up automated backups** in cPanel
4. **Monitor error logs** regularly
5. **Keep Laravel and dependencies updated**

## Need Help?

- Check Laravel logs: `storage/logs/laravel.log`
- Check cPanel error logs
- Verify PHP version: `php -v` (must be ≥ 8.1)
- Contact your hosting provider for server-specific issues

---

**Important Files Modified:**
- `.env` - Updated for production environment
- `.htaccess` (root) - Created to redirect to public folder
- `public/.htaccess` - Already configured correctly

**Next Immediate Actions:**
1. Update database credentials in `.env`
2. Configure domain/subdomain in cPanel
3. Run migrations
4. Test the site
