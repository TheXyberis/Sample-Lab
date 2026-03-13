<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sample;
use App\Models\Client;
use App\Models\Project;
use App\Models\AuditLog;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class SampleWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:samples:read')->only(['index', 'show']);
        $this->middleware('permission:samples:create')->only(['create', 'store']);
        $this->middleware('permission:samples:update')->only(['edit', 'update']);
        $this->middleware('permission:samples:delete')->only(['destroy']);
    }
    public function index(Request $request){
        $query = Sample::with(['client', 'project']);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('sample_code', 'like', "%{$q}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $samples = $query->paginate(20)->withQueryString();
        return view('samples.index', compact('samples'));
    }

    public function create(){
        return view('samples.form');
    }

    public function edit($id){
        $sample = Sample::findOrFail($id);
        return view('samples.form', compact('sample'));
    }

    public function show($id){
        $sample = Sample::with(['measurements.method', 'client', 'project'])->findOrFail($id);
        return view('samples.show', compact('sample'));
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $path = $file->store('imports', 'local');

        $fullPath = Storage::disk('local')->path($path);
        $rows = Excel::toArray(new \stdClass(), $fullPath);

        if (empty($rows) || empty($rows[0])) {
            return response()->json(['error' => 'Empty CSV'], 422);
        }

        return response()->json([
            'path' => $path,
            'preview' => array_slice($rows[0], 0, 50)
        ]);
    }

    public function importConfirm(Request $request)
    {
        $data = $request->validate([
            'path' => 'required|string',
            'mapping' => 'required|array',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $path = $data['path'];

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fullPath = Storage::disk('local')->path($path);
        $rows = Excel::toArray(new \stdClass(), $fullPath);

        if (empty($rows) || empty($rows[0])) {
            return response()->json(['error' => 'Empty CSV'], 422);
        }

        $rows = $rows[0];
        $created = 0;

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 0) continue;

            $sampleData = [];
            foreach ($data['mapping'] as $field => $col) {
                $colIndex = (int) filter_var($col, FILTER_SANITIZE_NUMBER_INT);
                $sampleData[$field] = $row[$colIndex] ?? null;
            }

            if (empty($sampleData['sample_code'])) {
                $sampleData['sample_code'] = 'S-' . date('Ymd') . '-' . rand(1000, 9999);
            }

            $sampleData['client_id'] = $data['client_id'];
            $sampleData['project_id'] = $data['project_id'] ?? null;
            $sampleData['created_by'] = Auth::id();
            $sampleData['status'] = 'REGISTERED';

            $fillable = ['sample_code', 'client_id', 'project_id', 'name', 'type', 'quantity', 'unit', 'status', 'created_by'];
            $filtered = array_intersect_key($sampleData, array_flip($fillable));
            Sample::create($filtered);
            $created++;
        }

        Storage::disk('local')->delete($path);

        return response()->json(['created' => $created]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string',
        ]);

        $data['sample_code'] = Sample::generateSampleCode();
        $data['status'] = $data['status'] ?? 'REGISTERED';
        $data['created_by'] = Auth::id();
        $data['barcode_value'] = $data['sample_code'];

        $sample = Sample::create($data);
        $sample->update(['qr_value' => route('samples.show', $sample->id)]);

        AuditLog::create([
            'entity_type' => 'sample',
            'entity_id' => $sample->id,
            'action' => 'create',
            'diff_json' => json_encode($data),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('samples.index')->with('success', 'Sample created successfully');
    }

    public function update(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string',
        ]);

        $oldValues = $sample->getAttributes();
        $sample->update($data);
        $newValues = $sample->getAttributes();
        
        $diff = [];
        foreach ($oldValues as $key => $oldValue) {
            if (isset($newValues[$key]) && $oldValue !== $newValues[$key]) {
                $diff[$key] = ['old' => $oldValue, 'new' => $newValues[$key]];
            }
        }

        AuditLog::create([
            'entity_type' => 'sample',
            'entity_id' => $sample->id,
            'action' => 'update',
            'diff_json' => json_encode($diff),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('samples.index')->with('success', 'Sample updated successfully');
    }

    public function destroy($id)
    {
        $sample = Sample::findOrFail($id);
        $sample->delete();

        return redirect()->route('samples.index')->with('success', 'Sample deleted successfully');
    }
    
    public function generateLabel($id)
    {
        $sample = Sample::findOrFail($id);
        $code = $sample->barcode_value ?? $sample->sample_code;

        $qrSvg = QrCode::format('svg')->size(200)->generate(route('samples.show', $id));
        $code128Png = DNS1D::getBarcodePNG($code, 'C128', 2, 50);

        return view('samples.label', compact('sample', 'qrSvg', 'code128Png'));
    }
    
    public function downloadBarcode($id)
    {
        $sample = Sample::findOrFail($id);
        $code = $sample->barcode_value ?? $sample->sample_code;
        $png = base64_decode(DNS1D::getBarcodePNG($code, 'C128', 2, 50));

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="barcode_' . $sample->sample_code . '.png"');
    }
}