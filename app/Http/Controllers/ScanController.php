<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $sample = Sample::with([
            'client', 
            'project', 
            'creator',
            'measurements.method',
            'measurements.assignee',
            'measurements.resultSets'
        ])->where('sample_code', $request->code)->first();

        if (!$sample) {
            return response()->json([
                'success' => false,
                'message' => 'Sample not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'sample' => [
                'id' => $sample->id,
                'sample_code' => $sample->sample_code,
                'name' => $sample->name,
                'type' => $sample->type,
                'status' => $sample->status,
                'quantity' => $sample->quantity,
                'unit' => $sample->unit,
                'received_at' => $sample->received_at?->format('Y-m-d'),
                'created_at' => $sample->created_at->format('Y-m-d H:i'),
                'client' => $sample->client ? [
                    'name' => $sample->client->name
                ] : null,
                'project' => $sample->project ? [
                    'name' => $sample->project->name
                ] : null,
                'measurements' => $sample->measurements->map(function($measurement) {
                    return [
                        'id' => $measurement->id,
                        'method' => [
                            'name' => $measurement->method->name
                        ],
                        'status' => $measurement->status,
                        'assignee' => $measurement->assignee ? [
                            'name' => $measurement->assignee->name
                        ] : null,
                        'result_sets' => $measurement->resultSets
                    ];
                })
            ]
        ]);
    }
}
