@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<h2 class="mb-4">Create Campaign</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('campaigns.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Campaign Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_name" class="form-label">From Name *</label>
                            <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                   id="from_name" name="from_name" value="{{ old('from_name') }}" required>
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="from_email" class="form-label">From Email *</label>
                            <input type="email" class="form-control @error('from_email') is-invalid @enderror" 
                                   id="from_email" name="from_email" value="{{ old('from_email') }}" required>
                            @error('from_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="template_id" class="form-label">Email Template *</label>
                            <select class="form-select @error('template_id') is-invalid @enderror" 
                                    id="template_id" name="template_id" required>
                                <option value="">Select Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>
                                        {{ ucwords($template->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="list_id" class="form-label">Subscriber List *</label>
                            <select class="form-select @error('list_id') is-invalid @enderror" 
                                    id="list_id" name="list_id" required>
                                <option value="">Select List</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list->id }}" @selected(old('list_id') == $list->id)>
                                        {{ $list->name }} ({{ $list->subscribers_count ?? 0 }} subscribers)
                                    </option>
                                @endforeach
                            </select>
                            @error('list_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" @selected(old('status') == 'draft')>Draft</option>
                                <option value="scheduled" @selected(old('status') == 'scheduled')>Schedule for Later</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="scheduled_at" class="form-label">Schedule Date & Time</label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                            <small class="text-muted">Required if status is "Scheduled"</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
