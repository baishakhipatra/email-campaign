@extends('layouts.app')

@section('title', $list->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">{{ ucwords($list->name) }}</h2>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('lists.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i>
        </a>
        <a href="{{ route('lists.edit', $list) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('subscribers.export', $list) }}" class="btn btn-info">
            <i class="bx bx-export"></i> Export
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <h3>{{ $subscriberCount }}</h3>
            <p>Total Subscribers</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Subscribers in this List</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Subscribed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ ucwords($subscriber->name ?? '-') }}</td>
                        <td>
                            <span class="badge bg-{{ $subscriber->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($subscriber->status) }}
                            </span>
                        </td>
                        <td>{{ $subscriber->subscribed_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No subscribers in this list
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $subscribers->links() }}
@endsection
