<?php

namespace App\Http\Controllers;

use App\Services\TrackingService;
use Illuminate\Response\Response;

class TrackingController extends Controller
{
    protected $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Track email open.
     */
    public function trackOpen($token): Response
    {
        $this->trackingService->recordOpen($token);

        // Return 1x1 transparent pixel
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Track link click and redirect.
     */
    public function trackClick($token, $emailLogId, $url)
    {
        $redirectUrl = $this->trackingService->recordClick($token, $emailLogId, $url);

        if (!$redirectUrl) {
            return redirect('/');
        }

        return redirect()->away($redirectUrl);
    }
}
