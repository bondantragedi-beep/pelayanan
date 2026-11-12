<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
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

        // Pengelompokan sama dengan tahapVerifikator(): pending, menunggu_ttd, terkirim, rejected.
        $tahap = $semua->groupBy(fn (Pengajuan $p) => $p->tahapVerifikator())->map->count();

        return view('dashboard.sekolah.index', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
            'stat' => [
                'menunggu' => (int) ($tahap['pending'] ?? 0),
                'diproses' => (int) ($tahap['menunggu_ttd'] ?? 0),
                'selesai'  => (int) ($tahap['terkirim'] ?? 0),
                'ditolak'  => (int) ($tahap['rejected'] ?? 0),
            ],
            'terbaru' => $semua->take(4),
        ]);
    }
}
