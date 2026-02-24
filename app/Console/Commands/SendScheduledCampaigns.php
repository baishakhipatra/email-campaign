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
        $campaigns = Campaign::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            dispatch(new StartCampaignJob($campaign));
        }

        $this->info('Scheduled campaigns processed');
    }
}
