@extends('layouts.app') 
@section('content') 
<div class="container mt-4"> 
 
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <h2 class="text-primary fw-bold">Sample Details: {{ $sample->sample_code }}</h2> 
        <div> 
            <a href="{{ route('samples.label', $sample->id) }}" class="btn btn-success me-2" target="_blank"> 
                <i class="fas fa-qrcode"></i> Print Label 
            </a> 
            <a href="{{ route('samples.barcode', $sample->id) }}" class="btn btn-info me-2" download> 
                <i class="fas fa-download"></i> Download QR 
            </a> 
            @if(Auth::user()->hasRole(['Admin', 'Manager', 'Laborant']))
                <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-primary me-2"> 
                    <i class="fas fa-edit"></i> Edit 
                </a> 
            @endif
            <a href="{{ route('samples.index') }}" class="btn btn-secondary"> 
                <i class="fas fa-arrow-left"></i> Back 
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
                <span class="badge bg-{{ $sample->status_badge }} fs-6">{{ $sample->status }}</span>
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
 
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-primary h-100 p-3">
                <h6 class="text-muted">QR Code</h6>
                <div class="text-center">
                    <img src="{{ route('samples.barcode', $sample->id) }}" alt="QR Code" class="img-thumbnail" style="max-width:150px;">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-info h-100 p-3">
                <h6 class="text-muted">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('samples.label', $sample->id) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Print Label
                    </a>
                    <a href="{{ route('samples.barcode', $sample->id) }}" class="btn btn-info btn-sm" download>
                        <i class="fas fa-download"></i> Download QR
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($sample->measurements && $sample->measurements->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header"><h5 class="mb-0">Measurements</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Method</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($sample->measurements as $m)
                    <tr>
                        <td>{{ $m->method?->name }}</td>
                        <td><span class="badge bg-secondary">{{ $m->status }}</span></td>
                        <td><a href="{{ route('results.page', $m->id) }}" class="btn btn-sm btn-outline-info">Results</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection