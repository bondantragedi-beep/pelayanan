<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerkasController extends Controller
{
    /**
     * Daftar semua berkas yang pernah diunggah sekolah ini, beserta status
     * (menunggu / diterima / ditolak) dan alasan penolakan bila ada.
     */
    public function index(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $daftarBerkas = Pengajuan::where('npsn', $sekolah->npsn)
            ->latest()
            ->get();

        return view('dashboard.sekolah.berkas', [
            'sekolahNama'  => $sekolah->nama_sekolah,
            'sekolahNpsn'  => $sekolah->npsn,
            'daftarBerkas' => $daftarBerkas,
        ]);
    }

    /**
     * Inbox: berkas yang statusnya sudah diproses (processed) dan punya
     * hasil_path (dokumen final yang sudah ditandatangani pimpinan),
     * siap diunduh sekolah.
     */
    public function inbox(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $inbox = Pengajuan::where('npsn', $sekolah->npsn)
            ->where('status', 'processed')
            ->whereNotNull('hasil_path')
            ->latest('diproses_pada')
            ->get();

        return view('dashboard.sekolah.inbox', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
            'inbox'       => $inbox,
        ]);
    }
}
