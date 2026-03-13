@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Sample Label: {{ $sample->sample_code }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="border border-2 border-dark p-4" style="width: 300px; margin: 0 auto;">
                                <h6 class="fw-bold mb-2">{{ $sample->client->name ?? 'No Client' }}</h6>
                                <h5 class="fw-bold mb-2">{{ $sample->sample_code }}</h5>
                                <p class="mb-2 small">{{ $sample->name }}</p>
                                <p class="mb-2 small">{{ $sample->type }}</p>
                                @if($sample->quantity)
                                    <p class="mb-2 small">Qty: {{ $sample->quantity }} {{ $sample->unit }}</p>
                                @endif
                                <div class="my-3">
                                    <img src="data:image/svg+xml;base64,{{ base64_encode($qrSvg) }}" alt="QR Code" style="width: 150px; height: 150px;">
                                </div>
                                @if(!empty($code128Png))
                                <div class="my-2">
                                    <img src="data:image/png;base64,{{ $code128Png }}" alt="Code128" style="height: 40px; max-width: 100%;">
                                </div>
                                @endif
                                <p class="mb-0 small text-muted">{{ $sample->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button onclick="window.print()" class="btn btn-primary me-2">
                            <i class="fas fa-print"></i> Print Label
                        </button>
                        <a href="{{ route('samples.show', $sample->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sample
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: none !important;
    }
    
    .card-header {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endsection
