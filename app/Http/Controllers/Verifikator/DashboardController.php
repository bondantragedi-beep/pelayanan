<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Daftar berkas yang menunggu diproses, hanya dari sekolah yang
     * SUNGGUHAN ditugaskan ke verifikator ini oleh Admin (relasi
     * sekolah.verifikator_nip), bukan lagi berdasarkan kecamatan yang
     * kebetulan sama.
     */
    public function index(Request $request)
    {
        $verifikator = Auth::guard('verifikator')->user();

        $berkasMasuk = Pengajuan::with('sekolah')
            ->whereHas('sekolah', fn ($q) => $q->where('verifikator_nip', $verifikator->nip))
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
