<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Method;
use Illuminate\Support\Facades\Auth;

class MethodController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'schema_json' => 'required|array',
            'limits_json' => 'nullable|array'
        ]);

        $method = Method::create([
            'name'=>$data['name'],
            'schema_json'=>$data['schema_json'],
            'limits_json'=>$data['limits_json'] ?? null,
            'created_by' => $request->user()->id
        ]);

        $method->base_method_id = $method->id;
        $method->save();

        return response()->json($method,201);
    }
    public function index()
    {
        return Method::paginate(20);
    }
    public function show($id)
    {
        return Method::findOrFail($id);
    }
    public function update(Request $request,$id)
    {
        $method = Method::findOrFail($id);

        if ($method->status !== 'DRAFT') {
            return response()->json([
                'error'=>'Published methods cannot be edited'
            ],403);
        }

        $method->update($request->only([
            'schema_json',
            'limits_json'
        ]));

        return $method;
    }
    public function version($id)
    {
        $method = Method::findOrFail($id);

        $new = Method::create([
            'base_method_id'=>$method->base_method_id,
            'name'=>$method->name,
            'version'=>$method->version+1,
            'schema_json'=>$method->schema_json,
            'limits_json'=>$method->limits_json,
            'created_by' => Auth::id(),
            'status'=>'DRAFT'
        ]);

        return $new;
    }
    public function publish($id)
    {
        $method = Method::findOrFail($id);

        $method->status='PUBLISHED';
        $method->save();

        return response()->json([
            'message'=>'method published'
        ]);
    }
    public function indexView()
    {
        $methods = Method::paginate(20);
        return view('methods.index', compact('methods'));
    }
    public function createView()
    {
        return view('methods.form');
    }

    public function editView($id)
    {
        $method = Method::findOrFail($id);
        return view('methods.form', compact('method'));
    }
}
