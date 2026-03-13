<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\ResultSet;
use App\Models\Result;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports:generate|reports:download');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function generateSampleReport($sampleId)
    {
        $sample = Sample::with([
            'client', 
            'project', 
            'creator',
            'measurements.method',
            'measurements.resultSets.results'
        ])->findOrFail($sampleId);

        $pdf = Pdf::loadView('reports.sample', compact('sample'));
        $filename = 'sample_report_' . $sample->sample_code . '_' . date('Y-m-d') . '.pdf';

        \App\Models\AuditLog::create([
            'entity_type' => 'report',
            'entity_id' => $sample->id,
            'action' => 'generate_sample',
            'diff_json' => json_encode(['filename' => $filename]),
            'user_id' => Auth::id(),
        ]);

        return $pdf->download($filename);
    }

    public function generateProjectReport($projectId)
    {
        $project = \App\Models\Project::with([
            'client',
            'samples' => function($query) {
                $query->with(['measurements.method', 'measurements.resultSets.results']);
            }
        ])->findOrFail($projectId);

        $pdf = Pdf::loadView('reports.project', compact('project'));
        $filename = 'project_report_' . $project->name . '_' . date('Y-m-d') . '.pdf';

        \App\Models\AuditLog::create([
            'entity_type' => 'report',
            'entity_id' => $project->id,
            'action' => 'generate_project',
            'diff_json' => json_encode(['filename' => $filename]),
            'user_id' => Auth::id(),
        ]);

        return $pdf->download($filename);
    }

    public function previewSampleReport($sampleId)
    {
        $sample = Sample::with([
            'client', 
            'project', 
            'creator',
            'measurements.method',
            'measurements.resultSets.results'
        ])->findOrFail($sampleId);

        return view('reports.sample', compact('sample'));
    }

    public function previewProjectReport($projectId)
    {
        $project = \App\Models\Project::with([
            'client',
            'samples' => function($query) {
                $query->with(['measurements.method', 'measurements.resultSets.results']);
            }
        ])->findOrFail($projectId);

        return view('reports.project', compact('project'));
    }
}
