<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'email_templates';

    protected $fillable = [
        'name',
        'slug',
        'html_content',
        'description',
        'variables',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'template_id');
    }

    public function getAvailableVariables()
    {
        return [
            '{{name}}' => 'Subscriber Name',
            '{{email}}' => 'Subscriber Email',
            '{{unsubscribe_link}}' => 'Unsubscribe Link',
            '{{first_name}}' => 'First Name',
            '{{last_name}}' => 'Last Name',
        ];
    }
}
