@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<h2 class="mb-4">Edit Email Template</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('templates.update', $template) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Template Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $template->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="html_content" class="form-label">HTML Content *</label>
                        <div class="alert alert-info text-sm">
                            <strong>Available Variables:</strong> @{{name}}, @{{email}}, @{{first_name}}, @{{last_name}}, @{{unsubscribe_link}}
                        </div>
                        <textarea class="form-control @error('html_content') is-invalid @enderror" 
                                  id="html_content" name="html_content" rows="12" required>{{ old('html_content', $template->html_content) }}</textarea>
                        @error('html_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('templates.show', $template) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
