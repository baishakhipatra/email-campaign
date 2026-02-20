@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ ucwords($campaign->name) }}</h2>
    <div>
        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back"></i>
        </a>
        @if($campaign->isDraft())
            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-warning">Edit</a>
        @endif
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <h3>{{ ucwords($campaign->status) }}</h3>
            <p>Status</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <h3>{{ $campaign->sent_count }}</h3>
            <p>Sent</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <h3>{{ $campaign->open_count }}</h3>
            <p>Opened</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <h3>{{ $campaign->click_count }}</h3>
            <p>Clicked</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Campaign Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Subject:</strong></td>
                        <td>{{ ucwords($campaign->subject) }}</td>
                    </tr>
                    <tr>
                        <td><strong>From:</strong></td>
                        <td>{{ ucwords($campaign->from_name) }} ({{ $campaign->from_email }})</td>
                    </tr>
                    <tr>
                        <td><strong>Template:</strong></td>
                        <td>{{ ucwords($campaign->template->name) }}</td>
                    </tr>
                    <tr>
                        <td><strong>List:</strong></td>
                        <td>{{ ucwords($campaign->list->name) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $campaign->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Performance</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Open Rate</strong>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ $analytics['open_rate'] }}%">
                            {{ $analytics['open_rate'] }}%
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Click Rate</strong>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $analytics['click_rate'] }}%">
                            {{ $analytics['click_rate'] }}%
                        </div>
                    </div>
                </div>

                <div class="text-muted small">
                    <p>Total Recipients: {{ $analytics['total_recipients'] }}</p>
                    <p>Successfully Sent: {{ $analytics['sent'] }}</p>
                    <p>Failed: {{ $analytics['failed'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($campaign->isDraft())
    <div class="card">
        <div class="card-body">
            <form action="{{ route('campaigns.send', $campaign) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Send this campaign now?')">
                    <i class="bx bx-send"></i> Send Campaign Now
                </button>
            </form>

            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" style="display: inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this campaign?')">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
@endif
@endsection
