<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\Measurement;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Project;
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
                'client_id' => 'required',
                'project_id' => 'required',
                'new_client_name' => 'nullable|string|max:255|required_if:client_id,<0',
                'new_project_name' => 'nullable|string|max:255|required_if:project_id,<0',
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

        if ($step === 1) {
            $clientId = $request->input('client_id');
            $projectId = $request->input('project_id');
            
            if ($clientId >= 0 && !Client::find($clientId)) {
                return response()->json([
                    'valid' => false,
                    'errors' => ['client_id' => ['Selected client is invalid.']],
                ], 422);
            }
            
            if ($projectId >= 0 && !Project::find($projectId)) {
                return response()->json([
                    'valid' => false,
                    'errors' => ['project_id' => ['Selected project is invalid.']],
                ], 422);
            }
            
            if ($projectId < 0 && empty($clientId)) {
                return response()->json([
                    'valid' => false,
                    'errors' => ['project_id' => ['Please select or create a client first before creating a new project.']],
                ], 422);
            }
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
            'client_id' => 'required',
            'project_id' => 'required',
            'new_client_name' => 'nullable|string|max:255|required_if:client_id,<0',
            'new_project_name' => 'nullable|string|max:255|required_if:project_id,<0',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'method_ids' => 'required|array|min:1',
            'method_ids.*' => 'exists:methods,id',
        ]);

        if ($data['client_id'] < 0) {
            $client = Client::create(['name' => $data['new_client_name']]);
            $clientId = $client->id;
        } else {
            $clientId = $data['client_id'];
        }

        if ($data['project_id'] < 0) {
            $project = Project::create([
                'name' => $data['new_project_name'],
                'client_id' => $clientId
            ]);
            $projectId = $project->id;
        } else {
            $projectId = $data['project_id'];
        }

        $sampleData = [
            'client_id' => $clientId,
            'project_id' => $projectId,
            'name' => $data['name'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'sample_code' => 'S-' . date('Y') . '-' . str_pad(Sample::count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'REGISTERED',
            'created_by' => Auth::id(),
        ];

        $sample = Sample::create($sampleData);

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