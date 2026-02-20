@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Email Templates</h2>
    <a href="{{ route('templates.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> New Template
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Template Name</th>
                    <th>Description</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td>
                            <a href="{{ route('templates.show', $template) }}" class="text-decoration-none">
                                {{ ucwords($template->name) }}
                            </a>
                        </td>
                        <td>{{ Str::limit(ucwords($template->description), 50) }}</td>
                        <td>{{ $template->creator->name }}</td>
                        <td>
                            @if($template->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $template->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('templates.show', $template) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="{{ route('templates.destroy', $template) }}"
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
                            No templates found. <a href="{{ route('templates.create') }}">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $templates->links() }}
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This template will be permanently deleted!",
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
