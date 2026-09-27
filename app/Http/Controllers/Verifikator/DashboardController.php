<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Daftar berkas yang menunggu diproses, hanya dari sekolah di kecamatan
     * tanggung jawab verifikator yang sedang login.
     */
    public function index(Request $request)
    {
        $verifikator = Auth::guard('verifikator')->user();

        $berkasMasuk = Pengajuan::with('sekolah')
            ->whereHas('sekolah', fn ($q) => $q->where('kecamatan', $verifikator->kecamatan))
            ->where('status', 'pending')
            ->oldest()
            ->get();

        return view('dashboard.verifikator.index', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'berkasMasuk'          => $berkasMasuk,
        ]);
    }
}
