<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\SubscribersList;
use App\Services\SubscriberService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    protected $subscriberService;

    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    public function index(): View
    {
        $subscribers = Subscriber::with('lists')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $lists = SubscribersList::where('is_active', true)->get();

        return view('subscribers.index', ['subscribers' => $subscribers, 'lists' => $lists]);
    }


    public function create(): View
    {
        $lists = SubscribersList::where('is_active', true)->get();
        return view('subscribers.create', ['lists' => $lists]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'nullable|string|max:255',
            'lists' => 'nullable|array',
            'lists.*' => 'exists:subscribers_lists,id',
        ]);

        $this->subscriberService->createSubscriber($validated);

        return redirect()->route('subscribers.index')
            ->with('success', 'Subscriber created successfully');
    }


    public function show(Subscriber $subscriber): View
    {
        return view('subscribers.show', ['subscriber' => $subscriber]);
    }


    public function edit(Subscriber $subscriber): View
    {
        $lists = SubscribersList::where('is_active', true)->get();
        return view('subscribers.edit', ['subscriber' => $subscriber, 'lists' => $lists]);
    }

    public function update(Request $request, Subscriber $subscriber): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'status' => 'in:active,unsubscribed',
            'lists' => 'nullable|array',
            'lists.*' => 'exists:subscribers_lists,id',
        ]);

        $subscriber->update($validated);
        $subscriber->lists()->sync($validated['lists'] ?? []);

        return redirect()->route('subscribers.show', $subscriber)
            ->with('success', 'Subscriber updated successfully');
    }


    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()->route('subscribers.index')
            ->with('success', 'Subscriber deleted successfully');
    }


    public function import(): View
    {
        $lists = SubscribersList::where('is_active', true)->get();
        return view('subscribers.import', ['lists' => $lists]);
    }


    public function importStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'list_id' => 'nullable|exists:subscribers_lists,id',
        ]);

        $result = $this->subscriberService->importFromCsv(
            $validated['file'],
            $validated['list_id'] ?? null
        );

        $message = "Imported: {$result['imported']}, Skipped: {$result['skipped']}";
        if (!empty($result['errors'])) {
            $message .= "\nErrors: " . implode(', ', array_slice($result['errors'], 0, 5));
        }

        return redirect()->route('subscribers.index')->with('success', $message);
    }

    public function export(SubscribersList $list): StreamedResponse
    {
        $csv = $this->subscriberService->exportToCsv($list);

        return response()->streamDownload(
            fn () => print($csv),
            "subscribers-{$list->slug}.csv",
            ['Content-Type' => 'text/csv']
        );
    }
}
