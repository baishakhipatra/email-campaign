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

                    <!-- Title -->
                    <div class="text-center mb-4">
                        <h5 class="fw-semibold mb-1">Login</h5>
                        <p class="text-muted small mb-0">
                            Enter your credentials to continue
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Enter email"
                                   required autofocus>

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="Enter password"
                                   required>

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                © {{ date('Y') }} All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection