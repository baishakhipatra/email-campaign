# Email Campaign Management System

A complete, production-ready email campaign management system built with Laravel. This system allows admins to manage subscribers, create campaigns, send bulk emails with tracking, and monitor campaign performance.

## 🎯 Features

### Core Features
- ✅ **AdminAuthentication** - Secure login with role-based access control
- ✅ **Dashboard** - Real-time campaign performance metrics and statistics
- ✅ **Campaign Management** - Create, schedule, and send email campaigns
- ✅ **Email Templates** - HTML editor with dynamic variables support
- ✅ **Subscriber Management** - Add, import (CSV), export, and segment subscribers
- ✅ **Subscriber Lists** - Organize subscribers into multiple lists
- ✅ **Queue-Based Sending** - Async email sending with Redis + Laravel Queue
- ✅ **Email Tracking** - Open tracking (pixel) and click tracking (URL replacement)
- ✅ **Analytics** - Campaign performance, open rates, click rates, heatmaps
- ✅ **Unsubscribe System** - Unique tokens per subscriber with automatic status update
- ✅ **SMTP Configuration** - Dynamic SMTP settings with encryption support

### Technical Features
- ✅ **Redis Queue** - Batch processing with 100 emails per job
- ✅ **Job Retry** - Automatic retry with exponential backoff
- ✅ **Database Optimization** - Indexed queries and efficient relationships
- ✅ **Clean Architecture** - Service layer, Repository pattern ready
- ✅ **RESTful Routes** - Full resource routing
- ✅ **Bootstrap UI** - Responsive admin panel
- ✅ **Validation** - Comprehensive input validation
- ✅ **Error Handling** - Proper exception handling

## 📋 System Requirements

- PHP 8.2+
- Laravel 11.x
- MySQL 8.0+
- Redis 6.0+
- Composer

## 🚀 Installation

### 1. Install Dependencies
```bash
cd My_Project
composer install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Update .env File
```env
APP_NAME="Email Campaign System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=email_campaign_db
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@emailcampaign.local"
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Create Admin User
```bash
php artisan tinker

# In tinker shell:
App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);

exit
```

Or use seeder if available:
```bash
php artisan db:seed
```

### 6. Install Node Dependencies (Optional - for assets)
```bash
npm install
npm run build
```

## 🔧 Configuration

### Queue Worker Setup

For development:
```bash
php artisan queue:work redis
```

For production (use Supervisor or similar):
```bash
# supervisor config: /etc/supervisor/conf.d/email-campaign.conf
[program:email-campaign-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=4
user=www-data
```

### Scheduled Tasks

Add to crontab for processing scheduled campaigns:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

The `ProcessScheduledCampaignsJob` will check for campaigns with status "scheduled" and send date <= now, then dispatch `StartCampaignJob`.

### Redis Setup

Ensure Redis is running:
```bash
# Windows (if installed via WSL or using Redis-Windows)
redis-server

# Linux/Mac
brew services start redis
# or
sudo systemctl start redis-server
```

### SMTP Configuration

1. Navigate to Settings → SMTP Settings
2. Enter your SMTP credentials:
   - Host (e.g., smtp.gmail.com)
   - Port (usually 587 for TLS or 465 for SSL)
   - Username and Password (encrypted in database)
   - Encryption type (TLS/SSL)
3. Click "Test Connection" to verify

## 📊 Database Schema

### Key Tables

- **users** - Admin users with roles
- **campaigns** - Campaign records with status tracking
- **email_templates** - Reusable email templates
- **subscribers** - Email subscribers
- **subscribers_lists** - Subscriber lists/segments
- **subscriber_list** - Pivot table for many-to-many relationship
- **email_logs** - Individual email sending records
- **open_logs** - Email open tracking
- **click_logs** - Link click tracking
- **smtp_settings** - SMTP configuration

## 🎯 Usage Guide

### Creating a Campaign

1. **Go to Campaigns** → Click "New Campaign"
2. **Fill Details**:
   - Campaign Name
   - Email Subject
   - From Name & Email
   - Select Template
   - Select Subscriber List
