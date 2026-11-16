<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Support\RingkasanBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard operator sekolah: ringkasan berkas sekolah ini
     * (menunggu / diproses / selesai / ditolak) dan beberapa berkas terbaru.
     * Data sekolah diambil dari akun yang sedang login (guard 'sekolah').
     */
    public function index(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $semua = Pengajuan::where('npsn', $sekolah->npsn)->latest()->get();

        $tahap = RingkasanBerkas::hitung($semua);

        return view('dashboard.sekolah.index', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
            'stat' => [
                'menunggu' => $tahap['pending'],
                'diproses' => $tahap['menunggu_ttd'],
                'selesai'  => $tahap['terkirim'],
                'ditolak'  => $tahap['rejected'],
            ],
            'terbaru' => $semua->take(4),
        ]);
    }
}
