<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .project-info { border: 1px solid #ddd; padding: 15px; margin-bottom: 30px; }
        .project-info h3 { margin: 0 0 10px 0; font-size: 16px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; }
        .info-value { flex: 1; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .summary-card { border: 1px solid #ddd; padding: 15px; text-align: center; }
        .summary-card h4 { margin: 0 0 5px 0; font-size: 24px; color: #333; }
        .summary-card p { margin: 0; color: #666; font-size: 11px; }
        .samples { margin-top: 30px; }
        .sample { border: 1px solid #ddd; margin-bottom: 25px; page-break-inside: avoid; }
        .sample-header { background: #f5f5f5; padding: 10px; border-bottom: 1px solid #ddd; }
        .sample-header h4 { margin: 0; font-size: 14px; }
        .sample-body { padding: 15px; }
        .measurements-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .measurements-table th, .measurements-table td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 11px; }
        .measurements-table th { background: #f9f9f9; font-weight: bold; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .status-registered { background: #d1ecf1; color: #0c5460; }
        .status-in_progress { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-draft { background: #f8f9fa; color: #6c757d; }
        .status-submitted { background: #d1ecf1; color: #0c5460; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-locked { background: #d6d8db; color: #383d41; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Project Summary Report</h1>
        <p>SampleLab LIMS - Professional Laboratory Information Management System</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="project-info">
        <h3>Project Information</h3>
        <div class="info-row">
            <div class="info-label">Project Name:</div>
            <div class="info-value"><strong>{{ $project->name }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Client:</div>
            <div class="info-value">{{ $project->client?->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $project->status ?? 'Active' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created:</div>
            <div class="info-value">{{ $project->created_at->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <h4>{{ $project->samples->count() }}</h4>
            <p>Total Samples</p>
        </div>
        <div class="summary-card">
            <h4>{{ $project->samples->where('status', 'COMPLETED')->count() }}</h4>
            <p>Completed</p>
        </div>
        <div class="summary-card">
            <h4>{{ $project->samples->where('status', 'IN_PROGRESS')->count() }}</h4>
            <p>In Progress</p>
        </div>
        <div class="summary-card">
            <h4>{{ $project->samples->sum(function($sample) { return $sample->measurements->count(); }) }}</h4>
            <p>Total Measurements</p>
        </div>
    </div>

    <div class="samples">
        <h2>Sample Details</h2>
        @if($project->samples->count() > 0)
            @foreach($project->samples as $sample)
                <div class="sample">
                    <div class="sample-header">
                        <h4>{{ $sample->sample_code }} - {{ $sample->name }}</h4>
                        <small>Type: {{ $sample->type }} | Status: <span class="status-badge status-{{ strtolower($sample->status) }}">{{ $sample->status }}</span></small>
                    </div>
                    <div class="sample-body">
                        <div class="info-row">
                            <div class="info-label">Quantity:</div>
                            <div class="info-value">{{ $sample->quantity ?? 'N/A' }} {{ $sample->unit ?? '' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Received:</div>
                            <div class="info-value">{{ $sample->received_at?->format('Y-m-d') ?? 'N/A' }}</div>
                        </div>
                        
                        @if($sample->measurements->count() > 0)
                            <h5 style="margin: 15px 0 10px 0; font-size: 13px;">Measurements</h5>
                            <table class="measurements-table">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Results Status</th>
                                        <th>Started</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sample->measurements as $measurement)
                                        @php
                                            $latestResultSet = $measurement->resultSets->sortByDesc('created_at')->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $measurement->method->name }}</td>
                                            <td>
                                                <span class="status-badge" style="background: #f8f9fa; color: #6c757d;">{{ $measurement->status }}</span>
                                            </td>
                                            <td>
                                                @if($latestResultSet)
                                                    <span class="status-badge status-{{ strtolower($latestResultSet->status) }}">{{ $latestResultSet->status }}</span>
                                                @else
                                                    <span style="color: #999;">No results</span>
                                                @endif
                                            </td>
                                            <td>{{ $measurement->started_at?->format('m-d') ?? '-' }}</td>
                                            <td>{{ $measurement->finished_at?->format('m-d') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="color: #666; font-style: italic; margin-top: 10px;">No measurements planned for this sample.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p style="color: #666; font-style: italic;">No samples have been registered for this project.</p>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated automatically by SampleLab LIMS. For questions or concerns, please contact the laboratory administrator.</p>
        <p>Report ID: P-{{ $project->id }}-{{ date('YmdHis') }} | Page 1 of 1</p>
    </div>
</body>
</html>
