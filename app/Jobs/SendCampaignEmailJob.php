<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Services\EmailSendingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;

class SendCampaignEmailJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $campaign;
    protected $subscriber;
    protected $emailLog;

    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry delays: 1 min, 2 min, 5 min
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, $subscriber, EmailLog $emailLog)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
        $this->emailLog = $emailLog;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailSendingService $emailSendingService): void
    {
        try {
            $subscriberData = [
                'name' => $this->subscriber->name ?? 'Subscriber',
                'email' => $this->subscriber->email,
                'first_name' => explode(' ', $this->subscriber->name ?? 'Friend')[0],
                'last_name' => isset(explode(' ', $this->subscriber->name ?? '')[1]) ? explode(' ', $this->subscriber->name)[1] : '',
                'unsubscribe_link' => route('unsubscribe', $this->subscriber->unsubscribe_token),
            ];

            $emailSendingService->sendCampaignEmail(
                $this->campaign,
                $this->subscriber->email,
                $subscriberData
            );

            // Mark as sent
            $this->emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
                'attempts' => $this->emailLog->attempts + 1,
            ]);

            // Update campaign stats
            $this->campaign->sent_count = $this->campaign->sent_count + 1;
            $this->campaign->save();

        } catch (\Exception $e) {
            $this->emailLog->update([
                'attempts' => $this->emailLog->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);

            if ($this->attempts >= $this->tries) {
                $this->emailLog->update(['status' => 'failed']);
                $this->campaign->failed_count = $this->campaign->failed_count + 1;
                $this->campaign->save();
            } else {
                $this->release(300); // Retry after 5 minutes
            }
        }
    }

    /**
     * Get the middleware for the job.
     */
    public function middleware(): array
    {
        return [new ThrottlesExceptions(3, 60)];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->emailLog->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $this->campaign->failed_count = $this->campaign->failed_count + 1;
        $this->campaign->save();
    }
}
