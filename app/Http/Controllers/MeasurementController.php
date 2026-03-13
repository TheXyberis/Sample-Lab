<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Sample;
use App\Models\Method;
use App\Models\ResultSet;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementController extends Controller
{
    public function index()
    {
        $measurements = Measurement::with(['sample.client', 'sample.project', 'method', 'assignee'])->paginate(20);
        return view('measurements.index', compact('measurements'));
    }

    public function create()
    {
        $samples = Sample::all();
        $methods = Method::all();
        return view('measurements.form', compact('samples','methods'));
    }

    public function show($id)
    {
        $measurement = Measurement::findOrFail($id);
        return view('measurements.show', compact('measurement'));
    }

    public function edit($id)
    {
        $measurement = Measurement::findOrFail($id);
        $samples = Sample::all();
        $methods = Method::all();
        return view('measurements.form', compact('measurement','samples','methods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sample_id' => 'required|exists:samples,id',
            'method_id' => 'required|exists:methods,id',
            'planned_at' => 'required|date',
            'priority' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        $data['status'] = 'PLANNED';
        $data['assignee_id'] = Auth::id();
        
        $measurement = Measurement::create($data);
        
        ResultSet::create([
            'measurement_id' => $measurement->id,
            'status' => 'DRAFT'
        ]);

        return redirect()->route('measurements.index')->with('success', 'Measurement created successfully');
    }

    public function update(Request $request, $id)
    {
        $measurement = Measurement::findOrFail($id);

        $data = $request->validate([
            'sample_id' => 'required|exists:samples,id',
            'method_id' => 'required|exists:methods,id',
            'planned_at' => 'required|date',
            'priority' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        $measurement->update($data);

        return redirect()->route('measurements.index')->with('success', 'Measurement updated successfully');
    }

    public function start($id)
    {
        $measurement = Measurement::findOrFail($id);
        $measurement->status = 'RUNNING';
        $measurement->started_at = now();
        $measurement->save();
        return redirect()->back();
    }

    public function finish($id)
    {
        $measurement = Measurement::findOrFail($id);
        $measurement->status = 'DONE';
        $measurement->finished_at = now();
        $measurement->save();
        
        $sample = $measurement->sample;
        $allMeasurementsDone = $sample->measurements()->where('status', '!=', 'DONE')->count() === 0;
        if ($allMeasurementsDone) {
            $sample->status = 'COMPLETED';
            $sample->save();
        }
        
        return redirect()->back();
    }
    
    public function bulkPlan(Request $request)
    {
        $data = $request->validate([
            'sample_ids' => 'required|array',
            'sample_ids.*' => 'exists:samples,id',
            'method_id' => 'required|exists:methods,id',
            'planned_at' => 'required|date',
            'priority' => 'required|integer'
        ]);
        
        $created = 0;
        foreach ($data['sample_ids'] as $sampleId) {
            $measurement = Measurement::create([
                'sample_id' => $sampleId,
                'method_id' => $data['method_id'],
                'planned_at' => $data['planned_at'],
                'priority' => $data['priority'],
                'status' => 'PLANNED',
                'assignee_id' => Auth::id()
            ]);
            
            ResultSet::create([
                'measurement_id' => $measurement->id,
                'status' => 'DRAFT'
            ]);
            
            $created++;
        }
        
        return response()->json(['created' => $created]);
    }
}