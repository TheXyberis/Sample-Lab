@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Users & Roles Management</h4>
                    <small class="text-white-50">Manage system users and their roles</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $users = \App\Models\User::orderBy('created_at', 'desc')->get();
                                    $roleDescriptions = [
                                        'Admin' => 'Full access + system configuration',
                                        'Manager' => 'Project management, assignments, reports, final approval',
                                        'Laborant' => 'Sample registration, measurements, results entry',
                                        'QC/Reviewer' => 'Verification, rejection, repeat requests, result locking',
                                        'Client' => 'View own samples and download reports'
                                    ];
                                    $roleColors = [
                                        'Admin' => 'danger',
                                        'Manager' => 'success', 
                                        'Laborant' => 'info',
                                        'QC/Reviewer' => 'warning',
                                        'Client' => 'secondary'
                                    ];
                                @endphp
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->id === auth()->id())
                                                        <small class="text-muted">(You)</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->active ?? true)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()->role === 'Admin')
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editUser({{ $user->id }})">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    @if($user->id !== auth()->id())
                                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleUserStatus({{ $user->id }})">
                                                            <i class="fas fa-ban"></i> 
                                                            {{ ($user->active ?? true) ? 'Disable' : 'Enable' }}
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">View only</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Role Descriptions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($roleDescriptions as $role => $description)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <span class="badge bg-{{ $roleColors[$role] ?? 'secondary' }}">
                                                            {{ $role }}
                                                        </span>
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <small class="text-muted">{{ $description }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editUser(userId) {
    // Future implementation: open user edit modal
    alert('User editing feature coming soon! User ID: ' + userId);
}

function toggleUserStatus(userId) {
    if (confirm('Are you sure you want to toggle this user\'s status?')) {
        // Future implementation: AJAX call to toggle user status
        alert('User status toggle feature coming soon! User ID: ' + userId);
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // Future implementation: AJAX call to delete user
        alert('User deletion feature coming soon! User ID: ' + userId);
    }
}
</script>
@endsection