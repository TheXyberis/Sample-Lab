@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Report Generator</h4>
                    <small class="text-white-50">Generate professional PDF reports for samples and projects</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Sample Report</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Generate a detailed report for a specific sample including all measurements and results.</p>
                                    
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <select id="sampleSelect" name="sample_id" class="form-select" required>
                                                <option value="">Select a sample...</option>
                                                @foreach(\App\Models\Sample::with('client')->orderBy('created_at', 'desc')->limit(50)->get() as $sample)
                                                    <option value="{{ $sample->id }}">{{ $sample->sample_code }} - {{ $sample->client?->name ?? 'No Client' }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="previewSampleBtn" class="btn btn-outline-primary">Preview</button>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-primary" id="generateSampleReportBtn" disabled>
                                            <i class="fas fa-file-pdf"></i> Generate Sample Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Project Report</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Generate a comprehensive report for a project including all associated samples and their results.</p>
                                    
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <select id="projectSelect" name="project_id" class="form-select" required>
                                                <option value="">Select a project...</option>
                                                @foreach(\App\Models\Project::with('client')->orderBy('name')->get() as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->client?->name ?? 'No Client' }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="previewProjectBtn" class="btn btn-outline-success">Preview</button>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-success" id="generateProjectReportBtn" disabled>
                                            <i class="fas fa-file-pdf"></i> Generate Project Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Recent Reports</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $recentReports = \App\Models\ReportLog::with('generator')
                                        ->orderBy('generated_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Identifier</th>
                                                <th>Generated By</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($recentReports->count() > 0)
                                                @foreach($recentReports as $report)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-{{ $report->report_type === 'sample' ? 'primary' : 'success' }}">
                                                                {{ ucfirst($report->report_type) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $report->entity_name }}</td>
                                                        <td>{{ $report->generator?->name ?? 'System' }}</td>
                                                        <td>{{ $report->generated_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <small class="text-muted">{{ $report->filename }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No reports generated yet</td>
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
    const sampleSelect = document.getElementById('sampleSelect');
    const projectSelect = document.getElementById('projectSelect');
    const generateSampleBtn = document.getElementById('generateSampleReportBtn');
    const generateProjectBtn = document.getElementById('generateProjectReportBtn');
    const previewSampleBtn = document.getElementById('previewSampleBtn');
    const previewProjectBtn = document.getElementById('previewProjectBtn');

    sampleSelect.addEventListener('change', function() {
        generateSampleBtn.disabled = !this.value;
    });

    projectSelect.addEventListener('change', function() {
        generateProjectBtn.disabled = !this.value;
    });

    previewSampleBtn.addEventListener('click', function() {
        if (sampleSelect.value) {
            window.location.href = `/reports/sample/${sampleSelect.value}/preview`;
        }
    });

    previewProjectBtn.addEventListener('click', function() {
        if (projectSelect.value) {
            window.location.href = `/reports/project/${projectSelect.value}/preview`;
        }
    });

    generateSampleBtn.addEventListener('click', function() {
        if (sampleSelect.value) {
            window.open(`/reports/sample/${sampleSelect.value}/generate`, '_blank');
        }
    });

    generateProjectBtn.addEventListener('click', function() {
        if (projectSelect.value) {
            window.open(`/reports/project/${projectSelect.value}/generate`, '_blank');
        }
    });
});
</script>
@endsection