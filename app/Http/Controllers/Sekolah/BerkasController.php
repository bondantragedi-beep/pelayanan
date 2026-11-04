<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Unduh/lihat dokumen final (hasil TTD) milik sekolah ini. Hanya berkas
     * sekolah sendiri yang bisa dibuka; dikirim lewat aplikasi, bukan tautan publik.
     */
    public function dokumen(Request $request, Pengajuan $pengajuan)
    {
        $sekolah = Auth::guard('sekolah')->user();

        abort_unless($pengajuan->npsn === $sekolah->npsn, 403, 'Berkas ini bukan milik sekolah Anda.');
        abort_if(! $pengajuan->hasil_path, 404, 'Dokumen belum tersedia.');

        $disk = Storage::disk('public');
        abort_unless($disk->exists($pengajuan->hasil_path), 404, 'File dokumen tidak ditemukan di penyimpanan.');

        return $disk->response($pengajuan->hasil_path);
    }
}
