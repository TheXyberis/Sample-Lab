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
                        class="form-control"  
                        placeholder="Enter your email" 
                        required> 
                </div> 
 
                <div class="mb-3"> 
                    <label class="form-label">Password</label> 
                    <input  
                        type="password"  
                        name="password"  
                        class="form-control" 
                        placeholder="Enter your password" 
                        required> 
                </div> 
 
                <div class="d-grid"> 
                    <button class="btn btn-primary"> 
                        Login 
                    </button> 
                </div> 
 
            </form> 
 
        </div> 
    </div> 
 
</div> 
@endsection