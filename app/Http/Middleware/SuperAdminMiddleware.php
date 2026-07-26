<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Fitur pengubahan master data ini hanya dapat diakses oleh Superadmin.');
        }

        return $next($request);
    }
}
