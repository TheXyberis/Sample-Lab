@extends('layouts.app') 
 
@section('content') 
<div class="container vh-100 d-flex justify-content-center align-items-center"> 
 
    <div class="card shadow-lg border-0" style="width: 420px;"> 
        <div class="card-body p-4"> 
 
            <h3 class="text-center mb-4">Login</h3> 
 
            <form action="{{ route('login.submit') }}" method="POST"> 
                @csrf 
 
                <div class="mb-3"> 
                    <label class="form-label">Email</label> 
                    <input  
                        type="email"  
                        name="email"  
                        class="form-control @error('email') is-invalid @enderror"  
                        placeholder="Enter your email" 
                        value="{{ old('email') }}"
                        required> 
                    @error('email')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div> 
 
                <div class="mb-3"> 
                    <label class="form-label">Password</label> 
                    <input  
                        type="password"  
                        name="password"  
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Enter your password" 
                        required> 
                    @error('password')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div> 
 
                <div class="d-grid mb-3">
                    <button class="btn btn-primary">
                        Login
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-decoration-none text-muted">
                        <i class="fas fa-question-circle me-1"></i>Forgot Password?
                    </a>
                </div> 
 
            </form> 
 
        </div> 
    </div> 
 
</div> 
@endsection
