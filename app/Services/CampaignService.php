<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\SubscribersList;

class CampaignService
{
    /**
     * Create a campaign
     */
    public function createCampaign($data)
    {
        $campaign = Campaign::create([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'template_id' => $data['template_id'],
            'list_id' => $data['list_id'],
            'status' => $data['status'] ?? 'draft',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'segments' => $data['segments'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return $campaign;
    }

    /**
     * Update a campaign
     */
    public function updateCampaign($campaign, $data)
    {
        $campaign->update([
            'name' => $data['name'] ?? $campaign->name,
            'subject' => $data['subject'] ?? $campaign->subject,
            'from_name' => $data['from_name'] ?? $campaign->from_name,
            'from_email' => $data['from_email'] ?? $campaign->from_email,
            'template_id' => $data['template_id'] ?? $campaign->template_id,
            'list_id' => $data['list_id'] ?? $campaign->list_id,
            'status' => $data['status'] ?? $campaign->status,
            'scheduled_at' => $data['scheduled_at'] ?? $campaign->scheduled_at,
            'segments' => $data['segments'] ?? $campaign->segments,
        ]);

        return $campaign;
    }

    /**
     * Get filtered subscribers for campaign
     */
    public function getFilteredSubscribers(Campaign $campaign)
    {
        $query = $campaign->list()
            ->first()
            ->subscribers()
            ->where('subscribers.status', 'active')
            ->where('subscribers.is_active', true);

        // Apply segments/filters if any
        if ($campaign->segments) {
            $query = $this->applySegments($query, $campaign->segments);
        }

        return $query->get();
    }

    /**
     * Apply segmentation filters
     */
    private function applySegments($query, $segments)
    {
        // Example: Filter by subscription date
        if (isset($segments['subscription_date_from'])) {
            $query->where('subscribed_at', '>=', $segments['subscription_date_from']);
        }

        if (isset($segments['subscription_date_to'])) {
            $query->where('subscribed_at', '<=', $segments['subscription_date_to']);
        }

        // Filter by email domain
        if (isset($segments['email_domain'])) {
            $query->where('email', 'like', '%@' . $segments['email_domain']);
        }

        return $query;
    }

    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics(Campaign $campaign)
    {
        return [
            'total_recipients' => $campaign->total_subscribers,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'opened' => $campaign->open_count,
            'clicked' => $campaign->click_count,
            'open_rate' => $campaign->getOpenRate(),
            'click_rate' => $campaign->getClickRate(),
        ];
    }
}
