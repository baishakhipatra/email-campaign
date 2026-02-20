@extends('layouts.app')

@section('title', 'Import Subscribers')

@section('content')
<h2 class="mb-4">Import Subscribers from CSV</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('subscribers.importStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="alert alert-info">
                        <strong>CSV Format Required:</strong><br>
                        Your CSV file must have columns: <code>email</code>, <code>name</code> (optional)<br>
                        Additional columns will be stored as custom fields.
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">CSV File *</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                               id="file" name="file" accept=".csv,.txt" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="list_id" class="form-label">Add to List</label>
                        <select class="form-select @error('list_id') is-invalid @enderror" 
                                id="list_id" name="list_id">
                            <option value="">Select a list (optional)</option>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('subscribers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Example CSV</h5>
            </div>
            <div class="card-body">
                <pre style="font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px;">email,name,company
john@example.com,John Doe,Acme Corp
jane@example.com,Jane Smith,Tech Inc</pre>
            </div>
        </div>
    </div>
</div>
@endsection
