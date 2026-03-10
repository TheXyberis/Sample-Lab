@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-3">{{ isset($measurement) ? 'Edit' : 'Create' }} Measurement</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
    <form method="POST" action="{{ isset($measurement) ? route('measurements.update', $measurement->id) : route('measurements.store') }}">
        @csrf
        @if(isset($measurement)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Sample <span class="text-danger">*</span></label>
            <select name="sample_id" class="form-select @error('sample_id') is-invalid @enderror" required>
                <option value="">Select sample</option>
                @foreach($samples as $s)
                    <option value="{{ $s->id }}" {{ old('sample_id', $measurement->sample_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->sample_code }} - {{ $s->name }}</option>
                @endforeach
            </select>
            @error('sample_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Method <span class="text-danger">*</span></label>
            <select name="method_id" class="form-select @error('method_id') is-invalid @enderror" required>
                <option value="">Select method</option>
                @foreach($methods as $m)
                    <option value="{{ $m->id }}" {{ old('method_id', $measurement->method_id ?? '') == $m->id ? 'selected' : '' }}>{{ $m->name }} (v{{ $m->version }})</option>
                @endforeach
            </select>
            @error('method_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Planned At <span class="text-danger">*</span></label>
            <input type="datetime-local" name="planned_at" class="form-control @error('planned_at') is-invalid @enderror" required
                value="{{ old('planned_at', isset($measurement) && $measurement->planned_at ? $measurement->planned_at->format('Y-m-d\TH:i') : '') }}">
            @error('planned_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Priority</label>
            <input type="number" name="priority" class="form-control" value="{{ old('priority', $measurement->priority ?? 1) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes', $measurement->notes ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($measurement) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('measurements.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
        </div>
    </div>
</div>
@endsection