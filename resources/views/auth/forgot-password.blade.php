@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    
    <div class="card shadow-lg border-0" style="width: 420px;">
        <div class="card-body p-4">
            
            <h3 class="text-center mb-4">Reset Password</h3>
            <p class="text-center text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

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

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

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

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
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
