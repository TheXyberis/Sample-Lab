@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h1 class="text-primary mb-3">Audit Trail</h1>
        <p class="text-muted mb-4">System activity and change history</p>

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label">Entity Type</label>
                            <select name="entity_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('audit.index') }}" class="btn btn-outline-primary w-100 mt-2">
                                Clear
                            </a>
                        </div>

                    </div>
                </form>


                @if($auditLogs->count() > 0)

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Entity</th>
                                    <th>Entity ID</th>
                                    <th>Action</th>
                                    <th>Changes</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($auditLogs as $log)
                                    <tr>

                                        <td>
                                            <small class="text-primary">
                                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                            </small>
                                            <br>
                                            <span class="text-muted">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($log->user)
                                                <strong class="text-primary">{{ $log->user->name }}</strong><br>
                                                <small class="text-muted">{{ $log->user->email }}</small>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-info text-uppercase">
                                                {{ $log->entity_type }}
                                            </span>
                                        </td>

                                        <td>
                                            <code class="text-primary">
                                                    {{ $log->entity_id }}
                                                </code>
                                        </td>

                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action === 'create' ? 'success' : ($log->action === 'delete' ? 'danger' : 'primary') }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>

                                        <td>

                                            @if($log->diff_json)

                                                @php
                                                    $diff = is_array($log->diff_json) ? $log->diff_json : json_decode($log->diff_json, true);
                                                    $summary = '';

                                                    if ($log->action === 'create' && is_array($diff)) {
                                                        $summary = 'Created: ' . implode(', ', array_keys($diff));
                                                    } elseif ($log->action === 'update' && is_array($diff)) {

                                                        $changes = [];

                                                        foreach ($diff as $key => $change) {
                                                            if (is_array($change) && isset($change['old'], $change['new'])) {
                                                                $changes[] = $key;
                                                            }
                                                        }

                                                        $summary = 'Updated: ' . implode(', ', $changes);
                                                    } else {
                                                        $summary = is_string($diff) ? $diff : json_encode($diff);
                                                    }
                                                @endphp

                                                <small class="text-muted">
                                                    {{ Str::limit($summary, 100) }}
                                                </small>

                                                <br>

                                                <a href="{{ route('audit.show', $log->id) }}"
                                                    class="btn btn-sm btn-outline-primary mt-1">
                                                    View Details
                                                </a>

                                            @else
                                                <span class="text-muted">No details</span>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>


                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <div class="text-muted">
                            Showing {{ $auditLogs->firstItem() }} to {{ $auditLogs->lastItem() }} of {{ $auditLogs->total() }}
                            entries
                        </div>

                        {{ $auditLogs->links() }}

                    </div>

                @else

                    <div class="text-center py-5">

                        <i class="fas fa-history fa-3x text-secondary mb-3"></i>

                        <h5 class="text-primary">No audit logs found</h5>

                        <p class="text-muted">
                            No activity matches your current filters.
                        </p>

                        <a href="{{ route('audit.index') }}" class="btn btn-outline-primary mt-2">
                            Clear Filters
                        </a>

                    </div>

                @endif

            </div>
        </div>

    </div>
@endsection