<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Jobs\SendCampaignMailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function send_campaign_mail()
    {
        $now = Carbon::now()->startOfMinute();

        DB::beginTransaction();

        try {

            $campaigns = Campaign::where('status', 'scheduled')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', $now)
                ->with('list.subscribers')
                ->lockForUpdate() 
                ->get();

            if ($campaigns->isEmpty()) {
                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'No scheduled campaigns found for this minute',
                    'time'    => $now->toDateTimeString(),
                ]);
            }

            $campaignCount = 0;
            $mailCount     = 0;

            foreach ($campaigns as $campaign) {

                // Lock campaign so it won’t run again
                $campaign->update([
                    'status'     => 'sending',
                    'started_at' => now(),
                ]);

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
                    );

                    $mailCount++;
                }

                $campaignCount++;
            }

            DB::commit();

            Log::info('Campaign cron executed successfully', [
                'campaigns_processed' => $campaignCount,
                'mails_dispatched'    => $mailCount,
                'time'                => $now->toDateTimeString(),
            ]);

            return response()->json([
                'status'          => true,
                'message'         => 'Scheduled campaigns processed successfully',
                'campaigns_found' => $campaignCount,
                'mails_dispatched'=> $mailCount,
                'time'            => $now->toDateTimeString(),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Campaign cron failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Campaign cron failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
