<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TunjanganController extends Controller
{
    /**
     * Form pengajuan tunjangan: pilih PNS/PPPK, pilih jenis tunjangan
     * (Istri/Suami atau Anak), lalu unggah berkas persyaratan.
     */
    public function create(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        return view('dashboard.sekolah.tunjangan', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
        ]);
    }

    /**
     * Simpan berkas tunjangan sebagai pengajuan 'pending'.
     *
     * Wajib : KP4, KK, SK Terakhir, Buku Nikah.
     * Akta Kelahiran Anak: wajib bila yang diajukan mencakup tunjangan anak
     * (Anak, atau Istri/Suami + Anak).
     * Dicek di server (bukan cuma disembunyikan di tampilan).
     */
    public function store(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $rule = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];

        $request->validate([
            'jenis_pegawai'       => ['required', 'in:PNS,PPPK'],
            'jenis_tunjangan'     => ['required', 'in:istri_suami,anak,istri_suami_anak'],
            'kp4'                 => $rule,
            'kk'                  => $rule,
            'sk_terakhir'         => $rule,
            'buku_nikah'          => $rule,
            'akta_kelahiran_anak' => array_merge(
                ['required_if:jenis_tunjangan,anak,istri_suami_anak', 'nullable'],
                array_slice($rule, 1)
            ),
        ], [
            'jenis_pegawai.required'   => 'Pilih dulu status pegawai: PNS atau PPPK.',
            'jenis_tunjangan.required' => 'Pilih jenis tunjangan yang diajukan.',
            'kp4.required'             => 'Berkas KP4 wajib diunggah.',
            'kk.required'              => 'Berkas Kartu Keluarga (KK) wajib diunggah.',
            'sk_terakhir.required'     => 'Berkas SK Terakhir wajib diunggah.',
            'buku_nikah.required'      => 'Berkas Buku Nikah wajib diunggah.',
            'akta_kelahiran_anak.required_if' => 'Akta Kelahiran Anak wajib diunggah bila mengajukan tunjangan anak.',
            '*.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            '*.max'   => 'Ukuran berkas maksimal 2 MB.',
        ]);

        $anak = in_array($request->input('jenis_tunjangan'), ['anak', 'istri_suami_anak'], true);

        $simpan = fn (string $field, string $folder) => $request->file($field)
            ->store("tunjangan/{$sekolah->npsn}/{$folder}", 'public');

        $berkas = [
            'kp4'         => $simpan('kp4', 'kp4'),
            'kk'          => $simpan('kk', 'kk'),
            'sk_terakhir' => $simpan('sk_terakhir', 'sk-terakhir'),
            'buku_nikah'  => $simpan('buku_nikah', 'buku-nikah'),
        ];

        if ($anak && $request->hasFile('akta_kelahiran_anak')) {
            $berkas['akta_kelahiran_anak'] = $simpan('akta_kelahiran_anak', 'akta-kelahiran-anak');
        }

        Pengajuan::create([
            'npsn'          => $sekolah->npsn,
            'jenis_layanan' => 'tunjangan',
            'berkas'        => $berkas,
            'detail'        => [
                'jenis_pegawai'   => $request->input('jenis_pegawai'),
                'jenis_tunjangan' => $request->input('jenis_tunjangan'),
            ],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('sekolah.berkas')
            ->with('success', 'Berkas Tunjangan berhasil diunggah dan menunggu verifikasi dari Dinas.');
    }
}
