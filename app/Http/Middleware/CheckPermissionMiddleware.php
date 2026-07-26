<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Superadmin melepaskan seluruh pengecekan izin (Akses Penuh)
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Pengecekan centang izin granular per menu & aksi
        if (!$user->hasPermissionTo($permission)) {
            abort(403, "Akses Ditolak (403 Forbidden). Anda tidak memiliki izin [{$permission}] untuk mengakses menu atau fitur ini.");
        }

        return $next($request);
    }
}
