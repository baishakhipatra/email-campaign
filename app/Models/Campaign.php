<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'from_name',
        'from_email',
        'template_id',
        'list_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_subscribers',
        'sent_count',
        'failed_count',
        'open_count',
        'click_count',
        'segments',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'segments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(8);
            }
        });
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function list()
    {
        return $this->belongsTo(SubscribersList::class, 'list_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function openLogs()
    {
        return $this->hasMany(OpenLog::class);
    }

    public function clickLogs()
    {
        return $this->hasMany(ClickLog::class);
    }

    public function getOpenRate()
    {
        if ($this->sent_count === 0) {
            return 0;
        }
        return round(($this->open_count / $this->sent_count) * 100, 2);
    }

    public function getClickRate()
    {
        if ($this->sent_count === 0) {
            return 0;
        }
        return round(($this->click_count / $this->sent_count) * 100, 2);
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isSending()
    {
        return $this->status === 'sending';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }
}
