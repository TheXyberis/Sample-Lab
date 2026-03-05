<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function index()
    {
        return Measurement::with(['sample','method','assignee'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sample_id'=>'required|exists:samples,id',
            'method_id'=>'required|exists:methods,id',
            'assignee_id'=>'nullable|exists:users,id',
            'priority'=>'nullable|integer',
            'planned_at'=>'nullable|date',
            'notes'=>'nullable|string'
        ]);

        $measurement = Measurement::create($data);
        return response()->json($measurement,201);
    }

    public function show($id)
    {
        return Measurement::with(['sample','method','assignee'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $measurement = Measurement::findOrFail($id);
        if(!in_array($measurement->status,['PLANNED','RUNNING'])){
            return response()->json(['error'=>'Cannot edit finished/cancelled measurement'],403);
        }

        $measurement->update($request->only(['assignee_id','priority','planned_at','notes']));
        return $measurement;
    }

    public function start($id)
    {
        $measurement = Measurement::findOrFail($id);
        $measurement->status='RUNNING';
        $measurement->started_at = now();
        $measurement->save();
        return $measurement;
    }

    public function finish($id)
    {
        $measurement = Measurement::findOrFail($id);
        $measurement->status='DONE';
        $measurement->finished_at = now();
        $measurement->save();
        return $measurement;
    }
}