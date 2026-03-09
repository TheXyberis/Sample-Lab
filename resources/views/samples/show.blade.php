@extends('layouts.app') 
@section('content') 
<div class="container mt-4"> 
 
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <h2 class="text-primary fw-bold">Sample Details: {{ $sample->sample_code }}</h2> 
        <div> 
            <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-primary me-2"> 
                <i class="bi bi-pencil-square"></i> Edit 
            </a> 
            <a href="{{ route('samples.index') }}" class="btn btn-secondary"> 
                <i class="bi bi-arrow-left-circle"></i> Back 
            </a> 
        </div> 
    </div> 
 
    <div class="row g-3 mb-4"> 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-primary h-100 p-3"> 
                <h6 class="text-muted">Name</h6> 
                <p class="fw-semibold">{{ $sample->name }}</p> 
            </div> 
        </div> 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-info h-100 p-3"> 
                <h6 class="text-muted">Type</h6> 
                <p class="fw-semibold">{{ $sample->type }}</p> 
            </div> 
        </div> 
        <div class="col-md-4"> 
            <div class="card shadow-sm border-success h-100 p-3"> 
                <h6 class="text-muted">Status</h6> 
                <p class="fw-semibold">{{ $sample->status }}</p> 
            </div> 
        </div> 
    </div> 
 
    <div class="table-responsive mb-4"> 
        <table class="table table-striped table-bordered align-middle"> 
            <tbody> 
                <tr><th>Sample Code</th><td>{{ $sample->sample_code }}</td></tr> 
                <tr><th>Client</th><td>{{ $sample->client?->name }}</td></tr> 
                <tr><th>Project</th><td>{{ $sample->project?->name }}</td></tr> 
                <tr><th>Quantity</th><td>{{ $sample->quantity }} {{ $sample->unit }}</td></tr> 
                <tr><th>Collected At</th><td>{{ $sample->collected_at?->format('Y-m-d') }}</td></tr> 
                <tr><th>Metadata JSON</th><td><pre>{{ json_encode($sample->metadata_json, JSON_PRETTY_PRINT) }}</pre></td></tr> 
            </tbody> 
        </table> 
    </div> 
 
    @if($sample->qr_value) 
    <div class="mb-4"> 
        <h5>QR Code</h5> 
        <img src="{{ asset($sample->qr_value) }}" alt="QR Code" class="img-thumbnail" style="max-width:180px;"> 
    </div> 
    @endif 
 
</div> 
@endsection