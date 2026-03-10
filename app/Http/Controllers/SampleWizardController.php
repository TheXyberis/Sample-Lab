<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}