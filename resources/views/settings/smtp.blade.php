@extends('layouts.app')

@section('title', 'SMTP Settings')

@section('content')
<h2 class="mb-4">SMTP Configuration</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('settings.smtp.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="host" class="form-label">SMTP Host *</label>
                            <input type="text" class="form-control @error('host') is-invalid @enderror" 
                                   id="host" name="host" value="{{ old('host', $setting->host ?? '') }}" required>
                            @error('host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="port" class="form-label">SMTP Port *</label>
                            <input type="number" class="form-control @error('port') is-invalid @enderror" 
                                   id="port" name="port" value="{{ old('port', $setting->port ?? '587') }}" required>
                            @error('port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $setting->username ?? '') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Enter password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="encryption" class="form-label">Encryption *</label>
                            <select class="form-select @error('encryption') is-invalid @enderror" 
                                    id="encryption" name="encryption" required>
                                <option value="tls" @selected(old('encryption', $setting->encryption ?? 'tls') === 'tls')>TLS</option>
                                <option value="ssl" @selected(old('encryption', $setting->encryption ?? '') === 'ssl')>SSL</option>
                                <option value="none" @selected(old('encryption', $setting->encryption ?? '') === 'none')>None</option>
                            </select>
                            @error('encryption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_per_minute" class="form-label">Max Emails per Minute</label>
                            <input type="number" class="form-control @error('max_per_minute') is-invalid @enderror" 
                                   id="max_per_minute" name="max_per_minute" value="{{ old('max_per_minute', $setting->max_per_minute ?? '60') }}">
                            @error('max_per_minute')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    @if($setting)
                        <div class="mb-3">
                            <small class="text-muted">
                                <strong>Last tested:</strong> 
                                {{ $setting->last_tested_at ? $setting->last_tested_at->format('M d, Y H:i') : 'Never' }}
                            </small>
                            @if($setting->test_result !== null)
                                <br>
                                <span class="badge bg-{{ $setting->test_result ? 'success' : 'danger' }}">
                                    {{ $setting->test_result ? 'Test Passed' : 'Test Failed' }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <form action="{{ route('settings.smtp.test') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-info">Test Connection</button>
                        </form>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Popular SMTP Providers</h5>
            </div>
            <div class="card-body small">
                <div class="mb-3">
                    <strong>Gmail</strong>
                    <p class="text-muted mb-2">
                        Host: smtp.gmail.com<br>
                        Port: 587<br>
                        Encryption: TLS
                    </p>
                </div>
                <div class="mb-3">
                    <strong>SendGrid</strong>
                    <p class="text-muted mb-2">
                        Host: smtp.sendgrid.net<br>
                        Port: 587<br>
                        Encryption: TLS
                    </p>
                </div>
                <div>
                    <strong>AWS SES</strong>
                    <p class="text-muted">
                        Host: email-smtp.{region}.amazonaws.com<br>
                        Port: 587<br>
                        Encryption: TLS
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
