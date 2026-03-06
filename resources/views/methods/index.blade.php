@extends('layouts.app')

@section('content')
<h1>Methods</h1>
<table class="table">
<thead>
    <tr><th>ID</th><th>Name</th><th>Version</th><th>Status</th><th>Actions</th></tr>
</thead>
<tbody>
    @foreach($methods as $method)
    <tr>
        <td>{{ $method->id }}</td>
        <td>{{ $method->name }}</td>
        <td>{{ $method->version }}</td>
        <td>{{ $method->status }}</td>
        <td>
        <a href="{{ route('methods.show', $method->id) }}" class="btn btn-sm btn-primary">View</a>
        <a href="{{ route('methods.edit', $method->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('methods.publish',$method->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Publish</button>
        </form>
        </td>
    </tr>
    @endforeach
</tbody>
</table>
{{ $methods->links() }}
@endsection