3. **Choose Status**:
   - Draft: Save and edit later
   - Scheduled: Set send date/time
4. **Create** and later send or schedule

### Managing Subscribers

1. **Add Manually** → Click "Add Subscriber"
2. **Import CSV** → Click "Import CSV" (format: email, name, optional fields)
3. **View Lists** → See subscribers organized by lists
4. **Export** → Download list as CSV from Lists page

### Email Templates

1. **Create Template** → Fill name and HTML content
2. **Use Variables**:
   - `{{name}}` - Full name
   - `{{email}}` - Email address
   - `{{first_name}}` - First name
   - `{{last_name}}` - Last name
   - `{{unsubscribe_link}}` - Unsubscribe link
3. **Save** and use in campaigns

### Monitoring Campaigns

1. **Dashboard** → See overall metrics
2. **Campaign Details** → View specific campaign stats:
   - Total sent
   - Open rate %
   - Click rate %
   - Individual email logs
3. **Export Data** → CSV exports available

## 🔐 Security Features

- ✅ HTTPS ready
- ✅ CSRF protection on all forms
- ✅ Password encryption (bcrypt)
- ✅ SMTP password encrypted in database
- ✅ Unique unsubscribe tokens per subscriber
- ✅ Role-based access control
- ✅ Input validation on all endpoints
- ✅ SQL injection prevention (Eloquent ORM)

## 📈 Performance Optimization

- **Indexed queries** in migrations
- **Queue-based email sending** - doesn't block requests
- **Redis caching** for frequently accessed data
- **Pagination** on all list views
- **Batch processing** - 100 emails per queue job
- **Database connection pooling** - Redis
- **Job retry logic** - exponential backoff

## 🐛 Troubleshooting

### Emails not sending?
1. Check Redis is running: `redis-cli ping` (should return PONG)
2. Check queue worker is running: `php artisan queue:work redis`
3. Check SMTP settings in admin panel
4. Check laravel.log for errors

### Failed jobs piling up?
1. Monitor: `php artisan queue:failed`
2. Retry: `php artisan queue:retry all`
3. Flush: `php artisan queue:flush`

### High email sending fails?
1. Check SMTP rate limits
2. Increase `max_per_minute` in SMTP settings
3. Check email validation in CSV imports
4. Verify unsubscribed emails aren't included

### Memory issues?
1. Reduce `batch_size` in `config/emailcampaign.php`
2. Reduce `max_per_minute` 
3. Add more queue workers

## 🚢 Production Deployment

### Pre-Deployment Checklist
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache if needed
php artisan cache:clear
php artisan config:clear

# Set correct permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

### Environment Variables (Production)
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
LOG_CHANNEL=stack
MIX_ASSET_URL=/
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📝 API Ready

The system is structured to easily add REST API endpoints. Example routes can be added to `routes/api.php`:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('subscribers', SubscriberController::class);
    // Add more API routes
});
```

## 📚 Architecture Overview

```
app/
├── Http/
│   ├── Controllers/          # Request handlers
│   └── Middleware/           # Authentication, authorization
├── Models/                   # Database models with relationships
├── Services/                 # Business logic (EmailSending, Campaign, etc)
├── Jobs/                     # Queued jobs for async processing
└── Mail/                     # Mailable classes

database/
├── migrations/               # Database schema
└── seeders/                  # Data seeders

resources/views/
├── layouts/                  # Layout template
├── dashboard/                # Dashboard views
├── campaigns/                # Campaign CRUD views
├── subscribers/              # Subscriber management views
├── templates/                # Template management views
├── lists/                    # List management views
└── settings/                 # Settings views

config/
├── app.php                   # Application config
├── queue.php                 # Queue configuration
├── cache.php                 # Cache configuration
└── emailcampaign.php         # Custom email campaign config
```

## 📄 License

MIT License - feel free to use in commercial projects.

## 🤝 Support

For issues and questions, check:
- Laravel documentation: https://laravel.com/docs
- Queue docs: https://laravel.com/docs/queues
- Redis setup: https://redis.io/docs

---

**Last Updated:** February 2026  
**Version:** 1.0.0  
**Laravel Version:** 11.x
