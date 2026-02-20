@extends('layouts.app')

@section('title', 'Edit List')

@section('content')
<h2 class="mb-4">Edit Subscriber List</h2>


<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('lists.update', $list) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">List Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', ucwords($list->name)) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', ucwords($list->description)) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('lists.show', $list) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update List</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
