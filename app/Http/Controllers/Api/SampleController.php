<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sample;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SampleController extends Controller
{
    public function importPreview(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt'
            ]);

            $file = $request->file('file');
            
            $path = $file->store('imports', 'local');

            $fullPath = Storage::disk('local')->path($path);

            $rows = Excel::toArray(new \stdClass(), $fullPath);
            
            if (empty($rows) || empty($rows[0])) {
                return response()->json(['error' => 'Файл пуст'], 422);
            }

            return response()->json([
                'path' => $path, 
                'preview' => array_slice($rows[0], 0, 50)
            ]);
        } catch (\Exception $e) {
            Log::error('Preview Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function importConfirm(Request $request)
    {
        try {
            $data = $request->validate([
                'path' => 'required|string',
                'mapping' => 'required|array',
                'client_id' => 'required|exists:clients,id'
            ]);

            $path = $data['path'];
            
            if (!Storage::disk('local')->exists($path)) {
                return response()->json(['error' => 'File not found in repository: ' . $path], 404);
            }

            $fullPath = Storage::disk('local')->path($path);

            $rows = Excel::toArray(new \stdClass(), $fullPath);
            $rows = $rows[0];

            $created = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $sampleData = [];
                foreach ($data['mapping'] as $field => $col) {
                    $colIndex = (int) filter_var($col, FILTER_SANITIZE_NUMBER_INT);
                    $sampleData[$field] = $row[$colIndex] ?? null;
                }

                if (empty($sampleData['sample_code'])) {
                    $sampleData['sample_code'] = 'S-' . date('Ymd') . '-' . rand(1000, 9999);
                }

                $sampleData['client_id'] = $data['client_id'];
                $sampleData['created_by'] = Auth::id();
                
                Sample::create($sampleData);
                $created++;
            }

            Storage::disk('local')->delete($path);

            return response()->json(['created' => $created]);

        } catch (\Exception $e) {
            Log::error('Confirm Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}