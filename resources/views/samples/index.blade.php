@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Samples</h2>
    <div class="mb-3">
        <a href="{{ route('samples.create') }}" class="btn btn-primary">Create Sample</a>
        <a href="{{ route('samples.import') }}" class="btn btn-secondary">Import CSV</a>
    </div>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name/code">
            </div>
            <div class="col">
                <select name="status" class="form-control">
                    <option value="">All statuses</option>
                    @foreach(['REGISTERED','IN_PROGRESS','COMPLETED','ARCHIVED','DISPOSED'] as $s)
                        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <button class="btn btn-secondary">Filter</button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Sample Code</th>
                <th>Name</th>
                <th>Client</th>
                <th>Project</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($samples as $sample)
            <tr>
                <td>{{ $sample->sample_code }}</td>
                <td>{{ $sample->name }}</td>
                <td>{{ $sample->client?->name }}</td>
                <td>{{ $sample->project?->name }}</td>
                <td>{{ $sample->status }}</td>
                <td>{{ $sample->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('samples.show', $sample->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $samples->withQueryString()->links() }}
</div>
@endsection