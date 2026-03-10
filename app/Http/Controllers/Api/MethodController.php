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
        $schema = is_string($request->schema_json) ? json_decode($request->schema_json, true) : $request->schema_json;
        $limits = is_string($request->limits_json ?? null) ? json_decode($request->limits_json, true) : ($request->limits_json ?? null);

        $data = $request->validate([
            'name' => 'required|string',
        ]);

        if (!is_array($schema)) {
            return redirect()->back()->withErrors(['schema_json' => 'Invalid JSON'])->withInput();
        }
        $data['schema_json'] = $schema;
        $data['limits_json'] = $limits;

        $method = Method::create([
            'name'=>$data['name'],
            'schema_json'=>$data['schema_json'],
            'limits_json'=>$data['limits_json'] ?? null,
            'created_by' => $request->user()->id
        ]);

        $method->base_method_id = $method->id;
        $method->save();

        if ($request->expectsJson()) {
            return response()->json($method,201);
        } else {
            return redirect()->route('methods.index')->with('success', 'Method created');
        }
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
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Published methods cannot be edited'], 403);
            }
            return redirect()->back()->with('error', 'Published methods cannot be edited');
        }

        $schema = is_string($request->schema_json) ? json_decode($request->schema_json, true) : $request->schema_json;
        $limits = is_string($request->limits_json ?? null) ? json_decode($request->limits_json, true) : ($request->limits_json ?? null);

        if (!is_array($schema)) {
            return redirect()->back()->withErrors(['schema_json' => 'Invalid JSON'])->withInput();
        }

        $method->update([
            'name' => $request->name,
            'schema_json' => $schema,
            'limits_json' => $limits,
        ]);

        if ($request->expectsJson()) {
            return $method;
        } else {
            return redirect()->route('methods.index')->with('success', 'Method updated');
        }
    }
    public function version(Request $request, $id)
    {
        $method = Method::findOrFail($id);
        $new = Method::create([
            'base_method_id' => $method->base_method_id,
            'name' => $method->name,
            'version' => $method->version + 1,
            'schema_json' => $method->schema_json,
            'limits_json' => $method->limits_json,
            'created_by' => Auth::id(),
            'status' => 'DRAFT'
        ]);

        if ($request->expectsJson()) {
            return response()->json($new);
        }
        return redirect()->route('methods.edit', $new->id)->with('success', 'New version created');
    }
    public function publish(Request $request, $id)
    {
        $method = Method::findOrFail($id);
        $method->status = 'PUBLISHED';
        $method->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'method published']);
        }
        return redirect()->route('methods.index')->with('success', 'Method published');
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
    public function showView($id)
    {
        $method = Method::findOrFail($id);
        return view('methods.show', compact('method'));
    }
}
