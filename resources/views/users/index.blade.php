@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Users & Roles</h4>
                    <small class="text-muted">Manage system users and their roles</small>
                </div>
                <div class="card-body">
                    @php $users = \App\Models\User::with([])->orderBy('name')->paginate(20); @endphp
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td><span class="badge bg-secondary">{{ $u->role ?? 'User' }}</span></td>
                                            <td>
                                                <span class="text-muted small">View / Edit (Admin only)</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $users->links() }}
                    @else
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0">No users found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection