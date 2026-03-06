@extends('layouts.app')

@section('content')

<div class="container">

<h2>Sample details</h2>

<div class="card mt-3">
<div class="card-body">

<p><strong>Sample code:</strong> {{ $sample->sample_code }}</p>
<p><strong>Name:</strong> {{ $sample->name }}</p>
<p><strong>Status:</strong> {{ $sample->status }}</p>
<p><strong>Client:</strong> {{ $sample->client }}</p>
<p><strong>Project:</strong> {{ $sample->project }}</p>

<div class="mt-4">
    <strong>Barcode:</strong>
    <br>

    <img src="/samples/{{ $sample->id }}/barcode" alt="Barcode">

</div>

<div class="mt-4">

    <a href="/samples/{{ $sample->id }}/label" 
       class="btn btn-primary"
       target="_blank">
       Print label
    </a>

</div>

</div>
</div>

</div>

@endsection