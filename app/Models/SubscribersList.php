<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribersList extends Model
{
    use HasFactory;

    protected $table = 'subscribers_lists';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'subscriber_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_list', 'list_id',
        'subscriber_id' );
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'list_id');
    }
}
