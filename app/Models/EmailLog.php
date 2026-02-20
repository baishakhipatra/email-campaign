<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'recipient_email',
        'status',
        'attempts',
        'error_message',
        'tracking_token',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->tracking_token) {
                $model->tracking_token = Str::random(40);
            }
        });
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function openLogs()
    {
        return $this->hasMany(OpenLog::class);
    }

    public function clickLogs()
    {
        return $this->hasMany(ClickLog::class);
    }

    public function hasOpened()
    {
        return $this->openLogs()->exists();
    }

    public function hasClicked()
    {
        return $this->clickLogs()->exists();
    }
}
