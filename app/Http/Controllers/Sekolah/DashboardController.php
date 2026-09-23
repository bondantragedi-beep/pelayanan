<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard operator sekolah.
     *
     * Catatan: $sekolahNama & $sekolahNpsn masih placeholder karena
     * autentikasi guard 'sekolah' belum dibuat. Setelah login sekolah
     * berfungsi, ganti bagian ini dengan data dari Auth::guard('sekolah')->user().
     */
    public function index(Request $request)
    {
        return view('dashboard.sekolah.index', [
            'sekolahNama' => $request->session()->get('sekolah_nama', 'Nama Sekolah'),
            'sekolahNpsn' => $request->session()->get('sekolah_npsn', '-'),
        ]);
    }
}
