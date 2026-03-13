@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> QC Queue</h4>
                    <small>Results pending verification</small>
                </div>
                <div class="card-body">
                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sample</th>
                                        <th>Method</th>
                                        <th>Submitted By</th>
                                        <th>Submitted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $resultSet)
                                        <tr>
                                            <td>
                                                <a href="{{ route('samples.show', $resultSet->measurement->sample_id) }}">
                                                    {{ $resultSet->measurement->sample->sample_code ?? 'N/A' }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $resultSet->measurement->sample->client?->name ?? '-' }}</small>
                                            </td>
                                            <td>{{ $resultSet->measurement->method->name ?? 'N/A' }}</td>
                                            <td>{{ $resultSet->submitter?->name ?? 'Unknown' }}</td>
                                            <td>{{ $resultSet->submitted_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('results.page', $resultSet->measurement_id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No pending reviews</h5>
                            <p class="text-muted mb-0">All submitted results have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
