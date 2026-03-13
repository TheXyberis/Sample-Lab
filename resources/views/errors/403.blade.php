@extends('layouts.app')
@section('content')
<div class="container text-center py-5">
    <h1 class="display-1 text-danger">403</h1>
    <p class="lead">Access denied. You do not have permission to access this page.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
</div>
@endsection