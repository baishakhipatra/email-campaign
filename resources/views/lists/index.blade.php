@extends('layouts.app')

@section('title', 'Subscriber Lists')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscriber Lists</h2>
    <a href="{{ route('lists.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> New List
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>List Name</th>
                    <th>Description</th>
                    <th>Subscribers</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lists as $list)
                    <tr>
                        <td>
                            <a href="{{ route('lists.show', $list) }}" class="text-decoration-none">
                                {{ ucwords($list->name) }}
                            </a>
                        </td>
                        <td>{{ Str::limit(ucwords($list->description), 50) }}</td>
                        <td>
                            <span class="badge bg-info">{{ $list->subscribers_count ?? 0 }}</span>
                        </td>
                        <td>
                            <form action="{{ route('subscriber-lists.toggle-status', $list) }}"
                                method="POST"
                                class="d-inline">
                                @csrf
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="toggle-{{ $list->id }}"
                                        {{ $list->is_active ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                </div>
                            </form>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('lists.show', $list) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('lists.edit', $list) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('subscribers.export', $list) }}" class="btn btn-sm btn-outline-info" title="Export">
                                    <i class="bx bx-export"></i>
                                </a>
                                <form action="{{ route('lists.destroy', $list) }}"
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
                        <td colspan="5" class="text-center text-muted py-4">
                            No lists found. <a href="{{ route('lists.create') }}">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $lists->links() }}
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This list will be permanently deleted!",
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

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.toggle-status').forEach(toggle => {

            toggle.addEventListener('change', function () {

                let checkbox = this;
                let url = this.dataset.url;
                let badge = this.closest('td').querySelector('.status-badge');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {
                        badge.textContent = data.status ? 'Active' : 'Inactive';
                        badge.classList.toggle('bg-success', data.status);
                        badge.classList.toggle('bg-secondary', !data.status);
                    } else {
                        checkbox.checked = !checkbox.checked;
                    }

                })
                .catch(() => {
                    checkbox.checked = !checkbox.checked;
                });

            });

        });

    });
</script>
