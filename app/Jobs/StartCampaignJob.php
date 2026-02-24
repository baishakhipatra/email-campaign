<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendCampaignEmailJob;
use Illuminate\Support\Facades\Log;

class StartCampaignJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $campaign;

    public $tries = 4;
    public $timeout = 300;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        try {
            $subscribers = $this->campaign->list->subscribers()
                ->where('status', 'active')
                ->where('is_active', 1)
                ->get();

            $this->campaign->update([
                'status' => 'sending',
                'started_at' => now(),
                'total_subscribers' => $subscribers->count(),
            ]);

            foreach ($subscribers as $subscriber) {

                $emailLog = EmailLog::create([
                    'campaign_id' => $this->campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'recipient_email' => $subscriber->email,
                    'status' => 'pending',
                ]);

                SendCampaignEmailJob::dispatch(
                    $this->campaign,
                    $subscriber,
                    $emailLog
                );
            }

        } catch (\Throwable $e) {

            $this->campaign->update(['status' => 'failed']);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update(['status' => 'failed']);
    }
}
