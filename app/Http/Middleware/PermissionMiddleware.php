<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $role = $user->role;

        $permissions = $this->getRolePermissions($role);

        $required = str_contains($permission, '|')
            ? explode('|', $permission)
            : [$permission];

        foreach ($required as $p) {
            if (in_array(trim($p), $permissions)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
    
    private function getRolePermissions($role)
    {
        $rolePermissions = [
            'Admin' => [
                'samples:create', 'samples:read', 'samples:update', 'samples:archive', 'samples:delete', 'samples:import',
                'measurements:read', 'measurements:plan', 'measurements:start', 'measurements:finish', 'measurements:assign',
                'results:read', 'results:edit', 'results:submit', 'results:review', 'results:approve', 'results:lock', 'results:unlock',
                'methods:read', 'methods:create', 'methods:version', 'methods:publish', 'methods:update',
                'users:manage', 'reports:generate', 'reports:download', 'audit:read', 'integrations:manage'
            ],
            'Manager' => [
                'samples:create', 'samples:read', 'samples:update', 'samples:import',
                'measurements:read', 'measurements:plan', 'measurements:assign',
                'results:read', 'results:review', 'results:approve', 'results:lock',
                'methods:read',
                'reports:generate', 'reports:download', 'audit:read'
            ],
            'Laborant' => [
                'samples:create', 'samples:read', 'samples:import',
                'measurements:read', 'measurements:plan', 'measurements:start', 'measurements:finish',
                'results:read', 'results:edit', 'results:submit'
            ],
            'QC/Reviewer' => [
                'samples:read',
                'measurements:read',
                'results:read', 'results:review', 'results:approve', 'results:lock', 'results:unlock',
                'methods:read',
                'reports:download', 'audit:read'
            ],
            'Client' => [
                'samples:read',
                'reports:download'
            ]
        ];
        
        return $rolePermissions[$role] ?? [];
    }
}
