<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventOperatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isOperator()) {
            abort(403, 'Akses Ditolak. Role Operator tidak memiliki akses ke modul Skema Penilaian Grade.');
        }

        return $next($request);
    }
}
