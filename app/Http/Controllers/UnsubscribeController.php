<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Http\RedirectResponse;

class UnsubscribeController extends Controller
{
    protected $subscriberService;

    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    /**
     * Unsubscribe via token.
     */
    public function unsubscribe($token): RedirectResponse
    {
        $success = $this->subscriberService->unsubscribe($token);

        if ($success) {
            return redirect('/')
                ->with('success', 'You have been unsubscribed successfully');
        }

        return redirect('/')
            ->with('error', 'Unable to unsubscribe. Invalid token.');
    }
}
