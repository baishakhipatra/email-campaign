<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenLog extends Model
{
    use HasFactory;

    protected $table = 'open_logs';

    protected $fillable = [
        'email_log_id',
        'campaign_id',
        'subscriber_id',
        'ip_address',
        'user_agent',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function emailLog()
    {
        return $this->belongsTo(EmailLog::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
