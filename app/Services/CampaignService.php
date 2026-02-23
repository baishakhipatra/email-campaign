<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\SubscribersList;

class CampaignService
{

    public function createCampaign(array $data): Campaign
    {
        if (($data['status'] ?? 'draft') === 'draft') {
            $data['scheduled_at'] = null;
        }

        if (($data['status'] ?? 'draft') === 'scheduled' && empty($data['scheduled_at'])) {
            throw new \InvalidArgumentException('Scheduled date is required');
        }

        return Campaign::create([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'template_id' => $data['template_id'],
            'list_id' => $data['list_id'],
            'status' => $data['status'] ?? 'draft',
            'scheduled_at' => $data['scheduled_at'],
            'segments' => $data['segments'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }
    // public function createCampaign($data)
    // {
    //     $campaign = Campaign::create([
    //         'name' => $data['name'],
    //         'subject' => $data['subject'],
    //         'from_name' => $data['from_name'],
    //         'from_email' => $data['from_email'],
    //         'template_id' => $data['template_id'],
    //         'list_id' => $data['list_id'],
    //         'status' => $data['status'] ?? 'draft',
    //         'scheduled_at' => $data['scheduled_at'] ?? null,
    //         'segments' => $data['segments'] ?? null,
    //         'created_by' => auth()->id(),
    //     ]);

    //     return $campaign;
    // }

    public function updateCampaign($campaign, $data)
    {
        if (($data['status'] ?? $campaign->status) === 'draft') {
            $data['scheduled_at'] = null;
        }

        if (($data['status'] ?? $campaign->status) === 'scheduled'
            && empty($data['scheduled_at'])) {
            throw new \InvalidArgumentException('Scheduled date is required');
        }

        $campaign->update([
            'name' => $data['name'] ?? $campaign->name,
            'subject' => $data['subject'] ?? $campaign->subject,
            'from_name' => $data['from_name'] ?? $campaign->from_name,
            'from_email' => $data['from_email'] ?? $campaign->from_email,
            'template_id' => $data['template_id'] ?? $campaign->template_id,
            'list_id' => $data['list_id'] ?? $campaign->list_id,
            'status' => $data['status'] ?? $campaign->status,
            'scheduled_at' => $data['scheduled_at'],
            'segments' => $data['segments'] ?? $campaign->segments,
        ]);

        return $campaign;
    }

    // public function updateCampaign($campaign, $data)
    // {
    //     $campaign->update([
    //         'name' => $data['name'] ?? $campaign->name,
    //         'subject' => $data['subject'] ?? $campaign->subject,
    //         'from_name' => $data['from_name'] ?? $campaign->from_name,
    //         'from_email' => $data['from_email'] ?? $campaign->from_email,
    //         'template_id' => $data['template_id'] ?? $campaign->template_id,
    //         'list_id' => $data['list_id'] ?? $campaign->list_id,
    //         'status' => $data['status'] ?? $campaign->status,
    //         'scheduled_at' => $data['scheduled_at'] ?? $campaign->scheduled_at,
    //         'segments' => $data['segments'] ?? $campaign->segments,
    //     ]);

    //     return $campaign;
    // }

    public function getFilteredSubscribers(Campaign $campaign)
    {
        $query = $campaign->list()
            ->first()
            ->subscribers()
            ->where('subscribers.status', 'active')
            ->where('subscribers.is_active', true);

        if ($campaign->segments) {
            $query = $this->applySegments($query, $campaign->segments);
        }

        return $query->get();
    }


    private function applySegments($query, $segments)
    {
        if (isset($segments['subscription_date_from'])) {
            $query->where('subscribed_at', '>=', $segments['subscription_date_from']);
        }

        if (isset($segments['subscription_date_to'])) {
            $query->where('subscribed_at', '<=', $segments['subscription_date_to']);
        }

        if (isset($segments['email_domain'])) {
            $query->where('email', 'like', '%@' . $segments['email_domain']);
        }

        return $query;
    }


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
