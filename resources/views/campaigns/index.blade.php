@extends('layouts.app')

@section('title', 'Campaigns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Campaigns</h2>
    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> New Campaign
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Campaign Name</th>
                    <th>Subject</th>
                    <th>List</th>
                    <th>Status</th>
                    <th>Recipients</th>
                    <th>Sent</th>
                    <th>Open Rate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr>
                        <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none">
                                {{ ucwords($campaign->name) }}
                            </a>
                        </td>
                        <td>{{ Str::limit(ucwords($campaign->subject), 40) }}</td>
                        <td>{{ ucwords($campaign->list->name) }}</td>
                        <td>
                            @if($campaign->isDraft())
                                <span class="badge bg-warning">Draft</span>
                            @elseif($campaign->isScheduled())
                                <span class="badge bg-info">Scheduled</span>
                            @elseif($campaign->isSending())
                                <span class="badge bg-primary">Sending</span>
                            @else
                                <span class="badge bg-success">Sent</span>
                            @endif
                        </td>
                        <td>{{ $campaign->total_subscribers }}</td>
                        <td>{{ $campaign->sent_count }}</td>
                        <td>{{ $campaign->getOpenRate() }}%</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                                @if($campaign->isDraft())
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </a>
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
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No campaigns found. <a href="{{ route('campaigns.create') }}">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $campaigns->links() }}
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

    });
</script>
