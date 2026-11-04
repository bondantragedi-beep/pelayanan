<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Models\SaldoCuti;
use App\Models\VerifikasiCuti;
use App\Support\LamaCuti;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Pengisian data & cetak PDF "Formulir Permintaan dan Pemberian Cuti".
 *
 * Melihat dokumen, menolak, dan mengirim hasil TTD tetap memakai
 * Verifikator\BerkasController (sama dengan layanan KGB/Tunjangan).
 *
 * Alur verifikator:
 *  1. Periksa dokumen yang diunggah sekolah (Formulir Permohonan Cuti, SK Pangkat, Rekap Absen, berkas tambahan).
 *  2. Lengkapi "V. Catatan Cuti" (sisa cuti tahunan N-2, N-1, N, dan catatan cuti lain).
 *  3. Centang "VII. Pertimbangan Atasan Langsung" (Disetujui bila dokumen persetujuan sekolah ada)
 *     dan "VIII. Keputusan Pejabat yang Memberikan Cuti".
 *  4. Unduh PDF -> proses di sistem persuratan -> unggah hasil TTD (BerkasController@kirim).
 */
class CutiController extends Controller
{
    public function terimaForm(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanCuti($pengajuan);
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $npsnBinaan    = $verifikator->sekolahBinaan()->pluck('npsn');
        $daftarPegawai = Pegawai::with('sekolah')->whereIn('npsn', $npsnBinaan)->get();

        return view('dashboard.verifikator.cuti-terima', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'pengajuan'            => $pengajuan,
            'verifikasi'           => $pengajuan->verifikasiCuti,
            'daftarPegawai'        => $daftarPegawai,
            'isian'                => $this->isianAwal($pengajuan),
            'pilihan'              => VerifikasiCuti::PILIHAN_KEPUTUSAN,
        ]);
    }

    public function terimaSimpan(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanCuti($pengajuan);
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();
        $pilihan     = implode(',', array_keys(VerifikasiCuti::PILIHAN_KEPUTUSAN));
        $hak         = (int) config('cuti.hak_tahunan', 12);

        $d = $request->validate([
            'nip_pegawai'      => ['required', 'digits:18', 'exists:pegawai,nip'],
            'jabatan'          => ['nullable', 'string', 'max:255'],
            'unit_kerja'       => ['nullable', 'string', 'max:200'],
            'masa_kerja'       => ['nullable', 'string', 'max:50'],
            'pangkat_golongan' => ['nullable', 'string', 'max:255'],

            'jenis_cuti'       => ['required', 'in:' . implode(',', array_keys(LamaCuti::JENIS))],
            'cuti_ibadah'      => ['nullable', 'boolean'],
            'alasan'           => ['required', 'string', 'max:500'],
            'tanggal_mulai'    => ['required', 'date'],
            'tanggal_selesai'  => ['required', 'date', 'after_or_equal:tanggal_mulai'],

            'sisa_n2' => ['required', 'integer', "min:0", "max:{$hak}"],
            'sisa_n1' => ['required', 'integer', "min:0", "max:{$hak}"],
            'sisa_n'  => ['required', 'integer', "min:0", "max:{$hak}"],
            'ket_n2'  => ['nullable', 'string', 'max:100'],
            'ket_n1'  => ['nullable', 'string', 'max:100'],
            'ket_n'   => ['nullable', 'string', 'max:100'],
            'catatan_besar'           => ['nullable', 'string', 'max:100'],
            'catatan_sakit'           => ['nullable', 'string', 'max:100'],
            'catatan_melahirkan'      => ['nullable', 'string', 'max:100'],
            'catatan_alasan_penting'  => ['nullable', 'string', 'max:100'],
            'catatan_luar_tanggungan' => ['nullable', 'string', 'max:100'],

            'alamat_cuti'  => ['required', 'string', 'max:500'],
            'telp'         => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'nama_kepsek'  => ['nullable', 'string', 'max:255'],
            'pertimbangan' => ['required', "in:{$pilihan}"],
            'keputusan'    => ['required', "in:{$pilihan}"],
        ], [
            'nip_pegawai.exists'           => 'NIP tidak ditemukan di data pegawai.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'sisa_n2.*' => "Sisa cuti N-2 harus angka 0–{$hak}.",
            'sisa_n1.*' => "Sisa cuti N-1 harus angka 0–{$hak}.",
            'sisa_n.*'  => "Sisa cuti tahun berjalan harus angka 0–{$hak}.",
            'telp.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
        ]);

        $pegawai = Pegawai::findOrFail($d['nip_pegawai']);

        // Pegawai harus berasal dari sekolah yang memang ditugaskan ke verifikator ini.
        $dalamBinaan = $pegawai->npsn
            && $verifikator->sekolahBinaan()->where('npsn', $pegawai->npsn)->exists();
        if (! $dalamBinaan) {
            throw ValidationException::withMessages([
                'nip_pegawai' => 'Pegawai ini tidak terhubung ke sekolah yang ditugaskan kepada Anda.',
            ]);
        }

        $ibadah = $d['jenis_cuti'] === 'tahunan' && $request->boolean('cuti_ibadah');

        $pesan = LamaCuti::periksaBatas($d['jenis_cuti'], $d['tanggal_mulai'], $d['tanggal_selesai'], $ibadah);
        if ($pesan) {
            throw ValidationException::withMessages(['tanggal_selesai' => $pesan]);
        }
        $lama = LamaCuti::hitung($d['jenis_cuti'], $d['tanggal_mulai'], $d['tanggal_selesai']);

        // N = tahun mulai cuti; N-1 dan N-2 = dua tahun sebelumnya.
        $n = (int) substr($d['tanggal_mulai'], 0, 4);
        $catatan = [
            'tahunan' => [
                ['tahun' => $n - 2, 'sisa' => (int) $d['sisa_n2'], 'ket' => trim((string) ($d['ket_n2'] ?? ''))],
                ['tahun' => $n - 1, 'sisa' => (int) $d['sisa_n1'], 'ket' => trim((string) ($d['ket_n1'] ?? ''))],
                ['tahun' => $n,     'sisa' => (int) $d['sisa_n'],  'ket' => trim((string) ($d['ket_n'] ?? ''))],
            ],
            'besar'           => trim((string) ($d['catatan_besar'] ?? '')),
            'sakit'           => trim((string) ($d['catatan_sakit'] ?? '')),
            'melahirkan'      => trim((string) ($d['catatan_melahirkan'] ?? '')),
            'alasan_penting'  => trim((string) ($d['catatan_alasan_penting'] ?? '')),
            'luar_tanggungan' => trim((string) ($d['catatan_luar_tanggungan'] ?? '')),
        ];

        VerifikasiCuti::updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            [
                'nip_pegawai'      => $pegawai->nip,
                'nama_pegawai'     => $pegawai->nama,
                'jabatan'          => $d['jabatan'] ?? null,
                'unit_kerja'       => $d['unit_kerja'] ?? null,
                'masa_kerja'       => $d['masa_kerja'] ?? null,
                'pangkat_golongan' => $d['pangkat_golongan'] ?? null,

                'jenis_cuti'      => $d['jenis_cuti'],
                'cuti_ibadah'     => $ibadah,
                'alasan'          => trim($d['alasan']),
                'tanggal_mulai'   => $d['tanggal_mulai'],
                'tanggal_selesai' => $d['tanggal_selesai'],
                'lama_angka'      => $lama['angka'],
                'lama_satuan'     => $lama['satuan'],

                'catatan'     => $catatan,
                'alamat_cuti' => trim($d['alamat_cuti']),
                'telp'        => trim((string) ($d['telp'] ?? '')) ?: null,

                'nama_kepsek'  => trim((string) ($d['nama_kepsek'] ?? '')) ?: null,
                'pertimbangan' => $d['pertimbangan'],
                'keputusan'    => $d['keputusan'],

                'dibuat_oleh_nip' => $verifikator->nip,
            ]
        );

        // Catatan sisa yang diisi verifikator direkam sebagai riwayat pegawai,
        // jadi pengajuan cuti berikutnya otomatis menampilkannya.
        SaldoCuti::aturSisa($pegawai->nip, $n - 2, (int) $d['sisa_n2']);
        SaldoCuti::aturSisa($pegawai->nip, $n - 1, (int) $d['sisa_n1']);
        SaldoCuti::aturSisa($pegawai->nip, $n,     (int) $d['sisa_n']);

        if ($pengajuan->status === 'pending') {
            $pengajuan->update([
                'status'            => 'processed',
                'diproses_oleh_nip' => $verifikator->nip,
                'diproses_pada'     => now(),
            ]);
        }

        return redirect()
            ->route('verifikator.berkas.cetak', $pengajuan)
            ->with('success', 'Data tersimpan. Silakan unduh PDF Formulir Cuti untuk diunggah ke sistem persuratan.');
    }

    /**
     * Unduh PDF Formulir Permintaan dan Pemberian Cuti. Kode ${tanggal_naskah},
     * ${jabatan_tujuan}, dan ${ttd_pengirim} sengaja dibiarkan apa adanya di PDF
     * (diganti oleh sistem persuratan), sama seperti surat KGB & Tunjangan.
     */
    public function cetakPdf(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanCuti($pengajuan);
        $this->pastikanWilayahSama($pengajuan);

        $v = $pengajuan->verifikasiCuti;
        abort_if(! $v, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        $pdf = Pdf::loadView('pdf.cuti', [
            'v'            => $v,
            'namaSekolah'  => $pengajuan->sekolah->nama_sekolah,
            'pegawai'      => Pegawai::find($v->nip_pegawai),
        ])->setPaper('legal', 'portrait');

        return $pdf->download("Cuti-{$v->jenis_cuti}-{$v->nip_pegawai}.pdf");
    }

    /* ---------------- Nilai awal form ---------------- */

    /**
     * Isian awal form: dari data yang diketik sekolah + data pegawai + saldo cuti.
     * Bila verifikasi sudah pernah disimpan, form memakai nilai yang tersimpan
     * (lihat blade); array ini hanya untuk yang belum pernah diisi.
     */
    private function isianAwal(Pengajuan $pengajuan): array
    {
        $det     = $pengajuan->detail ?? [];
        $nip     = $det['nip_pegawai'] ?? null;
        $pegawai = $nip ? Pegawai::find($nip) : null;

        $mulai = $det['tanggal_mulai'] ?? null;
        $n     = $mulai ? (int) substr($mulai, 0, 4) : (int) now()->format('Y');

        $kepsek = Pegawai::where('npsn', $pengajuan->npsn)
            ->where('nama_jabatan', 'like', 'Kepala Sekolah%')
            ->value('nama');

        $masaKerja = null;
        if ($pegawai && $pegawai->masa_kerja_tahun !== null) {
            $masaKerja = $pegawai->masa_kerja_tahun . ' Tahun ' . (int) $pegawai->masa_kerja_bulan . ' Bulan';
        }

        return [
            'nip_pegawai'      => $nip,
            'jabatan'          => $pegawai->nama_jabatan ?? null,
            'unit_kerja'       => $pegawai ? ($pegawai->unit_kerja ?: ($pegawai->sekolah->nama_sekolah ?? null)) : null,
            'masa_kerja'       => $masaKerja,
            'pangkat_golongan' => $pegawai->golongan_pangkat ?? null,

            'jenis_cuti'      => $det['jenis_cuti'] ?? 'tahunan',
            'cuti_ibadah'     => (bool) ($det['cuti_ibadah'] ?? false),
            'alasan'          => $det['alasan'] ?? '',
            'tanggal_mulai'   => $det['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $det['tanggal_selesai'] ?? '',
            'alamat_cuti'     => $det['alamat_cuti'] ?? '',
            'telp'            => $det['telp'] ?? ($pegawai->hp ?? $pegawai->telp ?? ''),

            'tahun_n' => $n,
            'sisa_n2' => $nip ? SaldoCuti::sisa($nip, $n - 2) : (int) config('cuti.hak_tahunan', 12),
            'sisa_n1' => $nip ? SaldoCuti::sisa($nip, $n - 1) : (int) config('cuti.hak_tahunan', 12),
            'sisa_n'  => $nip ? SaldoCuti::sisa($nip, $n)     : (int) config('cuti.hak_tahunan', 12),

            'nama_kepsek' => $kepsek,
        ];
    }

    /* ---------------- Guard kecil (sama dengan TunjanganController) ---------------- */

    private function pastikanCuti(Pengajuan $pengajuan): void
    {
        abort_unless($pengajuan->isCuti(), 404, 'Berkas ini bukan pengajuan cuti.');
    }

    private function pastikanWilayahSama(Pengajuan $pengajuan): void
    {
        $verifikator = Auth::guard('verifikator')->user();
        abort_unless($pengajuan->sekolah->verifikator_nip === $verifikator->nip, 403, 'Berkas ini bukan sekolah yang ditugaskan ke Anda.');
    }

    private function pastikanSudahDilihat(Pengajuan $pengajuan): void
    {
        if (! $pengajuan->dilihat_pada) {
            throw ValidationException::withMessages([
                'dokumen' => 'Anda harus membuka & memeriksa dokumen terlebih dahulu sebelum bisa Terima/Tolak.',
            ]);
        }
    }

    private function pastikanBisaDiisi(Pengajuan $pengajuan): void
    {
        $bisa = $pengajuan->status === 'pending'
            || ($pengajuan->status === 'processed' && ! $pengajuan->hasil_path);

        abort_unless($bisa, 409, 'Berkas ini sudah selesai diproses dan tidak bisa diubah lagi.');
    }
}
