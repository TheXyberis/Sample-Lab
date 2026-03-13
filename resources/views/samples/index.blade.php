@extends('layouts.app') 
 
@section('content') 
<div class="container mt-4"> 
 
    <div class="d-flex justify-content-between align-items-center mb-3"> 
        <h2 class="text-primary mb-0">Samples</h2> 
        <div class="btn-group">
            <a href="{{ route('samples.create-wizard') }}" class="btn btn-primary">Create Sample (Wizard)</a>
            <a href="{{ route('samples.create') }}" class="btn btn-outline-primary">Create Sample</a>
            <a href="{{ route('samples.import') }}" class="btn btn-outline-secondary">Import CSV</a>
        </div> 
    </div> 
 
    <form method="GET" class="mb-3"> 
        <div class="row g-2"> 
            <div class="col-md-5"> 
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name/code"> 
            </div> 
            <div class="col-md-3"> 
                <select name="status" class="form-select"> 
                    <option value="">All statuses</option> 
                    @foreach(['REGISTERED','IN_PROGRESS','COMPLETED','ARCHIVED','DISPOSED'] as $s) 
                        <option value="{{ $s }}" {{ request('status')==$s ? 'selected' : '' }}>{{ $s }}</option> 
                    @endforeach 
                </select> 
            </div> 
            <div class="col-md-2"> 
                <button class="btn btn-secondary w-100">Filter</button> 
            </div> 
            <div class="col-md-2 text-end"> 
                @if(request()->query()) 
                    <a href="{{ route('samples.index') }}" class="btn btn-link">Clear</a> 
                @endif 
            </div> 
        </div> 
    </form> 
 
    <div class="card shadow-sm"> 
        <div class="table-responsive"> 
            <table class="table table-hover mb-0 align-middle"> 
                <thead class="table-light"> 
                    <tr> 
                        <th>Sample Code</th> 
                        <th>Name</th> 
                        <th>Client</th> 
                        <th>Project</th> 
                        <th>Status</th> 
                        <th>Created At</th> 
                        <th class="text-end">Actions</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    @forelse($samples as $sample) 
                        <tr> 
                            <td class="fw-medium">{{ $sample->sample_code }}</td> 
                            <td>{{ $sample->name }}</td> 
                            <td>{{ $sample->client?->name ?? '—' }}</td> 
                            <td>{{ $sample->project?->name ?? '—' }}</td> 
                            <td> 
                                @php 
                                    $map = [ 
                                        'REGISTERED' => 'primary', 
                                        'IN_PROGRESS' => 'warning', 
                                        'COMPLETED' => 'success', 
                                        'ARCHIVED' => 'secondary', 
                                        'DISPOSED' => 'dark', 
                                    ]; 
                                    $badge = $map[$sample->status] ?? 'secondary'; 
                                @endphp 
                                <span class="badge bg-{{ $badge }}">{{ $sample->status }}</span> 
                            </td> 
                            <td>{{ $sample->created_at->format('Y-m-d') }}</td> 
                            <td class="text-end"> 
                                <a href="{{ route('samples.show', $sample->id) }}" class="btn btn-sm btn-outline-primary me-1">View</a> 
                                <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-sm btn-outline-warning">Edit</a> 
                            </td> 
                        </tr> 
                    @empty 
                        <tr> 
                            <td colspan="7" class="text-center text-muted py-5">
                                No samples found. <a href="{{ route('samples.create-wizard') }}">Create via Wizard</a> or <a href="{{ route('samples.import') }}">Import CSV</a>. 
                            </td> 
                        </tr>
                        @endforelse 
                </tbody> 
            </table> 
        </div> 
 
        <div class="card-footer d-flex justify-content-between align-items-center"> 
            <div class="text-muted small"> 
                Total: <strong>{{ $samples->total() }}</strong> 
            </div> 
            <div> 
                {{ $samples->withQueryString()->links() }} 
            </div> 
        </div> 
    </div> 
</div> 
@endsection