<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Sample;
use App\Models\Method;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function indexView()
    {
        $measurements = Measurement::with(['sample','method','assignee'])->paginate(20);
        return view('measurements.index', compact('measurements'));
    }

    public function createView()
    {
        $samples = Sample::all();
        $methods = Method::all();
        return view('measurements.form', compact('samples','methods'));
    }

    public function editView($id)
    {
        $measurement = Measurement::findOrFail($id);
        $samples = Sample::all();
        $methods = Method::all();
        return view('measurements.form', compact('measurement','samples','methods'));
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