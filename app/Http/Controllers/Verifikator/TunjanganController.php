<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Models\VerifikasiTunjangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Pengisian data & cetak Surat Pengantar untuk layanan Tunjangan.
 *
 * Melihat dokumen, menolak, dan mengirim hasil TTD tetap memakai
 * Verifikator\BerkasController (sama persis dengan layanan KGB).
 */
class TunjanganController extends Controller
{
    public function terimaForm(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanTunjangan($pengajuan);
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $npsnBinaan    = $verifikator->sekolahBinaan()->pluck('npsn');
        $daftarPegawai = Pegawai::with('sekolah')->whereIn('npsn', $npsnBinaan)->get();

        return view('dashboard.verifikator.tunjangan-terima', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'pengajuan'            => $pengajuan,
            'verifikasi'           => $pengajuan->verifikasiTunjangan,
            'daftarPegawai'        => $daftarPegawai,
        ]);
    }

    public function terimaSimpan(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanTunjangan($pengajuan);
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $d = $request->validate([
            'jenis_pegawai'    => ['required', 'in:PNS,PPPK'],
            'jenis_tunjangan'  => ['required', 'in:istri,suami,anak'],
            'anak_ke'          => ['nullable', 'integer', 'min:1', 'max:3', 'required_if:jenis_tunjangan,anak'],
            'nip_pegawai'      => ['required', 'digits:18', 'exists:pegawai,nip'],
            'pangkat_golongan' => ['required', 'string', 'max:255'],
            'jabatan'          => ['required', 'string', 'max:255'],
            'unit_kerja'       => ['required', 'string', 'max:200'],
        ], [
            'nip_pegawai.exists'      => 'NIP tidak ditemukan di data pegawai.',
            'anak_ke.required_if'     => 'Isi anak ke berapa yang diajukan tunjangannya.',
            'anak_ke.max'             => 'Tunjangan anak hanya sampai anak ke-3.',
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

        VerifikasiTunjangan::updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            [
                'jenis_pegawai'    => $d['jenis_pegawai'],
                'jenis_tunjangan'  => $d['jenis_tunjangan'],
                'anak_ke'          => $d['jenis_tunjangan'] === 'anak' ? (int) $d['anak_ke'] : null,
                'nip_pegawai'      => $pegawai->nip,
                'nama_pegawai'     => $pegawai->nama,
                'pangkat_golongan' => $d['pangkat_golongan'],
                'jabatan'          => $d['jabatan'],
                'unit_kerja'       => $d['unit_kerja'],
                'dibuat_oleh_nip'  => $verifikator->nip,
            ]
        );

        if ($pengajuan->status === 'pending') {
            $pengajuan->update([
                'status'            => 'processed',
                'diproses_oleh_nip' => $verifikator->nip,
                'diproses_pada'     => now(),
            ]);
        }

        return redirect()
            ->route('verifikator.berkas.cetak', $pengajuan)
            ->with('success', 'Data tersimpan. Silakan unduh PDF Surat Pengantar untuk diunggah ke sistem persuratan.');
    }

    /**
     * Unduh PDF Surat Pengantar. Kode ${nomor_naskah}, ${tanggal_naskah},
     * ${ttd_pengirim}, ${nama_pengirim}, ${nip_pengirim} sengaja dibiarkan
     * apa adanya di PDF (diganti oleh sistem persuratan lain).
     */
    public function cetakPdf(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanTunjangan($pengajuan);
        $this->pastikanWilayahSama($pengajuan);

        $v = $pengajuan->verifikasiTunjangan;
        abort_if(! $v, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        $pdf = Pdf::loadView('pdf.tunjangan', ['v' => $v])->setPaper('a4', 'portrait');

        return $pdf->download("Tunjangan-{$v->jenis_tunjangan}-{$v->nip_pegawai}.pdf");
    }

    /* ---------------- Guard kecil (sama dengan BerkasController) ---------------- */

    private function pastikanTunjangan(Pengajuan $pengajuan): void
    {
        abort_unless($pengajuan->isTunjangan(), 404, 'Berkas ini bukan pengajuan tunjangan.');
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
