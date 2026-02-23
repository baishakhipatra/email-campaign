<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Services\EmailSendingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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
        
            $smtp = SmtpSetting::where('is_active', 1)->first();

            if (!$smtp) {
                throw new \Exception('No active SMTP configuration found.');
            }

            Config::set('mail.mailers.smtp.host', $smtp->host);
            Config::set('mail.mailers.smtp.port', $smtp->port);
            Config::set('mail.mailers.smtp.username', $smtp->username);
            Config::set('mail.mailers.smtp.password', $smtp->password);
            Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
            Config::set('mail.from.address', $smtp->from_email);
            Config::set('mail.from.name', $smtp->from_name);

            $subscriberData = [
                'name' => $this->subscriber->name ?? 'Subscriber',
                'email' => $this->subscriber->email,
                'first_name' => explode(' ', $this->subscriber->name ?? 'Friend')[0],
                'unsubscribe_link' => route('unsubscribe', $this->subscriber->unsubscribe_token),
            ];

            $emailSendingService->sendCampaignEmail(
                $this->campaign,
                $this->subscriber->email,
                $subscriberData
            );

            $this->emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
                'attempts' => $this->emailLog->attempts + 1,
            ]);

            $this->campaign->increment('sent_count');

        } catch (\Throwable $e) {

            $this->emailLog->update([
                'attempts' => $this->emailLog->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->emailLog->update(['status' => 'failed']);
                $this->campaign->increment('failed_count');
            }

            throw $e; 
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
