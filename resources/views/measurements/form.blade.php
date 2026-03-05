@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($measurement) ? 'Edit' : 'Create' }} Measurement</h1>

    <form method="POST" action="{{ isset($measurement) ? route('measurements.update', $measurement->id) : route('measurements.store') }}">
        @csrf
        @if(isset($measurement)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Sample</label>
            <select name="sample_id" class="form-control" required>
                @foreach($samples as $s)
                    <option value="{{ $s->id }}" {{ (isset($measurement) && $measurement->sample_id==$s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Method</label>
            <select name="method_id" class="form-control" required>
                @foreach($methods as $m)
                    <option value="{{ $m->id }}" {{ (isset($measurement) && $measurement->method_id==$m->id) ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Planned At</label>
            <input type="datetime-local" name="planned_at" class="form-control" 
            value="{{ isset($measurement) ? date('Y-m-d\TH:i', strtotime($measurement->planned_at)) : '' }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Priority</label>
            <input type="number" name="priority" class="form-control" value="{{ $measurement->priority ?? 1 }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ $measurement->notes ?? '' }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection