<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscriber;
use Carbon\Carbon;
use Mail;

class SendOccasionWishes extends Command
{
    protected $signature = 'wishes:send';
    protected $description = 'Send birthday and anniversary wishes';

    public function handle()
    {
        $today = Carbon::today();

        $month = $today->month;
        $day   = $today->day;

        // 🎂 Birthday Users
        $birthdays = Subscriber::whereMonth('birthday_date', $month)
            ->whereDay('birthday_date', $day)
            ->where('is_active', 1)
            ->get();

        foreach ($birthdays as $user) {
            Mail::to($user->email)->queue(new \App\Mail\BirthdayWishMail($user));
        }

        // 💍 Anniversary Users
        $anniversaries = Subscriber::whereMonth('anniversary_date', $month)
            ->whereDay('anniversary_date', $day)
            ->where('is_active', 1)
            ->get();

        foreach ($anniversaries as $user) {
            Mail::to($user->email)->queue(new \App\Mail\AnniversaryWishMail($user));
        }

        $this->info('Wishes sent successfully!');
    }
}
