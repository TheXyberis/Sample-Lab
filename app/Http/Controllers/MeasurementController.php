<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Sample;
use App\Models\Method;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function index()
    {
        $measurements = Measurement::with(['sample','method','assignee'])->paginate(20);
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
        Measurement::create($data);

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
        return redirect()->back();
    }
}