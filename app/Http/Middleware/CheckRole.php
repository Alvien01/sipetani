<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::guard('personel')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('personel')->user();

        if ($role === 'komandan' && ($user->role_id !== 1 && $user->role->name !== 'komandan')) {
            abort(403, 'Unauthorized access. Only Komandan can access this page.');
        }

        return $next($request);
    }
}
