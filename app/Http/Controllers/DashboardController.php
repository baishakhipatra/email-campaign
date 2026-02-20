<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Subscriber;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        $totalSubscribers = Subscriber::where('is_active', true)->count();
        $totalCampaigns = Campaign::count();
        $totalSent = Campaign::sum('sent_count');
        
        // Calculate average open rate
        $campaigns = Campaign::where('sent_count', '>', 0)->get();
        $averageOpenRate = $campaigns->count() > 0 
            ? round($campaigns->sum('open_count') / $campaigns->sum('sent_count') * 100, 2)
            : 0;

        // Calculate average click rate
        $averageClickRate = $campaigns->count() > 0 
            ? round($campaigns->sum('click_count') / $campaigns->sum('sent_count') * 100, 2)
            : 0;

        // Get recent campaigns
        $recentCampaigns = Campaign::with('template', 'list')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get campaign performance chart data
        $chartData = Campaign::where('sent_count', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($campaign) {
                return [
                    'name' => $campaign->name,
                    'opens' => $campaign->open_count,
                    'clicks' => $campaign->click_count,
                    'openRate' => $campaign->getOpenRate(),
                    'clickRate' => $campaign->getClickRate(),
                ];
            });

        return view('dashboard.index', [
            'totalSubscribers' => $totalSubscribers,
            'totalCampaigns' => $totalCampaigns,
            'totalSent' => $totalSent,
            'averageOpenRate' => $averageOpenRate,
            'averageClickRate' => $averageClickRate,
            'recentCampaigns' => $recentCampaigns,
            'chartData' => $chartData,
        ]);
    }
}
