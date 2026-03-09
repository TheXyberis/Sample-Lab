@extends('layouts.app') 
 
@section('content') 
<div class="container-fluid mt-5"> 
    <h1 class="mb-4 text-primary">Dashboard</h1> 
    <p class="lead">Hello, <strong>{{ Auth::user()->name }}</strong>! Role: <span class="badge bg-secondary">{{ Auth::user()->role }}</span></p> 
 
    <div class="row g-4"> 
 
        <div class="col-12 col-md-4 d-flex"> 
            <div class="card shadow-sm border-primary text-center flex-fill"> 
                <div class="card-body"> 
                    <h5 class="card-title text-muted">Samples In Progress</h5> 
                    <h2 class="display-5 text-primary">{{ \App\Models\Sample::where('status','IN_PROGRESS')->count() }}</h2> 
                </div> 
            </div> 
        </div> 
 
        <div class="col-12 col-md-4 d-flex"> 
            <div class="card shadow-sm border-warning text-center flex-fill"> 
                <div class="card-body"> 
                    <h5 class="card-title text-muted">To Verify</h5> 
                    <h2 class="display-5 text-warning">{{ \App\Models\Measurement::where('status','PENDING_REVIEW')->count() }}</h2> 
                </div> 
            </div> 
        </div> 
 
        <div class="col-12 col-md-4 d-flex"> 
            <div class="card shadow-sm border-danger text-center flex-fill"> 
                <div class="card-body"> 
                    <h5 class="card-title text-muted">SLA Breaches</h5> 
                    <h2 class="display-5 text-danger"> 
                        {{ \App\Models\Measurement::where('planned_at','<',now())->where('status','<','FINISHED')->count() }} 
                    </h2> 
                </div> 
            </div> 
        </div> 
 
    </div> 
</div> 
@endsection