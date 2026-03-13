@extends('layouts.app') 
 
@section('content') 
<div class="container mt-4"> 
    <div class="d-flex justify-content-between align-items-center mb-3"> 
        <h2 class="text-primary mb-0">Methods</h2> 
        <a href="{{ route('methods.create') }}" class="btn btn-primary">Create Method</a> 
    </div> 
 
    <div class="card shadow-sm"> 
        <div class="table-responsive"> 
            <table class="table table-hover align-middle mb-0"> 
                <thead class="table-light"> 
                    <tr> 
                        <th>ID</th> 
                        <th>Name</th> 
                        <th>Version</th> 
                        <th>Status</th> 
                        <th class="text-end">Actions</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    @forelse($methods as $method) 
                    <tr> 
                        <td>{{ $method->id }}</td> 
                        <td>{{ $method->name }}</td> 
                        <td>{{ $method->version }}</td> 
                        <td> 
                            @php 
                                $map = ['DRAFT'=>'secondary','PUBLISHED'=>'success','ARCHIVED'=>'dark']; 
                                $badge = $map[$method->status] ?? 'secondary'; 
                            @endphp 
                            <span class="badge bg-{{ $badge }}">{{ $method->status }}</span> 
                        </td> 
                        <td class="text-end"> 
                            <a href="{{ route('methods.show', $method->id) }}" class="btn btn-sm btn-outline-primary me-1">View</a> 
                            <a href="{{ route('methods.edit', $method->id) }}" class="btn btn-sm btn-outline-warning me-1">Edit</a> 
                            <form action="{{ route('methods.publish', $method->id) }}" method="POST" style="display:inline;"> 
                                @csrf 
                                <button type="submit" class="btn btn-sm btn-outline-success">Publish</button> 
                            </form> 
                        </td> 
                    </tr> 
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-vial fa-2x mb-2 d-block"></i>
                            No methods found. <a href="{{ route('methods.create') }}">Create a new method</a> to define analysis procedures.
                        </td>
                    </tr> 
                    @endforelse 
                </tbody> 
            </table> 
        </div> 
 
        <div class="card-footer d-flex justify-content-end"> 
            {{ $methods->links() }} 
        </div> 
    </div> 
</div> 
@endsection