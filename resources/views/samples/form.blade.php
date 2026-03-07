@extends('layouts.app')
@section('content')
<div class="container">
    <h2>{{ isset($sample) ? 'Edit Sample' : 'Create Sample' }}</h2>

    <form method="POST" action="{{ isset($sample) ? route('samples.update', $sample->id) : route('samples.store') }}">
        @csrf
        @if(isset($sample))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $sample->name ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <input type="text" name="type" class="form-control" value="{{ old('type', $sample->type ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control">
                <option value="">Select Client</option>
                @foreach(App\Models\Client::all() as $client)
                    <option value="{{ $client->id }}" {{ isset($sample) && $sample->client_id==$client->id ? 'selected':'' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-control">
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
            <input type="date" name="collected_at" class="form-control" value="{{ old('collected_at', isset($sample)?$sample->collected_at?->format('Y-m-d'):null:'') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Metadata JSON</label>
            <textarea name="metadata_json" class="form-control" rows="4">{{ old('metadata_json', isset($sample)?json_encode($sample->metadata_json, JSON_PRETTY_PRINT):'') }}</textarea>
        </div>

        <button class="btn btn-success">{{ isset($sample) ? 'Update' : 'Create' }}</button>
    </form>
</div>
@endsection