@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Audit Log Details</h4>
                    <small class="text-white-50">Change history for {{ ucfirst($auditLog->entity_type) }} #{{ $auditLog->entity_id }}</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Timestamp:</th>
                                    <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }} ({{ $auditLog->created_at->diffForHumans() }})</td>
                                </tr>
                                <tr>
                                    <th>User:</th>
                                    <td>
                                        @if($auditLog->user)
                                            <strong>{{ $auditLog->user->name }}</strong> ({{ $auditLog->user->email }})
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Entity:</th>
                                    <td>
                                        <span class="badge bg-info text-uppercase">{{ $auditLog->entity_type }}</span>
                                        <code class="ms-2">{{ $auditLog->entity_id }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action:</th>
                                    <td>
                                        <span class="badge bg-{{ $auditLog->action === 'create' ? 'success' : ($auditLog->action === 'delete' ? 'danger' : 'primary') }} text-uppercase">
                                            {{ $auditLog->action }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Change Details</h5>
                            @if(!empty($diff) && is_array($diff))
                                @if($auditLog->action === 'create')
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="30%">Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($diff as $key => $value)
                                                    <tr>
                                                        <td><strong>{{ $key }}</strong></td>
                                                        <td>
                                                            @if(is_array($value))
                                                                <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                            @elseif(is_bool($value))
                                                                <span class="badge bg-{{ $value ? 'success' : 'danger' }}">{{ $value ? 'TRUE' : 'FALSE' }}</span>
                                                            @elseif(is_null($value))
                                                                <span class="text-muted">NULL</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif($auditLog->action === 'update')
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="30%">Field</th>
                                                    <th width="35%">Old Value</th>
                                                    <th width="35%">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($diff as $key => $change)
                                                    @if(is_array($change) && isset($change['old'], $change['new']))
                                                        <tr>
                                                            <td><strong>{{ $key }}</strong></td>
                                                            <td>
                                                                @if(is_array($change['old']))
                                                                    <pre>{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                                                @elseif(is_bool($change['old']))
                                                                    <span class="badge bg-{{ $change['old'] ? 'success' : 'danger' }}">{{ $change['old'] ? 'TRUE' : 'FALSE' }}</span>
                                                                @elseif(is_null($change['old']))
                                                                    <span class="text-muted">NULL</span>
                                                                @else
                                                                    {{ $change['old'] }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(is_array($change['new']))
                                                                    <pre>{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                                                @elseif(is_bool($change['new']))
                                                                    <span class="badge bg-{{ $change['new'] ? 'success' : 'danger' }}">{{ $change['new'] ? 'TRUE' : 'FALSE' }}</span>
                                                                @elseif(is_null($change['new']))
                                                                    <span class="text-muted">NULL</span>
                                                                @else
                                                                    {{ $change['new'] }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <pre>{{ json_encode($diff, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <strong>No detailed changes recorded</strong><br>
                                    The audit log entry exists but contains no change details.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Audit Trail
                        </a>
                        @if($auditLog->entity_type === 'sample')
                            <a href="{{ route('samples.show', $auditLog->entity_id) }}" class="btn btn-primary ms-2">
                                <i class="fas fa-eye"></i> View Sample
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
