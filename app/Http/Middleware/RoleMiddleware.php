<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        $role = strtoupper((string) $user->role);
        $allowed = array_map('strtoupper', $roles);
        abort_unless(in_array($role, $allowed, true), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
