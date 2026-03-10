@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-primary mb-3">{{ isset($method) ? 'Edit Method' : 'Create Method' }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
    <form method="POST" action="{{ isset($method) ? route('methods.update', $method->id) : route('methods.store') }}">
        @csrf
        @if(isset($method)) @method('PUT') @endif

        <div class="mb-3">
            <label for="name" class="form-label">Method Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $method->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="schema_json" class="form-label">Schema JSON <span class="text-danger">*</span></label>
            <textarea class="form-control @error('schema_json') is-invalid @enderror" name="schema_json" id="schema_json" rows="10" required>{{ old('schema_json', isset($method) ? json_encode($method->schema_json, JSON_PRETTY_PRINT) : '{}') }}</textarea>
            <small class="text-muted">JSON fields: key, type, label, required, unit, options</small>
            @error('schema_json') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="limits_json" class="form-label">Limits JSON</label>
            <textarea class="form-control" name="limits_json" id="limits_json" rows="5">{{ old('limits_json', isset($method) ? json_encode($method->limits_json, JSON_PRETTY_PRINT) : '{}') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary mb-2">Save Draft</button> 
 
        @if(isset($method))
            <form action="{{ route('methods.version', $method->id) }}" method="POST" class="d-inline me-2">
                @csrf
                <button type="submit" class="btn btn-warning" {{ $method->status !== 'DRAFT' ? 'disabled' : '' }}>Create New Version</button>
            </form>
            <form action="{{ route('methods.publish', $method->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" {{ $method->status === 'PUBLISHED' ? 'disabled' : '' }}>Publish</button>
            </form>
        @endif
    </form>
        </div>
    </div>
</div>
@endsection