<?php

namespace App\Console\Commands;
use App\Models\Campaign;
use App\Jobs\StartCampaignJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Services\EmailSendingService;
use App\Models\EmailLog;
use Illuminate\Support\Str;

class SendScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:send-scheduled';
    protected $description = 'Send scheduled email campaigns';

    public function handle()
    {
        try {
            $smtp = DB::table('smtp_settings')->where('is_active', 1)->first();

            if (!$smtp) {
                logger()->error('SMTP settings not found');
                return 0;
            }

            Config::set('mail.default', 'smtp');

            Config::set('mail.mailers.smtp.host', $smtp->host);
            Config::set('mail.mailers.smtp.port', $smtp->port);
            Config::set('mail.mailers.smtp.username', $smtp->username);
            Config::set('mail.mailers.smtp.password', $smtp->password);
            Config::set(
                'mail.mailers.smtp.encryption',
                $smtp->encryption === 'none' ? null : $smtp->encryption
            );

            Config::set('mail.from.address', $smtp->username);
            Config::set('mail.from.name', config('app.name'));

            $campaigns = Campaign::where('status', 'scheduled')
                ->where('scheduled_at', '<=', now())
                ->get();

            if ($campaigns->isEmpty()) {
                logger()->info('No scheduled campaigns found at ' . now());
                return 0;
            }

            foreach ($campaigns as $campaign) {
                logger()->info('Sending campaign ID: ' . $campaign->id);

                dispatch(new StartCampaignJob($campaign));

                $campaign->update([
                    'status' => 'sending'
                ]);
            }
            return 0;

        } catch (\Throwable $e) {
            logger()->error('Campaign send failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return 1;
        }
    }
}
