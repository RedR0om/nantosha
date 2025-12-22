# Render Deployment with PostgreSQL Guide

This guide will help you deploy the Nantosha Laravel application to Render using PostgreSQL (free tier available).

## Why PostgreSQL on Render?

- ✅ **Free tier available** - Perfect for getting started
- ✅ **No remote connection issues** - Works seamlessly with Render
- ✅ **Automatic backups** - Built-in backup system
- ✅ **Easy scaling** - Upgrade when needed
- ✅ **Better than cPanel MySQL** - No remote connection configuration needed

## Step 1: Create PostgreSQL Database on Render

### 1.1 Create Database

1. Log into [Render Dashboard](https://dashboard.render.com)
2. Click **New +** → **PostgreSQL**
3. Configure:
   - **Name:** `nantosha-db` (or your preferred name)
   - **Database:** `nantosha` (or your preferred name)
   - **User:** Auto-generated (or custom)
   - **Region:** Choose closest to your web service
   - **Plan:** Free (or paid for production)
4. Click **Create Database**

### 1.2 Get Connection Details

After creation, Render provides:
- **Internal Database URL** (for services in same region)
- **External Database URL** (for external connections)
- **Host, Port, Database, Username, Password**

**Note:** Use the **Internal Database URL** if your web service is in the same region (recommended).

## Step 2: Update Database Configuration

### 2.1 Change Default Database Connection

Update `config/database.php`:

```php
'default' => env('DB_CONNECTION', 'pgsql'),
```

### 2.2 PostgreSQL Configuration

The PostgreSQL configuration in `config/database.php` is already set up correctly:

```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8'),
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],
```

## Step 3: Deploy Web Service to Render

### 3.1 Connect GitHub Repository

1. In Render Dashboard, click **New +** → **Web Service**
2. Connect your GitHub repository
3. Select the repository containing this Laravel app

### 3.2 Configure Build Settings

- **Name:** `nantosha` (or your preferred name)
- **Region:** **Same region as your PostgreSQL database** (important for internal connection)
- **Branch:** `main` (or your main branch)
- **Root Directory:** Leave empty
- **Environment:** `PHP`
- **Build Command:**
  ```bash
  composer install --no-dev --optimize-autoloader --no-interaction && npm ci && npm run build && php artisan key:generate --force && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan storage:link
  ```
- **Start Command:**
  ```bash
  php artisan serve --host=0.0.0.0 --port=$PORT
  ```

### 3.3 Set Environment Variables

In the Render dashboard, add these environment variables:

#### Required Variables:

```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=your-generated-app-key
APP_URL=https://your-app-name.onrender.com
APP_TIMEZONE=UTC

# PostgreSQL Configuration
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host-from-render
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

# OR use the full database URL (easier)
DB_URL=postgresql://username:password@host:5432/database_name

CLOUDINARY_URL=your-cloudinary-url

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error
```

#### Using Database URL (Recommended)

Instead of individual DB_* variables, you can use the full URL from Render:

```bash
DB_URL=postgresql://nantosha_user:password@dpg-xxxxx-a.oregon-postgres.render.com:5432/nantosha_db
```

Render provides this URL in the database dashboard. Just copy and paste it!

#### Generate APP_KEY:

**Option 1:** Let Render generate it (add to build command):
```bash
php artisan key:generate --force
```

**Option 2:** Generate locally and set manually:
```bash
php artisan key:generate --show
```
Then copy the key to `APP_KEY` in Render.

### 3.4 Link Database to Web Service

1. In your Web Service settings
2. Go to **Environment** tab
3. Find **Add Environment Variable**
4. Click **Link Database** (if available)
5. Select your PostgreSQL database
6. Render will automatically add `DATABASE_URL` environment variable

## Step 4: Migration Compatibility

### 4.1 Laravel Handles Most Differences

Laravel's Schema Builder automatically handles PostgreSQL differences:
- ✅ `unsignedInteger` → converted to regular `integer`
- ✅ `json` columns → work the same
- ✅ Foreign keys → work the same
- ✅ Indexes → work the same

### 4.2 No Code Changes Needed

Your migrations are already compatible! Laravel abstracts database differences.

## Step 5: Post-Deployment

### 5.1 Verify Database Connection

1. Check Render logs for any database errors
2. Test your application
3. Verify data is being saved

### 5.2 Run Seeders (if needed)

If you have seeders, run them via Render Shell:

```bash
php artisan db:seed --force
```

### 5.3 Test Key Features

- ✅ User registration/login
- ✅ Product creation
- ✅ Cart functionality
- ✅ Order processing
- ✅ Cloudinary uploads

## Step 6: Environment Variables Summary

Here's a complete list of environment variables you'll need:

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://your-app-name.onrender.com
APP_TIMEZONE=UTC

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_URL=postgresql://user:pass@host:5432/dbname
# OR individual variables:
# DB_HOST=your-postgres-host
# DB_PORT=5432
# DB_DATABASE=your-database
# DB_USERNAME=your-username
# DB_PASSWORD=your-password

# Cloudinary
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Logging
LOG_LEVEL=error
LOG_CHANNEL=stack
```

## Troubleshooting

### Database Connection Issues

**Error: "Connection refused"**
- Verify database is in same region as web service
- Check DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Try using DB_URL instead of individual variables

**Error: "Password authentication failed"**
- Verify password in Render database dashboard
- Check for special characters that need URL encoding in DB_URL

**Error: "Database does not exist"**
- Verify database name matches
- Check database is created and active in Render

### Migration Issues

**Error: "Table already exists"**
- Database might have existing tables
- Run: `php artisan migrate:fresh --force` (⚠️ deletes all data)
- Or: `php artisan migrate:status` to check migration status

**Error: "Column type mismatch"**
- Rare, but can happen
- Check migration files for any MySQL-specific syntax
- Laravel should handle most conversions automatically

### Build Failures

**Error: "APP_KEY not set"**
- Add `php artisan key:generate --force` to build command
- Or set APP_KEY manually in environment variables

**Error: "npm build fails"**
- Check Node.js version compatibility
- Verify all dependencies in package.json
- Check build logs for specific errors

## Advantages of PostgreSQL on Render

1. **No Remote Connection Issues** - Database is in same network
2. **Automatic Backups** - Render handles backups automatically
3. **Easy Scaling** - Upgrade database plan when needed
4. **Better Performance** - Internal network connection is faster
5. **Free Tier Available** - Perfect for development and small projects

## Migration from MySQL to PostgreSQL

If you have existing data in MySQL:

1. **Export data from MySQL:**
   ```bash
   mysqldump -u username -p database_name > backup.sql
   ```

2. **Import to PostgreSQL:**
   - Use a tool like `pgloader` or manual conversion
   - Or start fresh with migrations (if acceptable)

3. **Test thoroughly** after migration

## Next Steps

1. ✅ Deploy to Render
2. ✅ Test all functionality
3. ✅ Set up custom domain (optional)
4. ✅ Configure SSL (automatic on Render)
5. ✅ Monitor logs and performance

## Support

For issues:
1. Check Render logs
2. Check Laravel logs (if accessible)
3. Verify all environment variables
4. Test database connection using Render Shell

