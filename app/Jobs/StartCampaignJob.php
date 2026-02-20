<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class StartCampaignJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $campaign;

    public $tries = 1;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get subscribers for this campaign
            $subscribers = $this->campaign->list->subscribers()
                ->where('status', 'active')
                ->where('is_active', true)
                ->get();

            $this->campaign->update([
                'status' => 'sending',
                'started_at' => now(),
                'total_subscribers' => $subscribers->count(),
            ]);

            // Create email logs and dispatch jobs in batches
            $batch = [];
            foreach ($subscribers as $subscriber) {
                $emailLog = EmailLog::create([
                    'campaign_id' => $this->campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'recipient_email' => $subscriber->email,
                    'status' => 'pending',
                ]);

                $batch[] = new SendCampaignEmailJob(
                    $this->campaign,
                    $subscriber,
                    $emailLog
                );

                // Dispatch in batches of 100
                if (count($batch) >= 100) {
                    dispatch($batch);
                    $batch = [];
                }
            }

            // Dispatch remaining batch
            if (!empty($batch)) {
                dispatch($batch);
            }

        } catch (\Exception $e) {
            $this->campaign->update([
                'status' => 'failed',
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'failed',
        ]);
    }
}
