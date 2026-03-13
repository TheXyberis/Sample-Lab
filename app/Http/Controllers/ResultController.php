<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResultSet;
use App\Models\Result;
use App\Models\Measurement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function qcQueue()
    {
        $items = ResultSet::with(['measurement.sample.client', 'measurement.method', 'submitter'])
            ->where('status', 'SUBMITTED')
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);

        return view('qc.queue', compact('items'));
    }

    public function index($measurementId)
    {
        return ResultSet::with('results')
            ->where('measurement_id', $measurementId)
            ->latest()
            ->get();
    }

    public function webIndex($measurementId)
    {
        $measurement = Measurement::with(['method', 'resultSets.results', 'resultSets.submitter'])->findOrFail($measurementId);
        $method = $measurement->method;

        $schemaData = is_array($method->schema_json) ? $method->schema_json : json_decode($method->schema_json, true);
        $schema = $schemaData['fields'] ?? [];

        $results = [];
        $flags = [];

        $currentResultSet = $measurement->resultSets->sortByDesc('created_at')->first();
        if ($currentResultSet) {
            foreach ($currentResultSet->results as $r) {
                $results[$r->field_key] = $r->value_text ?? $r->value_num;
                $flags[$r->field_key] = is_array($r->flags_json) ? $r->flags_json : json_decode($r->flags_json, true);
            }
        } else {
            $currentResultSet = new \stdClass();
            $currentResultSet->status = 'DRAFT';
            $currentResultSet->submitted_by = null;
            $currentResultSet->submitter = null;
        }

        return view('measurements.results', compact('measurement', 'schema', 'results', 'flags', 'currentResultSet'));
    }

    public function saveDraft(Request $request, $id)
    {
        $measurement = Measurement::with('method')->findOrFail($id);

        $schemaData = is_array($measurement->method->schema_json)
            ? $measurement->method->schema_json
            : json_decode($measurement->method->schema_json, true);
        $schema = $schemaData['fields'] ?? [];

        $resultsInput = $request->input('results', []);

        $resultSet = ResultSet::firstOrCreate(
            [
                'measurement_id' => $measurement->id,
                'status' => 'DRAFT'
            ],
            []
        );

        $oldResults = $resultSet->results->keyBy('field_key')->map(fn($r) => $r->value_text ?? $r->value_num)->toArray();

        DB::transaction(function () use ($schema, $resultsInput, $resultSet) {
            foreach ($schema as $field) {
                $key = $field['key'] ?? null;
                if (!$key) continue;

                $value = $resultsInput[$key] ?? null;

                $update = [
                    'flags_json' => json_encode([]),
                ];
                if (($field['type'] ?? '') === 'number') {
                    $update['value_num'] = $value === null || $value === '' ? null : (float)$value;
                    $update['value_text'] = null;
                } else {
                    $update['value_text'] = $value;
                    $update['value_num'] = null;
                }

                Result::updateOrCreate(
                    ['result_set_id' => $resultSet->id, 'field_key' => $key],
                    $update
                );
            }
        });

        $resultSet->load('results');
        $newResults = $resultSet->results->keyBy('field_key')->map(fn($r) => $r->value_text ?? $r->value_num)->toArray();
        $diff = [];
        foreach (array_unique(array_merge(array_keys($oldResults), array_keys($newResults))) as $k) {
            $old = $oldResults[$k] ?? null;
            $new = $newResults[$k] ?? null;
            if ($old !== $new) {
                $diff[$k] = ['old' => $old, 'new' => $new];
            }
        }

        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode($diff ?: ['action' => 'save_draft']),
            'user_id' => Auth::id(),
            'action' => 'save_draft'
        ]);

        return response()->json(['success' => true, 'result_set_id' => $resultSet->id]);
    }

    public function submit(Request $request, $id)
    {
        $measurement = Measurement::with('method')->findOrFail($id);
        $resultSet = $measurement->resultSets()->where('status', 'DRAFT')->with('results')->latest()->first();

        if (!$resultSet) {
            return response()->json(['error' => 'No draft found. Save draft first.'], 400);
        }

        $schemaData = is_array($measurement->method->schema_json)
            ? $measurement->method->schema_json
            : json_decode($measurement->method->schema_json, true);
        $schema = $schemaData['fields'] ?? [];
        $resultsByKey = $resultSet->results->keyBy('field_key');

        foreach ($schema as $field) {
            if (empty($field['required'])) continue;
            $key = $field['key'] ?? null;
            if (!$key) continue;
            $r = $resultsByKey->get($key);
            $val = $r ? ($r->value_text ?? $r->value_num) : null;
            if ($val === null || $val === '') {
                return response()->json(['error' => 'Required field "' . ($field['label'] ?? $key) . '" is empty.'], 422);
            }
        }

        $resultSet->status = 'SUBMITTED';
        $resultSet->submitted_by = Auth::id();
        $resultSet->submitted_at = now();
        $resultSet->save();

        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode(['action' => 'submit']),
            'user_id' => Auth::id(),
            'action' => 'submit'
        ]);

        return response()->json(['success' => true]);
    }

    public function review(Request $request, $measurementId)
    {
        $resultSet = ResultSet::where('measurement_id', $measurementId)->latest()->firstOrFail();
        $user = Auth::user();
        $resultSet->update([
            'status' => 'REVIEWED',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode(['status' => 'REVIEWED']),
            'user_id' => $user->id,
            'action' => 'review'
        ]);
        return response()->json(['status' => 'reviewed']);
    }

    public function approve(Request $request, $measurementId)
    {
        $resultSet = ResultSet::where('measurement_id', $measurementId)->latest()->firstOrFail();
        $user = Auth::user();
        $resultSet->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode(['status' => 'APPROVED']),
            'user_id' => $user->id,
            'action' => 'approve'
        ]);
        return response()->json(['status' => 'approved']);
    }

    public function lock(Request $request, $measurementId)
    {
        $resultSet = ResultSet::where('measurement_id', $measurementId)->latest()->firstOrFail();
        $user = Auth::user();
        $resultSet->update([
            'status' => 'LOCKED',
            'locked_at' => now(),
        ]);
        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode(['status' => 'LOCKED']),
            'user_id' => $user->id,
            'action' => 'lock'
        ]);
        return response()->json(['status' => 'locked']);
    }

    public function unlock(Request $request, $measurementId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['QC/Reviewer', 'Manager', 'Admin'])) {
            return response()->json(['error'=>'Unauthorized'],403);
        }
        $resultSet = ResultSet::where('measurement_id', $measurementId)->latest()->firstOrFail();
        $resultSet->update([
            'status' => 'DRAFT',
            'locked_at' => null
        ]);
        AuditLog::create([
            'entity_type' => 'result_set',
            'entity_id' => $resultSet->id,
            'diff_json' => json_encode(['status' => 'DRAFT']),
            'user_id' => $user->id,
            'action' => 'unlock'
        ]);
        return response()->json(['status' => 'unlocked']);
    }
}