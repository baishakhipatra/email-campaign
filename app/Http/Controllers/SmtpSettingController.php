<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SmtpSettingController extends Controller
{

    public function index(): View
    {
        $smtpSetting = SmtpSetting::first();
        return view('settings.smtp', ['setting' => $smtpSetting]);
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'encryption' => 'in:tls,ssl,none',
            'max_per_minute' => 'integer|min:1|max:1000',
        ]);

        $setting = SmtpSetting::first();

        if (!$setting) {
            SmtpSetting::create($validated);
        } else {
            $setting->update($validated);
        }

        return redirect()->back()
            ->with('success', 'SMTP settings saved successfully');
    }


    public function test(): RedirectResponse
    {
        $setting = SmtpSetting::first();

        if (!$setting) {
            return redirect()->back()->with('error', 'No SMTP settings configured');
        }

        try {
            Config::set('mail.mailers.smtp', $setting->toMailerConfig());

          
            Mail::raw('Test email from Email Campaign System', function ($message) {
                $message->to(auth()->user()->email)
                    ->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('SMTP Test Email');
            });

            $setting->update([
                'test_result' => true,
                'last_tested_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Test email sent successfully');

        } catch (\Exception $e) {
            $setting->update([
                'test_result' => false,
                'last_tested_at' => now(),
            ]);

            return redirect()->back()
                ->with('error', 'SMTP test failed: ' . $e->getMessage());
        }
    }
}
