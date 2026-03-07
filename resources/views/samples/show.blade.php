@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Sample Details: {{ $sample->sample_code }}</h2>

    <div class="mb-3">
        <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('samples.index') }}" class="btn btn-secondary">Back to list</a>
    </div>

    <table class="table table-bordered">
        <tr><th>Sample Code</th><td>{{ $sample->sample_code }}</td></tr>
        <tr><th>Name</th><td>{{ $sample->name }}</td></tr>
        <tr><th>Type</th><td>{{ $sample->type }}</td></tr>
        <tr><th>Client</th><td>{{ $sample->client?->name }}</td></tr>
        <tr><th>Project</th><td>{{ $sample->project?->name }}</td></tr>
        <tr><th>Status</th><td>{{ $sample->status }}</td></tr>
        <tr><th>Quantity</th><td>{{ $sample->quantity }} {{ $sample->unit }}</td></tr>
        <tr><th>Collected At</th><td>{{ $sample->collected_at?->format('Y-m-d') }}</td></tr>
        <tr><th>Metadata JSON</th><td><pre>{{ json_encode($sample->metadata_json, JSON_PRETTY_PRINT) }}</pre></td></tr>
    </table>

    @if($sample->qr_value)
        <h5>QR Code</h5>
        <img src="{{ asset($sample->qr_value) }}" alt="QR Code" style="max-width:150px;">
    @endif
</div>
@endsection