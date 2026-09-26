<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KgbController extends Controller
{
    /**
     * Tampilkan form pengajuan KGB beserta persyaratan.
     */
    public function create(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        return view('dashboard.sekolah.kgb', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
        ]);
    }

    /**
     * Simpan berkas KGB yang diunggah, sekaligus catat sebagai pengajuan
     * berstatus 'pending' supaya muncul di menu "Daftar Berkas Saya" dan
     * nanti bisa diproses oleh Verifikator.
     */
    public function store(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $request->validate([
            'sk_pangkat_terakhir' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'sk_kgb_terakhir'     => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'sk_pangkat_terakhir.required' => 'Berkas SK Pangkat Terakhir wajib diunggah.',
            'sk_kgb_terakhir.required'     => 'Berkas SK KGB Terakhir wajib diunggah.',
            '*.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            '*.max'   => 'Ukuran berkas maksimal 2 MB.',
        ]);

        $skPangkatPath = $request->file('sk_pangkat_terakhir')
            ->store("kgb/{$sekolah->npsn}/sk-pangkat-terakhir", 'public');

        $skKgbPath = $request->file('sk_kgb_terakhir')
            ->store("kgb/{$sekolah->npsn}/sk-kgb-terakhir", 'public');

        Pengajuan::create([
            'npsn'          => $sekolah->npsn,
            'jenis_layanan' => 'kgb',
            'berkas'        => [
                'sk_pangkat_terakhir' => $skPangkatPath,
                'sk_kgb_terakhir'     => $skKgbPath,
            ],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('sekolah.berkas')
            ->with('success', 'Berkas KGB berhasil diunggah dan menunggu verifikasi dari Dinas.');
    }
}
