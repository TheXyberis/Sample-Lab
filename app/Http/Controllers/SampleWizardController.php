<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\Measurement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SampleWizardController extends Controller
{
    public function showForm()
    {
        return view('samples.wizard');
    }

    public function validateStep(Request $request, int $step)
    {
        $rules = match ($step) {
            1 => [
                'client_id' => 'required|exists:clients,id',
                'project_id' => 'required|exists:projects,id',
            ],
            2 => [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ],
            3 => [
                'quantity' => 'nullable|numeric',
                'unit' => 'nullable|string|max:50',
            ],
            4 => [
                'method_ids' => 'required|array|min:1',
                'method_ids.*' => 'exists:methods,id',
            ],
            5 => [
                'confirm' => 'nullable',
            ],
            default => [],
        };

        if (empty($rules)) {
            return response()->json([
                'valid' => false,
                'errors' => ['step' => ['Invalid step number.']],
            ], 422);
        }

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Step validated successfully.',
        ]);
    }

    public function storeFromWizard(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'method_ids' => 'required|array|min:1',
            'method_ids.*' => 'exists:methods,id',
        ]);

        $data['sample_code'] = 'S-' . date('Y') . '-' . str_pad(Sample::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['status'] = 'REGISTERED';
        $data['created_by'] = Auth::id();

        $sample = Sample::create($data);

        foreach ($data['method_ids'] as $methodId) {
            Measurement::create([
                'sample_id' => $sample->id,
                'method_id' => $methodId,
                'status' => 'PLANNED',
            ]);
        }

        AuditLog::create([
            'entity_type' => 'sample',
            'entity_id' => $sample->id,
            'action' => 'create',
            'diff_json' => json_encode($data),
            'user_id' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['redirect' => route('samples.show', $sample->id), 'success' => true]);
        }
        return redirect()->route('samples.show', $sample->id)->with('success', 'Sample created successfully');
    }
}