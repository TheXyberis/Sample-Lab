@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Scan-First Workflow</h4>
                    <small class="text-white-50">Quick sample lookup via QR/Barcode scanning</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Scan Sample Code</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Enter sample code or scan QR/barcode to quickly access sample details and measurements.</p>
                                    
                                    <form id="scanForm" class="mb-3">
                                        <div class="input-group">
                                            <input type="text" 
                                                   id="sampleCodeInput" 
                                                   class="form-control form-control-lg" 
                                                   placeholder="Enter sample code (e.g., S-2026-0001)"
                                                   autofocus>
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-search"></i> Find Sample
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="clearBtn">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                        <button type="button" class="btn btn-outline-info" id="cameraBtn">
                                            <i class="fas fa-camera"></i> Open Camera
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('samples.create-wizard') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Register New Sample
                                        </a>
                                        <a href="{{ route('measurements.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-flask"></i> View All Measurements
                                        </a>
                                        <a href="{{ route('qc.queue') }}" class="btn btn-outline-warning">
                                            <i class="fas fa-clipboard-check"></i> QC Queue
                                        </a>
                                        <a href="{{ route('samples.import') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-file-import"></i> Import CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sampleResult" class="mt-4" style="display: none;">
                        <!-- Sample details will be loaded here -->
                    </div>

                    <div class="mt-4">
                        <div class="card border-light">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Recent Samples</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $recentSamples = \App\Models\Sample::with('client', 'project')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sample Code</th>
                                                <th>Name</th>
                                                <th>Client</th>
                                                <th>Status</th>
                                                <th>Measurements</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($recentSamples->count() > 0)
                                                @foreach($recentSamples as $sample)
                                                    <tr>
                                                        <td><code>{{ $sample->sample_code }}</code></td>
                                                        <td>{{ $sample->name }}</td>
                                                        <td>{{ $sample->client?->name ?? 'N/A' }}</td>
                                                        <td><span class="badge bg-{{ $sample->status_badge }}">{{ $sample->status }}</span></td>
                                                        <td>{{ $sample->measurements->count() }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('samples.show', $sample->id) }}" class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                @if($sample->measurements->count() > 0)
                                                                    <a href="{{ route('results.page', $sample->measurements->first()->id) }}" class="btn btn-outline-success">
                                                                        <i class="fas fa-chart-line"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No samples found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
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
document.addEventListener('DOMContentLoaded', function() {
    const scanForm = document.getElementById('scanForm');
    const sampleCodeInput = document.getElementById('sampleCodeInput');
    const clearBtn = document.getElementById('clearBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const sampleResult = document.getElementById('sampleResult');

    // Auto-focus on input field
    sampleCodeInput.focus();

    // Handle form submission
    scanForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const sampleCode = sampleCodeInput.value.trim();
        
        if (!sampleCode) {
            showAlert('Please enter a sample code', 'warning');
            return;
        }

        findSample(sampleCode);
    });

    // Clear button
    clearBtn.addEventListener('click', function() {
        sampleCodeInput.value = '';
        sampleResult.style.display = 'none';
        sampleCodeInput.focus();
    });

    // Camera button (placeholder for future camera integration)
    cameraBtn.addEventListener('click', function() {
        showAlert('Camera scanning feature coming soon!', 'info');
    });

    // Find sample function
    function findSample(sampleCode) {
        fetch(`/api/samples/lookup?code=${encodeURIComponent(sampleCode)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySampleDetails(data.sample);
            } else {
                showAlert(data.message || 'Sample not found', 'danger');
                sampleResult.style.display = 'none';
            }
        })
        .catch(error => {
            showAlert('Error searching for sample', 'danger');
            console.error('Error:', error);
        });
    }

    // Display sample details
    function displaySampleDetails(sample) {
        const statusColors = {
            'REGISTERED': 'secondary',
            'IN_PROGRESS': 'info',
            'COMPLETED': 'success',
            'ARCHIVED': 'dark',
            'DISPOSED': 'danger'
        };

        const measurementsHtml = sample.measurements.map(meas => `
            <tr>
                <td>${meas.method.name}</td>
                <td><span class="badge bg-${meas.status === 'DONE' ? 'success' : 'warning'}">${meas.status}</span></td>
                <td>${meas.assignee?.name || 'Unassigned'}</td>
                <td>
                    ${meas.result_sets && meas.result_sets.length > 0 ? 
                        `<a href="/measurements/${meas.id}/results" class="btn btn-sm btn-primary">View Results</a>` : 
                        '<span class="text-muted">No results</span>'
                    }
                </td>
            </tr>
        `).join('');

        sampleResult.innerHTML = `
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Sample Found: ${sample.sample_code}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>Name:</strong></td><td>${sample.name}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>${sample.type}</td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-${statusColors[sample.status] || 'secondary'}">${sample.status}</span></td></tr>
                                <tr><td><strong>Client:</strong></td><td>${sample.client?.name || 'N/A'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>Project:</strong></td><td>${sample.project?.name || 'N/A'}</td></tr>
                                <tr><td><strong>Quantity:</strong></td><td>${sample.quantity || 'N/A'} ${sample.unit || ''}</td></tr>
                                <tr><td><strong>Received:</strong></td><td>${sample.received_at || 'N/A'}</td></tr>
                                <tr><td><strong>Registered:</strong></td><td>${sample.created_at}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <a href="/samples/${sample.id}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Full Details
                        </a>
                        <a href="/samples/${sample.id}/label" class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-print"></i> Print Label
                        </a>
                        <button onclick="printSampleCode('${sample.sample_code}')" class="btn btn-outline-info">
                            <i class="fas fa-qrcode"></i> Print QR
                        </button>
                    </div>

                    ${sample.measurements && sample.measurements.length > 0 ? `
                        <h6>Measurements (${sample.measurements.length})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Assignee</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${measurementsHtml}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-muted">No measurements planned for this sample.</p>'}
                </div>
            </div>
        `;
        
        sampleResult.style.display = 'block';
        
        // Scroll to result
        sampleResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Print sample code (placeholder)
    function printSampleCode(sampleCode) {
        showAlert(`Printing QR code for ${sampleCode}...`, 'info');
        // Future implementation: trigger QR code print
    }

    // Show alert function
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        
        const card = document.querySelector('.card-body');
        card.insertBefore(alertDiv, card.firstChild);
        
        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            sampleCodeInput.focus();
        }
        
        // Escape to clear
        if (e.key === 'Escape') {
            clearBtn.click();
        }
    });
});
</script>
@endsection
