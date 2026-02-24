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
                <form action="{{ route('campaigns.send', $campaign) }}" method="POST" class="send-campaign-form d-inline" style="display: inline;">
                    @csrf
                    <button type="button" class="btn btn-success btn-send-campaign" title="Send Campaign">
                        <i class="bx bx-send"></i> Send Campaign Now
                    </button>
                </form>

                <form action="{{ route('campaigns.destroy', $campaign) }}"
                    method="POST"
                    class="d-inline delete-form">
                    @csrf
                    @method('DELETE')

                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-delete"
                            title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This campaign will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });
        });

        document.querySelectorAll('.btn-send-campaign').forEach(button => {
            button.addEventListener('click', function () {

                const form = this.closest('.send-campaign-form');

                Swal.fire({
                    title: 'Send Campaign?',
                    text: "Are you sure you want to send this campaign now?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });
        });
    });
</script>
