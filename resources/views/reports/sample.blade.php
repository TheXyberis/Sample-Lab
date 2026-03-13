<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sample Report - {{ $sample->sample_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-section { border: 1px solid #ddd; padding: 15px; }
        .info-section h3 { margin: 0 0 10px 0; font-size: 16px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; }
        .info-value { flex: 1; }
        .measurements { margin-top: 30px; }
        .measurement { border: 1px solid #ddd; margin-bottom: 20px; page-break-inside: avoid; }
        .measurement-header { background: #f5f5f5; padding: 10px; border-bottom: 1px solid #ddd; }
        .measurement-header h4 { margin: 0; font-size: 14px; }
        .measurement-body { padding: 15px; }
        .results-table { width: 100%; border-collapse: collapse; }
        .results-table th, .results-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .results-table th { background: #f9f9f9; font-weight: bold; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .status-draft { background: #f8f9fa; color: #6c757d; }
        .status-submitted { background: #d1ecf1; color: #0c5460; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-locked { background: #d6d8db; color: #383d41; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sample Analysis Report</h1>
        <p>SampleLab LIMS - Professional Laboratory Information Management System</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>Sample Information</h3>
            <div class="info-row">
                <div class="info-label">Sample Code:</div>
                <div class="info-value"><strong>{{ $sample->sample_code }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $sample->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type:</div>
                <div class="info-value">{{ $sample->type }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower($sample->status) }}">{{ $sample->status }}</span>
                </div>
            </div>
            @if($sample->quantity)
            <div class="info-row">
                <div class="info-label">Quantity:</div>
                <div class="info-value">{{ $sample->quantity }} {{ $sample->unit }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Received:</div>
                <div class="info-value">{{ $sample->received_at?->format('Y-m-d') ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Collected:</div>
                <div class="info-value">{{ $sample->collected_at?->format('Y-m-d') ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="info-section">
            <h3>Client & Project</h3>
            <div class="info-row">
                <div class="info-label">Client:</div>
                <div class="info-value">{{ $sample->client?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project:</div>
                <div class="info-value">{{ $sample->project?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Registered By:</div>
                <div class="info-value">{{ $sample->creator?->name ?? 'System' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Registration:</div>
                <div class="info-value">{{ $sample->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <div class="measurements">
        <h2>Measurements & Results</h2>
        @if($sample->measurements->count() > 0)
            @foreach($sample->measurements as $measurement)
                <div class="measurement">
                    <div class="measurement-header">
                        <h4>{{ $measurement->method->name }} @if($measurement->method->version)(v{{ $measurement->method->version }})@endif</h4>
                        <small>Status: {{ $measurement->status }} | Priority: {{ $measurement->priority }}</small>
                    </div>
                    <div class="measurement-body">
                        @if($measurement->resultSets->count() > 0)
                            @php
                                $latestResultSet = $measurement->resultSets->sortByDesc('created_at')->first();
                                $results = [];
                                if ($latestResultSet) {
                                    foreach ($latestResultSet->results as $result) {
                                        $results[$result->field_key] = [
                                            'value' => $result->value_text ?? $result->value_num,
                                            'unit' => $result->unit,
                                            'flags' => is_array($result->flags_json) ? $result->flags_json : json_decode($result->flags_json, true)
                                        ];
                                    }
                                }
                                $schema = is_array($measurement->method->schema_json) ? $measurement->method->schema_json : json_decode($measurement->method->schema_json, true);
                                $fields = $schema['fields'] ?? [];
                            @endphp
                            
                            <table class="results-table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Result</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fields as $field)
                                        @php
                                            $fieldKey = $field['key'] ?? 'unknown';
                                            $fieldLabel = $field['label'] ?? $fieldKey;
                                            $result = $results[$fieldKey] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $fieldLabel }}</td>
                                            <td>{{ $result['value'] ?? '-' }}</td>
                                            <td>{{ $result['unit'] ?? ($field['unit'] ?? '-') }}</td>
                                            <td>
                                                @if($result && !empty($result['flags']))
                                                    @foreach($result['flags'] as $flag)
                                                        <span class="status-badge" style="background: #fff3cd; color: #856404;">{{ $flag }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="status-badge status-{{ strtolower($latestResultSet->status) }}">{{ $latestResultSet->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <div style="margin-top: 15px; font-size: 11px; color: #666;">
                                <strong>Result Set Status:</strong> {{ $latestResultSet->status }} |
                                @if($latestResultSet->submitted_by) <strong>Submitted:</strong> {{ $latestResultSet->submitted_at->format('Y-m-d H:i:s') }} @endif
                                @if($latestResultSet->approved_by) <strong>Approved:</strong> {{ $latestResultSet->approved_at->format('Y-m-d H:i:s') }} @endif
                            </div>
                        @else
                            <p style="color: #666; font-style: italic;">No results recorded for this measurement.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p style="color: #666; font-style: italic;">No measurements have been planned for this sample.</p>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated automatically by SampleLab LIMS. For questions or concerns, please contact the laboratory administrator.</p>
        <p>Report ID: R-{{ $sample->id }}-{{ date('YmdHis') }} | Page 1 of 1</p>
    </div>
</body>
</html>
