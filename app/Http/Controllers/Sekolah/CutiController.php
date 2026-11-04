<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Support\LamaCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Layanan Cuti (Peraturan BKN No. 24 Tahun 2017) — sisi operator sekolah.
 *
 * Sekolah memilih pegawai, mencentang jenis cuti, mengisi alasan, tanggal
 * mulai & selesai, alamat selama cuti, lalu mengunggah berkas. Data pegawai
 * (nama, jabatan, pangkat, masa kerja, dst.) TIDAK diketik sekolah: diambil
 * dari tabel pegawai saat verifikator memproses.
 */
class CutiController extends Controller
{
    public function create(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $daftarPegawai = Pegawai::where('npsn', $sekolah->npsn)
            ->orderBy('nama')
            ->get(['nip', 'nama', 'nama_jabatan', 'hp', 'telp'])
            ->map(fn ($p) => [
                'nip'     => $p->nip,
                'nama'    => $p->nama,
                'jabatan' => $p->nama_jabatan,
                'telp'    => $p->hp ?: $p->telp,
            ])->values();

        return view('dashboard.sekolah.cuti', [
            'sekolahNama'   => $sekolah->nama_sekolah,
            'sekolahNpsn'   => $sekolah->npsn,
            'daftarPegawai' => $daftarPegawai,
        ]);
    }

    public function store(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $jenis  = (string) $request->input('jenis_cuti');
        $ibadah = $jenis === 'tahunan' && $request->boolean('cuti_ibadah');
        $labelTambahan = LamaCuti::berkasTambahan($jenis, $ibadah);

        $berkas = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];

        $request->validate([
            'nip_pegawai'         => ['required', 'digits:18'],
            'jenis_cuti'          => ['required', 'in:' . implode(',', LamaCuti::JENIS_SEKOLAH)],
            'cuti_ibadah'         => ['nullable', 'boolean'],
            'alasan'              => ['required', 'string', 'max:500'],
            'tanggal_mulai'       => ['required', 'date'],
            'tanggal_selesai'     => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alamat_cuti'         => ['required', 'string', 'max:500'],
            'telp'                => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'formulir_cuti'       => $berkas,
            'sk_pangkat_terakhir' => $berkas,
            'rekap_absen'         => $berkas,
            'berkas_tambahan'     => $labelTambahan
                ? $berkas
                : ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'nip_pegawai.required'         => 'Pilih pegawai yang mengajukan cuti.',
            'nip_pegawai.digits'           => 'NIP pegawai harus 18 digit.',
            'jenis_cuti.required'          => 'Centang jenis cuti yang diambil.',
            'jenis_cuti.in'                => 'Jenis cuti ini tidak bisa diajukan lewat sekolah.',
            'alasan.required'              => 'Alasan cuti wajib diisi.',
            'tanggal_mulai.required'       => 'Tanggal mulai cuti wajib diisi.',
            'tanggal_selesai.required'     => 'Tanggal selesai cuti wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'alamat_cuti.required'         => 'Alamat selama menjalankan cuti wajib diisi.',
            'telp.regex'                   => 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'formulir_cuti.required'       => 'Formulir Permohonan Cuti wajib diunggah.',
            'sk_pangkat_terakhir.required' => 'SK Pangkat Terakhir wajib diunggah.',
            'rekap_absen.required'         => 'Rekap Absen 2 Tahun Terakhir wajib diunggah.',
            'berkas_tambahan.required'     => ($labelTambahan ?? 'Berkas tambahan') . ' wajib diunggah.',
            '*.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            '*.max'   => 'Ukuran berkas maksimal 2 MB.',
        ]);

        // Pegawai harus benar-benar pegawai sekolah ini (dicek di server, bukan hanya di dropdown).
        $pegawai = Pegawai::where('nip', $request->input('nip_pegawai'))
            ->where('npsn', $sekolah->npsn)
            ->first();
        if (! $pegawai) {
            throw ValidationException::withMessages([
                'nip_pegawai' => 'Pegawai ini tidak terdaftar pada sekolah Anda.',
            ]);
        }

        // Cuti tahunan maks. 12 hari kerja (kecuali ibadah); jenis lain mengikuti batas di aturan.
        $pesan = LamaCuti::periksaBatas($jenis, $request->input('tanggal_mulai'), $request->input('tanggal_selesai'), $ibadah);
        if ($pesan) {
            throw ValidationException::withMessages(['tanggal_selesai' => $pesan]);
        }

        $lama = LamaCuti::hitung($jenis, $request->input('tanggal_mulai'), $request->input('tanggal_selesai'));

        $simpan = fn (string $field, string $folder) => $request->file($field)
            ->store("cuti/{$sekolah->npsn}/{$folder}", 'public');

        $dokumen = [
            'formulir_cuti'       => $simpan('formulir_cuti', 'formulir-cuti'),
            'sk_pangkat_terakhir' => $simpan('sk_pangkat_terakhir', 'sk-pangkat-terakhir'),
            'rekap_absen'         => $simpan('rekap_absen', 'rekap-absen'),
        ];
        if ($request->hasFile('berkas_tambahan')) {
            $dokumen['berkas_tambahan'] = $simpan('berkas_tambahan', 'berkas-tambahan');
        }

        Pengajuan::create([
            'npsn'          => $sekolah->npsn,
            'jenis_layanan' => 'cuti',
            'berkas'        => $dokumen,
            'detail'        => [
                'nip_pegawai'     => $pegawai->nip,
                'nama_pegawai'    => $pegawai->nama,
                'jenis_cuti'      => $jenis,
                'cuti_ibadah'     => $ibadah,
                'alasan'          => trim($request->input('alasan')),
                'tanggal_mulai'   => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $request->input('tanggal_selesai'),
                'lama_angka'      => $lama['angka'],
                'lama_satuan'     => $lama['satuan'],
                'alamat_cuti'     => trim($request->input('alamat_cuti')),
                'telp'            => trim((string) $request->input('telp')) ?: null,
            ],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('sekolah.berkas')
            ->with('success', 'Berkas Cuti berhasil diunggah dan menunggu verifikasi dari Dinas.');
    }
}
