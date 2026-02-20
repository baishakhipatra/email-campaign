<?php

namespace App\Services;

use App\Models\OpenLog;
use App\Models\ClickLog;
use App\Models\EmailLog;

class TrackingService
{
    /**
     * Record email open
     */
    public function recordOpen($trackingToken)
    {
        $emailLog = EmailLog::where('tracking_token', $trackingToken)->first();

        if (!$emailLog) {
            return false;
        }

        // Check if already opened
        if ($emailLog->openLogs()->exists()) {
            return false;
        }

        OpenLog::create([
            'email_log_id' => $emailLog->id,
            'campaign_id' => $emailLog->campaign_id,
            'subscriber_id' => $emailLog->subscriber_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update campaign open count
        $campaign = $emailLog->campaign;
        $campaign->open_count = $campaign->openLogs()->distinct('subscriber_id')->count();
        $campaign->save();

        return true;
    }

    /**
     * Record email click
     */
    public function recordClick($clickToken, $emailLogId, $originalUrl)
    {
        $emailLog = EmailLog::find($emailLogId);

        if (!$emailLog) {
            return null;
        }

        ClickLog::create([
            'email_log_id' => $emailLog->id,
            'campaign_id' => $emailLog->campaign_id,
            'subscriber_id' => $emailLog->subscriber_id,
            'original_url' => base64_decode($originalUrl),
            'click_token' => $clickToken,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update campaign click count
        $campaign = $emailLog->campaign;
        $campaign->click_count = $campaign->clickLogs()->distinct('subscriber_id')->count();
        $campaign->save();

        return base64_decode($originalUrl);
    }

    /**
     * Get campaign performance metrics
     */
    public function getCampaignMetrics($campaignId)
    {
        $emailLogs = EmailLog::where('campaign_id', $campaignId)->get();
        
        $totalEmails = $emailLogs->count();
        $sentEmails = $emailLogs->where('status', 'sent')->count();
        $uniqueOpens = OpenLog::where('campaign_id', $campaignId)
            ->distinct('subscriber_id')
            ->count();
        $uniqueClicks = ClickLog::where('campaign_id', $campaignId)
            ->distinct('subscriber_id')
            ->count();

        return [
            'total_sent' => $sentEmails,
            'open_count' => $uniqueOpens,
            'click_count' => $uniqueClicks,
            'open_rate' => $sentEmails > 0 ? round(($uniqueOpens / $sentEmails) * 100, 2) : 0,
            'click_rate' => $sentEmails > 0 ? round(($uniqueClicks / $sentEmails) * 100, 2) : 0,
        ];
    }

    /**
     * Get click heatmap data for a campaign
     */
    public function getClickHeatmap($campaignId)
    {
        return ClickLog::where('campaign_id', $campaignId)
            ->select('original_url')
            ->selectRaw('COUNT(*) as click_count')
            ->groupBy('original_url')
            ->orderBy('click_count', 'desc')
            ->get();
    }
}
