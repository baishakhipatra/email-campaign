<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    use HasFactory;

    protected $table = 'smtp_settings';

    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'is_active',
        'max_per_minute',
        'last_tested_at',
        'test_result',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_tested_at' => 'datetime',
        'test_result' => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public static function getActive()
    {
        logger()->info('SMTP getActive called');
        return self::where('is_active', 1)->first();
    }

    public function toMailerConfig()
    {
        return [
            'driver' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
        ];
    }
}
