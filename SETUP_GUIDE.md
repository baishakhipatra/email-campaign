# SETUP GUIDE - Email Campaign Management System

## Quick Start (5 Minutes)

### Prerequisites
- PHP 8.2+ with common extensions (PDO, OpenSSL, etc)
- MySQL or MariaDB 8.0+
- Redis 6.0+
- Composer installed
- PHP extensions: php-mysql, php-redis, php-ctype, php-fileinfo

### Step 1: Clone/Extract Project
```bash
cd My_Project
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

**Windows tip:** If you get permission errors, use `composer install --no-scripts` then run separately.

### Step 3: Environment Setup
```bash
cp .env.example .env
```

Edit `.env` with your database credentials:
```env
DB_DATABASE=email_campaign_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

Generate application key:
```bash
php artisan key:generate
```

### Step 4: Database Setup
Create database:
```sql
CREATE DATABASE email_campaign_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations:
```bash
php artisan migrate
```

### Step 5: Admin Account
Create via tinker:
```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@localhost',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);

exit
```

### Step 6: Start Development Server
```bash
php artisan serve
```

Access: http://localhost:8000
- Email: admin@localhost
- Password: password

## Full Production Setup

### Infrastructure Required
- **Web Server:** Nginx or Apache with PHP-FPM
- **Database:** MySQL 8.0+
- **Message Queue:** Redis 6.0+
- **Monitoring:** Optional (New Relic, DataDog)

### Production Deployment Checklist

#### 1. Server Setup
```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install PHP 8.2+
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-redis \
  php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip

# 3. Install MySQL
sudo apt install -y mysql-server

# 4. Install Redis
sudo apt install -y redis-server

# 5. Install Nginx
sudo apt install -y nginx

# 6. Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

#### 2. Project Deployment
```bash
# Clone/upload project
git clone your-repo.git /var/www/email-campaign
# or upload via SFTP

cd /var/www/email-campaign

# Install dependencies
composer install --no-dev --optimize-autoloader

# Copy environment
cp .env.example .env
# Edit .env with production values

# Generate key
php artisan key:generate

# Migrate database
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

#### 3. Nginx Configuration
Create `/etc/nginx/sites-available/email-campaign`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/email-campaign/public;
    index index.php index.html index.htm;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.{
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/email-campaign /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 4. SSL Certificate (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### 5. Queue Worker (Supervisor)
Install supervisor:
```bash
sudo apt install -y supervisor
```

Create `/etc/supervisor/conf.d/email-campaign.conf`:
```ini
[program:email-campaign-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/email-campaign/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=4
user=www-data
stopasgroup=true
stopwaitsecs=3600

[program:email-campaign-scheduler]
process_name=%(program_name)s
command=php /var/www/email-campaign/artisan schedule:run
autostart=true
autorestart=true
numprocs=1
user=www-data
stopasgroup=true
redirect_stderr=true
stdout_logfile=/var/log/email-campaign-scheduler.log
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start email-campaign-worker:*
sudo supervisorctl start email-campaign-scheduler
```

#### 6. Backups
```bash
# Database backup script
#!/bin/bash
BACKUP_DIR="/backups/email-campaign"
mkdir -p $BACKUP_DIR
mysqldump -u root -p$DB_PASSWORD email_campaign_db > \
  $BACKUP_DIR/db_$(date +%Y%m%d_%H%M%S).sql
# Compress old backups older than 7 days
find $BACKUP_DIR -type f -name "*.sql" -mtime +7 -exec gzip {} \;
```

Add to crontab:
```bash
0 2 * * * /path/to/backup.sh
```

#### 7. Monitoring
Check queue status:
```bash
php artisan queue:failed
php artisan queue:work redis --tries=5
```

Monitor supervisor:
```bash
sudo supervisorctl status
sudo tailf /var/log/supervisor/email-campaign-*.log
```

## Configuration Files

### Important Config Files

1. **config/emailcampaign.php** - Campaign-specific settings
   - Batch size, retry attempts, throttle rate

2. **config/queue.php** - Queue configuration
   - Redis connection settings

3. **config/cache.php** - Cache/Redis settings
   - Cache prefix, TTL

4. **config/mail.php** - Mail configuration
   - Can be overridden per SMTP setting in DB

## Testing

### Test Email Sending
```bash
# 1. Create test subscriber
php artisan tinker
App\Models\Subscriber::create([
    'email' => 'test@example.com',
    'name' => 'Test User'
]);
exit

# 2. Create test campaign
# Via admin panel: Create campaign with test subscriber

# 3. Monitor queue
php artisan queue:work redis

# 4. Check email logs
DB::table('email_logs')->latest()->first();
```

## Troubleshooting Guide

### Issue: "SQLSTATE[HY000]: General error: 1030 Got error..."
**Solution:** Increase MySQL max_allowed_packet
```bash
# Edit /etc/mysql/mysql.conf.d/mysqld.cnf
max_allowed_packet=256M
sudo systemctl restart mysql
```

### Issue: Redis connection refused
**Solution:** Ensure Redis is running
```bash
sudo systemctl start redis-server
redis-cli ping  # Should return PONG
```

### Issue: Queue jobs not processing
**Solution:** Check supervisor status
```bash
sudo supervisorctl status email-campaign-worker:*
sudo supervisorctl restart email-campaign-worker:*
```

### Issue: High memory usage
**Solution:** Reduce batch size
```bash
# Edit config/emailcampaign.php
'batch_size' => 50,  # Instead of 100
```

### Issue: Emails throttled/stuck in queue
**Solution:** Increase max_per_minute
```bash
# In admin SMTP Settings:
Set "Max Emails per Minute" to appropriate value (60-1000 depending on provider)
```

## Security Best Practices

1. **Update regularly**
   ```bash
   composer update
   php artisan migrate
   ```

2. **Environment variables**
   - Never commit .env
   - Use strong APP_KEY
   - Use strong database password

3. **Backups**
   - Daily database backups
   - Weekly file backups

4. **Firewall rules**
   - Only expose ports 80 (HTTP) and 443 (HTTPS)
   - Redis only accessible from app server

5. **Monitoring**
   - Monitor error logs daily
   - Set up email alerts for failures
   - Monitor queue depth

## Performance Tuning

### Database Optimization
```bash
# Analyze tables
mysqlcheck -o --all-databases

# Rebuild indexes
php artisan migrate:refresh --seed
```

### Redis Optimization
```bash
# Increase max clients
redis-cli CONFIG SET maxclients 10000

# Monitor commands
redis-cli MONITOR
```

### Application Optimization
```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev
```

## Scaling

### Horizontal Scaling
1. Add more queue workers (Supervisor numprocs)
2. Add more web servers behind load balancer
3. Use managed RDS for database
4. Use managed Redis (ElastiCache, Azure Cache)

### Vertical Scaling
1. Increase server RAM
2. Optimize MySQL my.cnf
3. Optimize PHP-FPM process settings

---

**Last Updated:** February 2026  
**Support Email:** support@yourdomain.com
