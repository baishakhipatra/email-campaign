@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f5f6fa;
    }
    .login-card {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
</style>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-100 justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.direct_update') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                            @error('email')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Set Password</button>
                            <a href="{{ route('login') }}" class="btn btn-secondary text-decoration-none text-center">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                © {{ date('Y') }} Email-Campaign All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection