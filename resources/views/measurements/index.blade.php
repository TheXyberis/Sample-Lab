@extends('layouts.app') 
 
@section('content') 
<div class="container mt-4"> 
    <div class="d-flex justify-content-between align-items-center mb-3"> 
        <h2 class="text-primary mb-0">Measurements</h2> 
        <a href="{{ route('measurements.create') }}" class="btn btn-primary">New Measurement</a> 
    </div> 
 
    <div class="card shadow-sm"> 
        <div class="table-responsive"> 
            <table class="table table-hover align-middle mb-0"> 
                <thead class="table-light"> 
                    <tr> 
                        <th>ID</th> 
                        <th>Sample</th> 
                        <th>Method</th> 
                        <th>Assignee</th> 
                        <th>Status</th> 
                        <th>Planned At</th> 
                        <th class="text-end">Actions</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    @forelse($measurements as $m) 
                    <tr> 
                        <td>{{ $m->id }}</td> 
                        <td>{{ $m->sample?->sample_code ?? '-' }} {{ $m->sample?->name ?? '' }}</td>
                        <td>{{ $m->method?->name ?? '-' }}</td> 
                        <td>{{ $m->assignee?->name ?? '-' }}</td> 
                        <td> 
                            @php 
                                $map = [
                                    'PLANNED' => 'secondary',
                                    'RUNNING' => 'info',
                                    'DONE' => 'success',
                                    'CANCELLED' => 'danger',
                                    'REPEAT_REQUIRED' => 'warning'
                                ]; 
                                $badge = $map[$m->status] ?? 'secondary'; 
                            @endphp 
                            <span class="badge bg-{{ $badge }}">{{ $m->status }}</span> 
                        </td> 
                        <td>{{ $m->planned_at ? $m->planned_at->format('Y-m-d H:i') : '-' }}</td> 
                        <td class="text-end"> 
                            <a href="{{ route('measurements.edit',$m->id) }}" class="btn btn-sm btn-outline-warning me-1">Edit</a> 
                            <a href="{{ route('results.page',$m->id) }}" class="btn btn-sm btn-outline-info me-1">Results</a> 
                            <form method="POST" action="{{ route('measurements.start',$m->id) }}" style="display:inline;"> 
                                @csrf @method('PATCH') 
                                <button type="submit" class="btn btn-sm btn-outline-success me-1">Start</button> 
                            </form> 
                            <form method="POST" action="{{ route('measurements.finish',$m->id) }}" style="display:inline;"> 
                                @csrf @method('PATCH') 
                                <button type="submit" class="btn btn-sm btn-outline-primary">Finish</button> 
                            </form> 
                        </td> 
                    </tr> 
                    @empty 
                    <tr> 
                        <td colspan="7" class="text-center text-muted py-4"> 
                            No measurements found — <a href="{{ route('measurements.create') }}">create a new measurement</a>. 
                        </td> 
                    </tr> 
                    @endforelse 
                </tbody> 
            </table> 
        </div> 
 
        <div class="card-footer d-flex justify-content-end"> 
            {{ $measurements->links() }} 
        </div> 
    </div> 
</div> 
@endsection