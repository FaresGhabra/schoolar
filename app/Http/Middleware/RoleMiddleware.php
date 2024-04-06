<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = $request->user()->load('role')->toArray();
        $rolesArr = is_array($roles) ? $roles : explode('|', $roles);
        if ($request->user() && in_array($user['role']['name'], $rolesArr)) {
            return $next($request);
        } else {
            abort(401, 'Unauthorized');
        }
    }
}