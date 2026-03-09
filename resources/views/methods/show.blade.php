@extends('layouts.app') 
 
@section('content') 
<div class="container mt-4"> 
 
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <h2 class="text-primary fw-bold">{{ $method->name }} (v{{ $method->version }})</h2> 
        <a href="{{ route('methods.edit', $method->id) }}" class="btn btn-primary"> 
            <i class="bi bi-pencil-square"></i> Edit 
        </a> 
    </div> 
 
    <div class="row g-3 mb-4"> 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-info p-3 h-100"> 
                <h6 class="text-muted">Status</h6> 
                <p class="fw-semibold">{{ $method->status }}</p> 
            </div> 
        </div> 
 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-primary p-3 h-100"> 
                <h6 class="text-muted">Schema JSON</h6> 
                <pre class="small">{{ json_encode($method->schema_json, JSON_PRETTY_PRINT) }}</pre> 
            </div> 
        </div> 
 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-success p-3 h-100"> 
                <h6 class="text-muted">Limits JSON</h6> 
                <pre class="small">{{ json_encode($method->limits_json, JSON_PRETTY_PRINT) }}</pre> 
            </div> 
        </div> 
    </div> 
 
</div> 
@endsection