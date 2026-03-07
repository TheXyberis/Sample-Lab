@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Measurements</h1>
    <a href="{{ route('measurements.create') }}" class="btn btn-primary mb-3">New Measurement</a>
    <table class="table">
        <thead>
            <tr>
            <th>ID</th><th>Sample</th><th>Method</th><th>Assignee</th><th>Status</th><th>Planned At</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($measurements as $m)
            <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->sample->name }}</td>
                <td>{{ $m->method->name }}</td>
                <td>{{ $m->assignee->name ?? '-' }}</td>
                <td>{{ $m->status }}</td>
                <td>{{ $m->planned_at }}</td>
                <td>
                <a href="{{ route('measurements.edit',$m->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <a href="{{ route('results.page',$m->id) }}" class="btn btn-sm btn-info">Results</a>
                <form method="POST" action="{{ route('measurements.start',$m->id) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success">Start</button>
                </form>
                <form method="POST" action="{{ route('measurements.finish',$m->id) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-primary">Finish</button>
                </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $measurements->links() }}
</div>
@endsection