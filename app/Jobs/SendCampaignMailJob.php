<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Models\Campaign;
use App\Models\Subscriber;

class SendCampaignMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;
    public int $subscriberId;

    public function __construct(int $campaignId, int $subscriberId)
    {
        $this->campaignId = $campaignId;
        $this->subscriberId = $subscriberId;
    }

    public function handle()
    {
        $campaign = Campaign::with('template')
            ->findOrFail($this->campaignId);
        $subscriber = Subscriber::findOrFail($this->subscriberId);

        if (!$campaign || !$subscriber) {
            return;
        }

        // Mail::to($subscriber->email)
        //     ->send(new CampaignMail($campaign, $subscriber));

        try {
            Mail::to($subscriber->email)
                ->send(new CampaignMail($campaign, $subscriber));
            Campaign::where('id', $campaign->id)
            ->increment('sent_count');

            logger()->info('Campaign job executed', [
                'campaign_id' => $this->campaignId,
                'subscriber_id' => $this->subscriberId,
            ]);

        } catch (\Throwable $e) {
            Campaign::where('id', $campaign->id)
            ->increment('failed_count');

            logger()->error('Campaign mail failed', [
                'campaign_id'   => $campaign->id,
                'subscriber_id' => $subscriber->id,
                'error'         => $e->getMessage(),
            ]);
        }
        $this->checkCampaignCompletion($campaign->id);
    }

    protected function checkCampaignCompletion(int $campaignId)
    {
        $campaign = Campaign::find($campaignId);

        if (!$campaign) return;

        $totalProcessed = $campaign->sent_count + $campaign->failed_count;

        if ($totalProcessed >= $campaign->total_subscribers) {
            $campaign->update([
                'status'        => 'sent',
                'completed_at'  => now(),
            ]);
        }
    }
}

