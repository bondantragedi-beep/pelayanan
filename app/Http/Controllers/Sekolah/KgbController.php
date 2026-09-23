<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KgbController extends Controller
{
    /**
     * Tampilkan form pengajuan KGB beserta persyaratan.
     */
    public function create(Request $request)
    {
        return view('dashboard.sekolah.kgb', [
            'sekolahNama' => $request->session()->get('sekolah_nama', 'Nama Sekolah'),
            'sekolahNpsn' => $request->session()->get('sekolah_npsn', '-'),
        ]);
    }

    /**
     * Simpan berkas KGB yang diunggah.
     *
     * PENTING: penyimpanan di bawah ini baru menyimpan FILE ke disk publik.
     * Belum ada tabel/model "pengajuan_kgb" untuk mencatat status
     * (menunggu verifikasi / disetujui / ditolak) dan mengaitkan berkas
     * dengan sekolah + pegawai yang mengajukan, karena guard/login sekolah
     * juga belum dibuat. Setelah itu siap, tambahkan:
     *   - migration + model PengajuanKgb (sekolah_id, nama_pegawai, status, dst)
     *   - simpan $skPangkatPath & $skKgbPath ke record tersebut
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sk_pangkat_terakhir' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'sk_kgb_terakhir'     => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'sk_pangkat_terakhir.required' => 'Berkas SK Pangkat Terakhir wajib diunggah.',
            'sk_kgb_terakhir.required'     => 'Berkas SK KGB Terakhir wajib diunggah.',
            '*.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            '*.max'   => 'Ukuran berkas maksimal 2 MB.',
        ]);

        $skPangkatPath = $request->file('sk_pangkat_terakhir')
            ->store('kgb/sk-pangkat-terakhir', 'public');

        $skKgbPath = $request->file('sk_kgb_terakhir')
            ->store('kgb/sk-kgb-terakhir', 'public');

        // TODO: ganti dengan penyimpanan ke tabel pengajuan_kgb setelah model dibuat.

        return redirect()
            ->route('sekolah.kgb')
            ->with('success', 'Berkas KGB berhasil diunggah dan menunggu verifikasi dari Dinas.');
    }
}
