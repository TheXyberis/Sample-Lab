@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="text-primary mb-2">Dashboard</h1>
                            <p class="text-muted mb-0">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</p>
                            @php
    $roleColors = ['Admin' => 'danger', 'Manager' => 'success', 'Laborant' => 'info', 'QC/Reviewer' => 'warning', 'Client' => 'secondary'];
    $roleBadge = $roleColors[Auth::user()->role ?? ''] ?? 'secondary';
@endphp
                            <small class="text-muted">Role: <span class="badge bg-{{ $roleBadge }}">{{ Auth::user()->role }}</span></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small><br>
                            <small class="text-muted">{{ now()->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-flask fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title text-muted">Samples In Progress</h5>
                    <h2 class="display-6 text-primary mb-0">{{ \App\Models\Sample::where('status','IN_PROGRESS')->count() }}</h2>
                    <small class="text-muted">Active samples</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-warning h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-check fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title text-muted">Pending Review</h5>
                    <h2 class="display-6 text-warning mb-0">{{ \App\Models\ResultSet::where('status','SUBMITTED')->count() }}</h2>
                    <small class="text-muted">Awaiting QC</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-success h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title text-muted">Completed Today</h5>
                    <h2 class="display-6 text-success mb-0">{{ \App\Models\Sample::where('status','COMPLETED')->whereDate('updated_at', today())->count() }}</h2>
                    <small class="text-muted">Finished samples</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-danger h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                    <h5 class="card-title text-muted">Overdue</h5>
                    <h2 class="display-6 text-danger mb-0">{{ \App\Models\Measurement::where('planned_at','<',now())->where('status','!=','DONE')->count() }}</h2>
                    <small class="text-muted">SLA breaches</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentSamples = \App\Models\Sample::with('client')->orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp
                    @if($recentSamples->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sample Code</th>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSamples as $sample)
                                        <tr>
                                            <td><code>{{ $sample->sample_code }}</code></td>
                                            <td>{{ $sample->name }}</td>
                                            <td>{{ $sample->client?->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-{{ $sample->status_badge }}">{{ $sample->status }}</span></td>
                                            <td><small class="text-muted">{{ $sample->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('samples.index') }}" class="btn btn-sm btn-outline-primary">View All Samples</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No samples registered yet.</p>
                            @if(Auth::user()->hasRole(['Admin', 'Manager', 'Laborant']))
                                <a href="{{ route('samples.create-wizard') }}" class="btn btn-primary btn-sm mt-2">Register First Sample</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->hasRole(['Admin', 'Manager', 'Laborant']))
                            <a href="{{ route('samples.create-wizard') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Register Sample
                            </a>
                        @endif
                        @if(Auth::user()->hasRole(['Admin', 'Manager']))
                            <a href="{{ route('methods.create') }}" class="btn btn-info">
                                <i class="fas fa-flask"></i> Create Method
                            </a>
                        @endif
                        @if(Auth::user()->hasRole(['Admin', 'Manager', 'QC/Reviewer']))
                            <a href="{{ route('qc.queue') }}" class="btn btn-warning">
                                <i class="fas fa-clipboard-check"></i> QC Queue
                            </a>
                        @endif
                        <a href="{{ route('reports.index') }}" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </a>
                        <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                            <i class="fas fa-history"></i> View Audit Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection