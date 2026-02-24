<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\SubscribersList;
use App\Services\CampaignService;
use App\Jobs\StartCampaignJob;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Jobs\SendCampaignMailJob;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }


    public function index(): View
    {
        $campaigns = Campaign::withCount('template', 'list', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('campaigns.index', ['campaigns' => $campaigns]);
    }


    public function create(): View
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        $lists = SubscribersList::where('is_active', true)->withCount('subscribers')->get();

        return view('campaigns.create', ['templates' => $templates, 'lists' => $lists]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email',
            'template_id' => 'required|exists:email_templates,id',
            'list_id' => 'required|exists:subscribers_lists,id',
            'status' => 'in:draft,scheduled',
            'scheduled_at' => 'nullable|date|required_if:status,scheduled',
            'segments' => 'nullable|json',
        ]);

        $campaign = $this->campaignService->createCampaign($validated);
        $campaign->load('template');
        if ($campaign->status === 'scheduled') {
            $subscribers = $campaign->list->subscribers()
                ->where('status', 'active')
                ->where('is_active', 1)
                ->get();

            $campaign->update([
                'total_subscribers' => $subscribers->count(),
            ]);

            foreach ($subscribers as $subscriber) {
                SendCampaignMailJob::dispatch(
                    $campaign->id,
                    $subscriber->id
                )->delay($campaign->scheduled_at);
            }

            $campaign->update([
                'status'     => 'sending',
                'started_at'=> now(),
            ]);
        }
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully');
    }

    public function show(Campaign $campaign): View
    {
        $analytics = $this->campaignService->getCampaignAnalytics($campaign);

        return view('campaigns.show', [
            'campaign' => $campaign,
            'analytics' => $analytics,
        ]);
    }


    public function edit(Campaign $campaign): View
    {
        if (!$campaign->isDraft()) {
            abort(403, 'Cannot edit a campaign that has been sent');
        }

        $templates = EmailTemplate::where('is_active', true)->get();
        $lists = SubscribersList::where('is_active', true)->get();

        return view('campaigns.edit', [
            'campaign' => $campaign,
            'templates' => $templates,
            'lists' => $lists,
        ]);
    }


    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        if (!$campaign->isDraft()) {
            abort(403, 'Cannot edit a campaign that has been sent');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email',
            'template_id' => 'required|exists:email_templates,id',
            'list_id' => 'required|exists:subscribers_lists,id',
            'status' => 'in:draft,scheduled',
            'scheduled_at' => 'nullable|date',
        ]);

        $this->campaignService->updateCampaign($campaign, $validated);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully');
    }


    public function send(Campaign $campaign): RedirectResponse
    {
        if (!$campaign->isDraft()) {
            return redirect()->back()->with('error', 'Only draft campaigns can be sent');
        }

            $subscribers = $campaign->list->subscribers()
                ->where('status', 'active')
                ->where('is_active', 1)
                ->get();

            $campaign->update([
                'status'            => 'sending',
                'started_at'        => now(),
                'total_subscribers' => $subscribers->count(),
            ]);

            foreach ($subscribers as $subscriber) {
                SendCampaignMailJob::dispatch(
                    $campaign->id,
                    $subscriber->id
                )->delay($campaign->scheduled_at);
            }

        return redirect()->back()
            ->with('success', 'Campaign is being sent. Check back shortly for updates');
    }


    public function destroy(Campaign $campaign): RedirectResponse
    {
        if (!$campaign->isDraft()) {
            return redirect()->back()->with('error', 'Cannot delete a sent campaign');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully');
    }
}
