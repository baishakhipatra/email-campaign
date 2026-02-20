@extends('layouts.app')

@section('title', 'Edit Subscriber')

@section('content')
<h2 class="mb-4">Edit Subscriber</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('subscribers.update', $subscriber) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="{{ $subscriber->email }}" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', ucwords($subscriber->name ?? '')) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="active" @selected(old('status', $subscriber->status) === 'active')>Active</option>
                            <option value="unsubscribed" @selected(old('status', $subscriber->status) === 'unsubscribed')>Unsubscribed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lists" class="form-label">Subscriber Lists</label>
                        <select class="form-select select2 @error('lists') is-invalid @enderror" 
                                id="lists" name="lists[]" multiple>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}" 
                                    @if($subscriber->lists->contains($list->id)) selected @endif>
                                    {{ ucwords($list->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('lists')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Subscriber</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

