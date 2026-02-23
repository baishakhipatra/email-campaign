<?php

namespace App\Http\Controllers;

use App\Models\SubscribersList;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SubscribersListController extends Controller
{
    public function index(): View
    {
        $lists = SubscribersList::withCount('subscribers')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('lists.index', ['lists' => $lists]);
    }

    public function create(): View
    {
        return view('lists.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list = SubscribersList::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return redirect()->route('lists.show', $list)
            ->with('success', 'List created successfully');
    }

    public function show(SubscribersList $list): View
    {
        $subscribers = $list->subscribers()
            ->paginate(25);

        return view('lists.show', [
            'list' => $list,
            'subscribers' => $subscribers,
            'subscriberCount' => $list->subscribers()->count(),
        ]);
    }

    public function edit(SubscribersList $list): View
    {
        return view('lists.edit', ['list' => $list]);
    }

    public function update(Request $request, SubscribersList $list): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list->update($validated);

        return redirect()->route('lists.show', $list)
            ->with('success', 'List updated successfully');
    }

    public function toggleStatus(SubscribersList $list)
    {
        $list->is_active = $list->is_active ? 0 : 1;
        $list->save();

        return redirect()->back()->with(
            'success',
            $list->is_active
                ? 'Subscriber list activated successfully'
                : 'Subscriber list deactivated successfully'
        );
    }

    public function destroy(SubscribersList $list): RedirectResponse
    {
        $list->delete();

        return redirect()->route('lists.index')
            ->with('success', 'List deleted successfully');
    }
}
