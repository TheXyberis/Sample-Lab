<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Unauthorized action. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
