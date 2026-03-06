@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $method->name }} (v{{ $method->version }})</h1>
    <p>Status: {{ $method->status }}</p>
    <p>Schema: {{ json_encode($method->schema_json) }}</p>
    <p>Limits: {{ json_encode($method->limits_json) }}</p>
    <a href="{{ route('methods.edit', $method->id) }}" class="btn btn-warning">Edit</a>
</div>
@endsection