@extends('layouts.app')

@section('title', 'Create Template')

@section('content')
<h2 class="mb-4">Create Email Template</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('templates.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Template Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
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
                                  id="html_content" name="html_content" rows="12" required>{{ old('html_content') }}</textarea>
                        @error('html_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Tips</h5>
            </div>
            <div class="card-body small">
                <ul class="list-unstyled">
                    <li>✓ Use template variables for personalization</li>
                    <li>✓ Keep subject lines under 50 characters</li>
                    <li>✓ Use responsive HTML for mobile devices</li>
                    <li>✓ Include unsubscribe link for compliance</li>
                    <li>✓ Test template before sending campaigns</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
