@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-3">{{ isset($sample) ? 'Edit Sample' : 'Create Sample' }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
    <form method="POST" action="{{ isset($sample) ? route('samples.update', $sample->id) : route('samples.store') }}">
        @csrf
        @if(isset($sample)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $sample->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Type <span class="text-danger">*</span></label>
            <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $sample->type ?? '') }}" required>
            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Client</label> 
            <select name="client_id" class="form-select"> 
                <option value="">Select Client</option> 
                @foreach(App\Models\Client::all() as $client) 
                    <option value="{{ $client->id }}" {{ isset($sample) && $sample->client_id==$client->id ? 'selected':'' }}>{{ $client->name }}</option> 
                @endforeach 
            </select> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Project</label> 
            <select name="project_id" class="form-select"> 
                <option value="">Select Project</option> 
                @foreach(App\Models\Project::all() as $project) 
                    <option value="{{ $project->id }}" {{ isset($sample) && $sample->project_id==$project->id ? 'selected':'' }}>{{ $project->name }}</option> 
                @endforeach 
            </select> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Quantity</label> 
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $sample->quantity ?? '') }}"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Unit</label> 
            <input type="text" name="unit" class="form-control" value="{{ old('unit', $sample->unit ?? '') }}"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Collected At</label> 
            <input type="date" name="collected_at" class="form-control" value="{{ old('collected_at', isset($sample) ? $sample->collected_at?->format('Y-m-d') : '') }}"> 
        </div> 
 
        <div class="mb-3"> 
            <label class="form-label">Metadata JSON</label> 
            <textarea name="metadata_json" class="form-control" rows="4">{{ old('metadata_json', isset($sample) ? json_encode($sample->metadata_json, JSON_PRETTY_PRINT) : '') }}</textarea> 
        </div> 
 
        <button type="submit" class="btn btn-primary">{{ isset($sample) ? 'Update' : 'Create' }}</button>
        <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
        </div>
    </div>
</div>
@endsection