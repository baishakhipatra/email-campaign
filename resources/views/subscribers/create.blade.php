@extends('layouts.app')

@section('title', 'Add Subscriber')

@section('content')
<h2 class="mb-4">Add Subscriber</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('subscribers.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lists" class="form-label">Subscriber Lists</label>
                        <select class="form-select @error('lists') is-invalid @enderror" 
                                id="lists" name="lists[]" multiple>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}" @selected(in_array($list->id, old('lists', [])))>
                                    {{ $list->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lists')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('subscribers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Subscriber</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
