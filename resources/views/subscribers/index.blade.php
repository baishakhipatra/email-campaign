@extends('layouts.app')

@section('title', 'Subscribers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscribers</h2>
    <div>
        <a href="{{ route('subscribers.create') }}" class="btn btn-primary">
            <i class="bx bx-user-plus"></i> Add Subscriber
        </a>
        <a href="javascript:void(0)"
            class="btn btn-info"
            data-bs-toggle="modal"
            data-bs-target="#importCsvModal">
            <i class="bx bx-import"></i> Import CSV
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Lists</th>
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
                            @if($subscriber->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($subscriber->status === 'unsubscribed')
                                <span class="badge bg-warning">Unsubscribed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($subscriber->status) }}</span>
                            @endif
                        </td>
                        <td>
                           {{ $subscriber->lists->pluck('name')->map(fn ($name) => ucwords($name))->join(', ') ?: '-' }}
                        </td>
                        <td>{{ $subscriber->subscribed_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="{{ route('subscribers.destroy', $subscriber) }}"
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No subscribers found. <a href="{{ route('subscribers.create') }}">Add one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('subscribers.importStore') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-import"></i> Import Subscribers (CSV)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- CSV File -->
                        <div class="mb-3">
                            <label class="form-label">CSV File <span class="text-danger">*</span></label>
                            <input type="file"
                                name="file"
                                class="form-control"
                                accept=".csv"
                                required>
                            <small class="text-muted">
                                Allowed format: email, name
                            </small>
                        </div>

                        <!-- Subscriber List -->
                        <div class="mb-3">
                            <label class="form-label">Add to List (optional)</label>
                            <select name="list_id" class="form-select">
                                <option value="">-- Select List --</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list->id }}">
                                        {{ ucwords($list->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-upload"></i> Import
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{ $subscribers->links() }}
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This subscriber will be permanently deleted!",
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
