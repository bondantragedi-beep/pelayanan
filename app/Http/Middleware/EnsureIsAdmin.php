<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Hanya izinkan verifikator dengan is_admin = true untuk mengakses halaman admin.
     * Verifikator biasa (is_admin = false) yang mencoba masuk akan ditolak.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $verifikator = Auth::guard('verifikator')->user();

        if (! $verifikator || ! $verifikator->is_admin) {
            abort(403, 'Halaman ini khusus untuk Admin.');
        }

        return $next($request);
    }
}
