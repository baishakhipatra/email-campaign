@extends('layouts.app')

@section('title', $subscriber->email)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">

        <h5 class="mb-0 fw-semibold">{{ $subscriber->email }}</h5>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('subscribers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back"></i>
        </a>
        <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-warning btn-sm">
            <i class="bx bx-edit"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Subscriber Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $subscriber->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ ucwords($subscriber->name ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $subscriber->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($subscriber->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Subscribed:</strong></td>
                        <td>{{ $subscriber->subscribed_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lists</h5>
            </div>
            <div class="card-body">
                @forelse($subscriber->lists as $list)
                    <span class="badge bg-primary">{{ ucwords($list->name) }}</span>
                @empty
                    <p class="text-muted">Not assigned to any list</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Campaign Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Emails Sent: {{ $subscriber->emailLogs()->count() }}</p>
                <p class="text-muted">Emails Opened: {{ $subscriber->openLogs()->count() }}</p>
                <p class="text-muted">Links Clicked: {{ $subscriber->clickLogs()->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
