@extends('layouts.app')
@section('content')
<h1>Dashboard</h1>
<p>Witaj, {{ Auth::user()->name }}! Role: {{ Auth::user()->getRoleNames()->first() }}</p>

<div class="row mt-4">
    <div class="col-md-3"><div class="card text-center"><div class="card-body">
        <h5>Samples In Progress</h5>
        <h3>{{ \App\Models\Sample::where('status','IN_PROGRESS')->count() }}</h3>
    </div></div></div>

    <div class="col-md-3"><div class="card text-center"><div class="card-body">
        <h5>To Verify</h5>
        <h3>{{ \App\Models\Measurement::where('status','PENDING_REVIEW')->count() }}</h3>
    </div></div></div>

    <div class="col-md-3"><div class="card text-center"><div class="card-body">
        <h5>SLA Breaches</h5>
        <h3>{{ \App\Models\Measurement::where('planned_at','<',now())->where('status','<','FINISHED')->count() }}</h3>
    </div></div></div>
</div>
@endsection