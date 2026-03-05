@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($method) ? 'Edit Method' : 'Create Method' }}</h1>

    <form method="POST" action="{{ isset($method) ? route('methods.update', $method->id) : route('methods.store') }}">
        @csrf
        @if(isset($method))
            @method('PUT')
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Method Name</label>
            <input type="text" class="form-control" name="name" id="name" 
                   value="{{ $method->name ?? old('name') }}" required>
        </div>

        <!-- schema_json -->
        <div class="mb-3">
            <label for="schema_json" class="form-label">Schema JSON</label>
            <textarea class="form-control" name="schema_json" id="schema_json" rows="10" required>
                {{ isset($method) ? json_encode($method->schema_json, JSON_PRETTY_PRINT) : '{}' }}
            </textarea>
            <small class="text-muted">JSON fields: key, type, label, required, unit, options</small>
        </div>

        <!-- limits_json -->
        <div class="mb-3">
            <label for="limits_json" class="form-label">Limits JSON</label>
            <textarea class="form-control" name="limits_json" id="limits_json" rows="5">
                {{ isset($method) ? json_encode($method->limits_json, JSON_PRETTY_PRINT) : '{}' }}
            </textarea>
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Save Draft</button>

        @if(isset($method))
        <button type="button" class="btn btn-warning" id="btn-version">Create New Version</button>
        <button type="button" class="btn btn-success" id="btn-publish">Publish</button>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    @if(isset($method))
    // Create New Version
    document.getElementById('btn-version').addEventListener('click', function(){
        fetch('/api/methods/{{ $method->id }}/version', {
            method: 'POST',
            headers:{
                'Authorization':'Bearer {{ auth()->user()->currentAccessToken()->plainTextToken ?? '' }}',
                'Content-Type':'application/json'
            }
        }).then(res=>res.json()).then(data=>{
            alert('New version created: '+data.version);
            location.reload();
        });
    });

    // Publish
    document.getElementById('btn-publish').addEventListener('click', function(){
        fetch('/api/methods/{{ $method->id }}/publish', {
            method: 'POST',
            headers:{
                'Authorization':'Bearer {{ auth()->user()->currentAccessToken()->plainTextToken ?? '' }}',
                'Content-Type':'application/json'
            }
        }).then(res=>res.json()).then(data=>{
            alert(data.message);
            location.reload();
        });
    });
    @endif
});
</script>
@endsection