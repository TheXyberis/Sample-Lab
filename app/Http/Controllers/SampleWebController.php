<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sample;

class SampleWebController extends Controller
{
    public function index(){
        $samples = Sample::paginate(20);
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
        $sample = Sample::findOrFail($id);
        return view('samples.show', compact('sample'));
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->store('imports');

        $rows = Excel::toArray([], $file);
        if(empty($rows) || empty($rows[0])){
            return response()->json(['error'=>'Empty CSV'],422);
        }

        $preview = array_slice($rows[0],0,50);

        return response()->json([
            'path' => $path,
            'preview' => $preview
        ]);
    }

    public function importConfirm(Request $request)
    {
        $data = $request->validate([
            'path' => 'required|string',
            'mapping' => 'required|array'
        ]);

        $path = $data['path'];
        $mapping = $data['mapping'];

        if(!Storage::exists($path)){
            return response()->json(['error'=>'File not found'],404);
        }

        $rows = Excel::toArray([], Storage::path($path));
        if(empty($rows) || empty($rows[0])){
            return response()->json(['error'=>'Empty CSV'],422);
        }

        $rows = $rows[0];
        $created = 0;

        foreach($rows as $rowIndex => $row){
            if($rowIndex === 0) continue;
            $sampleData = [];

            foreach($mapping as $field => $col){
                $index = intval(str_replace('col','',$col));
                $sampleData[$field] = $row[$index] ?? null;
            }

            if(empty($sampleData['sample_code'])){
                $sampleData['sample_code'] =
                    'S-'.date('Y').'-'.str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);
            }

            $sampleData['created_by'] = Auth::id();
            Sample::create($sampleData);
            $created++;
        }

        return response()->json([
            'created' => $created
        ]);
    }
}