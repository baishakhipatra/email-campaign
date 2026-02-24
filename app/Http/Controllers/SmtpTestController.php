<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\SmtpSetting;

class SmtpTestController extends Controller
{
    public function sendTest()
    {
        // Fetch active SMTP
        $smtp = SmtpSetting::where('is_active', 1)->first();

        if (!$smtp) {
            return "SMTP not configured";
        }

        // Dynamically set mail config
        config([
            'mail.mailers.smtp.host'       => $smtp->host,
            'mail.mailers.smtp.port'       => $smtp->port,
            'mail.mailers.smtp.username'   => $smtp->username,
            'mail.mailers.smtp.password'   => $smtp->password,
            'mail.mailers.smtp.encryption' => $smtp->encryption,
            'mail.from.address'            => $smtp->from_email ?: $smtp->username,
            'mail.from.name'               => $smtp->from_name ?: 'Your App Name',
        ]);

        try {
            Mail::raw('SMTP Test Mail Successful!', function ($message) use ($smtp) {
                $message->to('baishakhi.patra@techmantra.co')
                        ->from($smtp->from_email ?: $smtp->username, $smtp->from_name ?: 'Your App Name') 
                        ->subject('SMTP Test Mail');
            });

            return "SMTP is working. Test mail sent!";
        } catch (\Exception $e) {
            return "SMTP Failed: " . $e->getMessage();
        }
    }
}
