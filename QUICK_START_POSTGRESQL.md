# Quick Start: Deploy to Render with PostgreSQL

## 🚀 5-Minute Setup Guide

### Step 1: Create PostgreSQL Database (2 minutes)

1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click **New +** → **PostgreSQL**
3. Fill in:
   - **Name:** `nantosha-db`
   - **Database:** `nantosha`
   - **Region:** Choose one (remember it!)
   - **Plan:** Free
4. Click **Create Database**
5. **Copy the Internal Database URL** (looks like: `postgresql://user:pass@host:5432/dbname`)

### Step 2: Create Web Service (3 minutes)

1. In Render Dashboard, click **New +** → **Web Service**
2. Connect your GitHub repository
3. Configure:
   - **Name:** `nantosha`
   - **Region:** **SAME as your PostgreSQL database** ⚠️
   - **Environment:** `PHP`
   - **Build Command:**
     ```bash
     composer install --no-dev --optimize-autoloader --no-interaction && npm ci && npm run build && php artisan key:generate --force && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan storage:link
     ```
   - **Start Command:**
     ```bash
     php artisan serve --host=0.0.0.0 --port=$PORT
     ```

### Step 3: Set Environment Variables

In your Web Service settings → **Environment** tab, add:

```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_URL=paste-your-internal-database-url-here
CLOUDINARY_URL=your-cloudinary-url
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error
```

**That's it!** Click **Save Changes** and Render will deploy your app.

## ✅ What Works Automatically

- ✅ All migrations are PostgreSQL compatible
- ✅ No code changes needed
- ✅ Laravel handles database differences automatically
- ✅ Free tier available for both database and web service

## 🔍 Verify It Works

1. Wait for deployment to complete (2-5 minutes)
2. Visit your app URL (provided by Render)
3. Test:
   - Homepage loads
   - Can register/login
   - Can view products
   - Can add to cart

## 📝 Important Notes

1. **Same Region:** Web service and database must be in the same region for internal connection
2. **DB_URL:** Use the Internal Database URL from Render (faster and more secure)
3. **First Deploy:** Migrations run automatically on first deploy
4. **APP_KEY:** Generated automatically in build command

## 🆘 Troubleshooting

**Database connection error?**
- Check DB_URL is correct
- Verify web service and database are in same region
- Check Render logs for specific error

**Build fails?**
- Check build logs in Render dashboard
- Verify all environment variables are set
- Check for any missing dependencies

## 📚 Full Guide

See `RENDER_POSTGRESQL_DEPLOYMENT.md` for detailed instructions.

