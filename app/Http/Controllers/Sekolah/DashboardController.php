<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard operator sekolah.
     * Data sekolah sekarang diambil dari akun yang sedang login (guard 'sekolah').
     */
    public function index(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        return view('dashboard.sekolah.index', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
        ]);
    }
}
