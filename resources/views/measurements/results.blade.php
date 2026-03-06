@extends('layouts.app')

@section('content')
<h2>Measurement #{{ $measurement->id }} Results</h2>

<form id="resultsForm" data-measurement-id="{{ $measurement->id }}">
    <table class="table">
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
                <th>Flags</th>
            </tr>
        </thead>
        <tbody>
        @foreach($schema as $field)
            @php $fieldKey = $field['key'] ?? 'unknown'; @endphp
            <tr>
                <td>{{ $field['label'] ?? $fieldKey }}</td>
                <td>
                    @if($field['type'] === 'number')
                        <input type="number" name="results[{{ $fieldKey }}]" value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}">
                    @elseif($field['type'] === 'text')
                        <input type="text" name="results[{{ $fieldKey }}]" value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}">
                    @elseif($field['type'] === 'date')
                        <input type="date" name="results[{{ $fieldKey }}]" value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}">
                    @elseif($field['type'] === 'file')
                        <input type="file" name="results[{{ $fieldKey }}]">
                    @endif
                </td>
                <td>
                    {{ $flags[$fieldKey]['OUT_OF_RANGE'] ?? '' }}
                    {{ $flags[$fieldKey]['SUSPECT'] ?? '' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <button type="button" id="saveDraftBtn" class="btn btn-secondary">Save Draft</button>
    <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
</form>
@endsection

@section('scripts')
<script src="{{ asset('js/results.js') }}"></script>
@endsection