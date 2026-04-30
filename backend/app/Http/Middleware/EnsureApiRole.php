<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->attributes->get('api_user');
        $role = $user['role'] ?? null;

        if (! $role || ! in_array($role, $roles, true)) {
            return ApiResponse::error('Forbidden for this role.', 403, 'AUTH_FORBIDDEN_ROLE');
        }

        return $next($request);
    }
}
