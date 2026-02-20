# Email Campaign System - Architecture Documentation

## Project Structure Overview

```
email-campaign-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/           # Route handlers
│   │   │   ├── DashboardController
│   │   │   ├── CampaignController
│   │   │   ├── SubscriberController
│   │   │   ├── EmailTemplateController
│   │   │   ├── SubscribersListController
│   │   │   ├── SmtpSettingController
│   │   │   ├── TrackingController
│   │   │   └── UnsubscribeController
│   │   └── Middleware/
│   │       └── CheckAdminRole.php
│   ├── Models/                    # Eloquent models
│   │   ├── User.php
│   │   ├── Campaign.php
│   │   ├── Subscriber.php
│   │   ├── EmailTemplate.php
│   │   ├── SubscribersList.php
│   │   ├── EmailLog.php
│   │   ├── OpenLog.php
│   │   ├── ClickLog.php
│   │   └── SmtpSetting.php
│   ├── Services/                  # Business logic
│   │   ├── EmailSendingService.php
│   │   ├── CampaignService.php
│   │   ├── SubscriberService.php
│   │   └── TrackingService.php
│   ├── Jobs/                      # Queued jobs
│   │   ├── SendCampaignEmailJob.php
│   │   ├── StartCampaignJob.php
│   │   └── ProcessScheduledCampaignsJob.php
│   ├── Providers/                 # Service providers
│   │   ├── AppServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Console/
│       └── Kernel.php             # Schedule commands
├── database/
│   ├── migrations/                # Schema migrations
│   │   ├── *_create_users_table
│   │   ├── *_create_subscribers_lists_table
│   │   ├── *_create_subscribers_table
│   │   ├── *_create_email_templates_table
│   │   ├── *_create_campaigns_table
│   │   ├── *_create_email_logs_table
│   │   ├── *_create_open_logs_table
│   │   ├── *_create_click_logs_table
│   │   └── *_create_smtp_settings_table
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php          # Main layout
│   ├── dashboard/
│   │   └── index.blade.php        # Dashboard
│   ├── campaigns/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── subscribers/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── show.blade.php
│   │   └── import.blade.php
│   ├── templates/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── lists/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── settings/
│       └── smtp.blade.php
├── routes/
│   ├── web.php                    # Web routes
│   └── api.php                    # API routes (ready for expansion)
├── config/
│   ├── app.php                    # Application config
│   ├── queue.php                  # Queue configuration
│   ├── cache.php                  # Cache configuration
│   └── emailcampaign.php          # Custom config
├── public/
│   ├── css/
│   ├── js/
│   └── index.php                  # Entry point
├── storage/
│   ├── logs/
│   ├── app/
│   └── framework/
├── bootstrap/
│   └── cache/
├── composer.json                  # PHP dependencies
├── README.md                      # Documentation
└── SETUP_GUIDE.md                 # Setup instructions
```

## Data Models & Relationships

### Users
```
User
├── campaigns (hasMany)
└── templates (hasMany)
```

### Campaign Workflow
```
Campaign
├── template (belongsTo)
├── list (belongsTo)
├── creator (belongsTo User)
├── emailLogs (hasMany)
├── openLogs (hasMany)
└── clickLogs (hasMany)
```

### Subscribers
```
Subscriber
├── lists (belongsToMany via subscriber_list)
├── emailLogs (hasMany)
├── openLogs (hasMany)
└── clickLogs (hasMany)

SubscribersList
├── subscribers (belongsToMany)
└── campaigns (hasMany)
```

### Email Tracking
```
EmailLog
├── campaign (belongsTo)
├── subscriber (belongsTo)
├── openLogs (hasMany)
└── clickLogs (hasMany)

OpenLog
├── emailLog (belongsTo)
├── campaign (belongsTo)
└── subscriber (belongsTo)

ClickLog
├── emailLog (belongsTo)
├── campaign (belongsTo)
└── subscriber (belongsTo)
```

## Request Flow

### Campaign Sending Flow
```
1. User creates campaign (DashboardController)
   ↓
2. CampaignService stores in DB
   ↓
3. User sends/schedules campaign
   ↓
4. StartCampaignJob dispatched
   ↓
5. Job creates EmailLog entries for each subscriber
   ↓
6. SendCampaignEmailJob dispatched for each batch (100 emails)
   ↓
7. EmailSendingService:
   a. Replaces variables (name, email, unsubscribe_link)
   b. Adds open tracking pixel
   c. Replaces links with tracking URLs
   d. Sends via configured SMTP
   ↓
8. Email marked as sent/failed
   ↓
9. Campaign stats updated
```

### Email Tracking Flow
```
User opens email
    ↓
Requests tracking pixel (GET /tracking/open/{token})
    ↓
TrackingController.trackOpen()
    ↓
TrackingService.recordOpen()
    ↓
OpenLog created
    ↓
Campaign.open_count incremented
    ↓
Returns 1x1 transparent GIF

User clicks link
    ↓
Redirects to /tracking/click/{token}/{emailLogId}/{url}
    ↓
TrackingController.trackClick()
    ↓
TrackingService.recordClick()
    ↓
ClickLog created
    ↓
Campaign.click_count incremented
    ↓
Redirects to original URL
```

## Service Layer

### EmailSendingService
- Sends individual emails via SMTP
- Variable replacement
- Tracking pixel injection
- Link URL rewriting

### CampaignService
- Create/update campaigns
- Filter subscribers by segments
- Calculate analytics
- Manage campaign lifecycle

### SubscriberService
- Create/update subscribers
- Import from CSV
- Export to CSV
- Manage unsubscribe

### TrackingService
- Record opens
- Record clicks
- Generate analytics
- Click heatmap data

## Queue Processing

### Jobs
1. **ProcessScheduledCampaignsJob**
   - Runs every minute via scheduler
   - Finds campaigns with scheduled_at <= now()
   - Dispatches StartCampaignJob

2. **StartCampaignJob**
   - Gets all active subscribers from list
   - Creates EmailLog records
   - Dispatches SendCampaignEmailJob batches (100 at a time)

3. **SendCampaignEmailJob**
   - Sends individual email
   - Handles retries (up to 3 times)
   - Exponential backoff: 60s, 120s, 300s
   - Updates campaign stats

### Retry Mechanism
```
Attempt 1:
   Send email
   If fails: Release after 60 seconds

Attempt 2:
   Send email
   If fails: Release after 120 seconds

Attempt 3:
   Send email
   If fails: Mark as failed, increment failed_count

After 3 attempts:
   Mark as failed in email_logs
   Increment campaign.failed_count
```

## Database Optimizations

### Indexes
- All foreign keys indexed
- Campaign status indexed (for scheduling)
- Subscriber email unique indexed
- Tracking tokens unique indexed
- Pivot table unique combined index

### Query Optimization
- Eager loading with `with()` to prevent N+1
- Pagination on list views (25 items)
- Select specific columns when possible
- Batch operations for bulk imports

## Caching Strategy

- Campaign statistics cached during import
- Subscriber lists cached for dropdown selects
- SMTP settings cached (invalidate on update)
- Redis as session driver

## Security Measures

1. **Authentication**
   - Laravel Breeze/Jetstream for auth
   - Password hashing with bcrypt
   - Session-based login

2. **Authorization**
   - Role-based access (admin/staff)
   - CheckAdminRole middleware on protected routes
   - User-specific resource access

3. **Data Protection**
   - Unique unsubscribe tokens (40 characters)
   - SMTP password encrypted in database
   - HTTPS recommended in production
   - CSRF tokens on all forms

4. **Input Validation**
   - All inputs validated server-side
   - Email format validation
   - File upload validation for CSV

## Performance Targets

- Page load: < 2 seconds
- Email sending: 1000 emails in ~10 minutes (with 100/batch)
- Dashboard metrics: < 1 second
- Queue processing: Real-time with Redis

## Scaling Considerations

1. **Horizontal**
   - Add queue workers (4+ recommended)
   - Load balance web servers
   - Use managed database/Redis

2. **Vertical**
   - Increase server RAM (4GB minimum)
   - Optimize MySQL configuration
   - PHP-FPM worker processes

3. **Features**
   - Ready for API expansion
   - Template for microservices
   - Ready for WebSocket events (optional)

---

**Last Updated:** February 2026
