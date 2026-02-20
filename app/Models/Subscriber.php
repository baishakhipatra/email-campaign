<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'status',
        'custom_fields',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
        'is_active',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->unsubscribe_token) {
                $model->unsubscribe_token = Str::random(40);
            }
        });
    }

    public function lists()
    {
        return $this->belongsToMany(SubscribersList::class, 'subscriber_list','subscriber_id',
        'list_id' );
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

    public function isActive()
    {
        return $this->status === 'active' && $this->is_active;
    }

    public function unsubscribe()
    {
        $this->status = 'unsubscribed';
        $this->unsubscribed_at = now();
        $this->save();
    }
}
