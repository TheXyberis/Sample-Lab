@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Audit Trail</h4>
                    <small class="text-white-50">System activity and change history</small>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Entity Type</label>
                                <select name="entity_type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($entityTypes as $type)
                                        <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Action</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-1">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Timestamp</th>
                                        <th width="15%">User</th>
                                        <th width="10%">Entity</th>
                                        <th width="10%">Entity ID</th>
                                        <th width="10%">Action</th>
                                        <th width="40%">Changes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $log)
                                        <tr>
                                            <td>
                                                <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small><br>
                                                <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td>
                                                @if($log->user)
                                                    <strong>{{ $log->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $log->user->email }}</small>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-uppercase">{{ $log->entity_type }}</span>
                                            </td>
                                            <td>
                                                <code>{{ $log->entity_id }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->action === 'create' ? 'success' : ($log->action === 'delete' ? 'danger' : 'primary') }} text-uppercase">
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
                                                    <small class="text-muted">{{ Str::limit($summary, 100) }}</small>
                                                    <br>
                                                    <a href="{{ route('audit.show', $log->id) }}" class="btn btn-sm btn-outline-info mt-1">View Details</a>
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
                                Showing {{ $auditLogs->firstItem() }} to {{ $auditLogs->lastItem() }} of {{ $auditLogs->total() }} entries
                            </div>
                            {{ $auditLogs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-history fa-3x"></i>
                            </div>
                            <h5 class="text-muted">No audit logs found</h5>
                            <p class="text-muted">No activity matches your current filters.</p>
                            <a href="{{ route('audit.index') }}" class="btn btn-outline-primary">Clear Filters</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection