<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Scheduler
    |--------------------------------------------------------------------------
    |
    | The application scheduler is used for scheduled tasks like processing
    | scheduled campaigns and cleaning up old logs.
    */

    'schedule_enabled' => env('SCHEDULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Email Campaign Settings
    |--------------------------------------------------------------------------
    */

    'campaign' => [
        'batch_size' => 100,
        'max_retries' => 3,
        'retry_delay' => 300, // seconds
        'throttle_rate' => 60, // emails per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Settings
    |--------------------------------------------------------------------------
    */

    'tracking' => [
        'open_tracking_enabled' => true,
        'click_tracking_enabled' => true,
    ],

];
