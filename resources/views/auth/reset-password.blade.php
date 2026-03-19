@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    
    <div class="card shadow-lg border-0" style="width: 420px;">
        <div class="card-body p-4">
            
            <h3 class="text-center mb-4">Set New Password</h3>
            <p class="text-center text-muted mb-4">Enter your new password below.</p>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        required>
                    @error('email')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Enter new password"
                        required>
                    @error('password')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        placeholder="Confirm new password"
                        required>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>The password confirmation does not match.
                        </div>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock me-2"></i>Reset Password
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
