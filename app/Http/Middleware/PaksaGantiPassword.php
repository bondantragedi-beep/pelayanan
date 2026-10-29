<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sekolah yang masih memakai password awal (wajib_ganti_password = true)
 * tidak boleh membuka halaman lain sebelum menggantinya.
 * Dipasang di grup rute sekolah, KECUALI halaman ubah password itu sendiri.
 */
class PaksaGantiPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $sekolah = $request->user('sekolah');

        if ($sekolah && $sekolah->wajib_ganti_password) {
            return redirect()->route('sekolah.password.edit');
        }

        return $next($request);
    }
}
