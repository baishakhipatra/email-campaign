@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $template->name }}</h2>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i>
        </a>
        <div>
            <a href="{{ route('templates.edit', $template) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Template Preview</h5>
            </div>
            <div class="card-body">
                <div style="border: 1px solid #dee2e6; border-radius: 4px; padding: 20px; min-height: 300px; background: white;">
                    <div class="template-preview">
                        {!! $template->html_content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Template Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $template->name }}</p>
                <p><strong>Slug:</strong> {{ $template->slug }}</p>
                <p><strong>Created By:</strong> {{ $template->creator->name }}</p>
                <p><strong>Status:</strong> 
                    @if($template->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </p>
                <p><strong>Created:</strong> {{ $template->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('templates.edit', $template) }}" class="btn btn-warning d-block mb-2">
                    <i class="bx bx-edit"></i> Edit Template
                </a>
                <form action="{{ route('templates.destroy', $template) }}"
                    method="POST"
                    class="d-inline delete-form">
                    @csrf
                    @method('DELETE')

                    <button type="button"
                            class="btn btn-danger w-100 btn-delete"
                            title="Delete">
                        <i class="bx bx-trash"></i> Delete Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
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
