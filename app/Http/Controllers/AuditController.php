<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:audit:read');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with(['user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(50)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();
        $entityTypes = AuditLog::distinct()->pluck('entity_type')->filter();
        $actions = AuditLog::distinct()->pluck('action')->filter();

        return view('audit.index', compact('auditLogs', 'users', 'entityTypes', 'actions'));
    }

    public function show($id)
    {
        $auditLog = AuditLog::with(['user'])->findOrFail($id);
        
        $diff = [];
        if ($auditLog->diff_json) {
            $diff = is_array($auditLog->diff_json) ? $auditLog->diff_json : json_decode($auditLog->diff_json, true);
        }

        return view('audit.show', compact('auditLog', 'diff'));
    }
}
