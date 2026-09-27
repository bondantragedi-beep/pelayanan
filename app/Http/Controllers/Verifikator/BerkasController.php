<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Models\ReferensiGaji;
use App\Models\VerifikasiKgb;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BerkasController extends Controller
{
    /** Daftar jenis Surat Keputusan untuk dropdown di form penerimaan. */
    private const JENIS_SK = [
        'Keputusan Wali Kota Makassar',
        'Keputusan Kepala Badan Kepegawaian dan Pengembangan SDM',
        'Keputusan Kepala Dinas Pendidikan Kota Makassar',
    ];

    /** Pilihan golongan ruang PNS/PPPK untuk dropdown golongan. */
    private const GOLONGAN = [
        'I/a', 'I/b', 'I/c', 'I/d',
        'II/a', 'II/b', 'II/c', 'II/d',
        'III/a', 'III/b', 'III/c', 'III/d',
        'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e',
    ];

    /**
     * Halaman detail satu berkas: info sekolah, daftar dokumen untuk dilihat,
     * serta tombol Terima/Tolak (aktif hanya jika dokumen sudah dilihat).
     */
    public function show(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);

        return view('dashboard.verifikator.show', [
            'verifikatorNama'      => Auth::guard('verifikator')->user()->nama,
            'verifikatorKecamatan' => Auth::guard('verifikator')->user()->kecamatan,
            'pengajuan'            => $pengajuan,
        ]);
    }

    /**
     * Buka salah satu dokumen berkas. Route ini yang mencatat "dilihat_pada"
     * sebelum meneruskan (redirect) ke file aslinya — jadi Terima/Tolak
     * tidak bisa dipakai tanpa pernah lewat sini dulu.
     */
    public function lihatDokumen(Request $request, Pengajuan $pengajuan, string $key)
    {
        $this->pastikanWilayahSama($pengajuan);

        $path = $pengajuan->berkas[$key] ?? null;
        abort_if(! $path, 404, 'Dokumen tidak ditemukan.');

        if (! $pengajuan->dilihat_pada) {
            $pengajuan->update([
                'dilihat_pada'     => now(),
                'dilihat_oleh_nip' => Auth::guard('verifikator')->id(),
            ]);
        }

        return redirect(asset('storage/' . $path));
    }

    /**
     * Tolak pengajuan. Wajib sudah dilihat dokumennya (dicek di server,
     * bukan cuma disembunyikan di tampilan) dan wajib isi alasan.
     */
    public function tolak(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanMasihPending($pengajuan);

        $request->validate([
            'alasan_ditolak' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan_ditolak.required' => 'Alasan penolakan wajib diisi.',
            'alasan_ditolak.min'      => 'Alasan penolakan minimal 10 karakter, jelaskan secukupnya.',
        ]);

        $pengajuan->update([
            'status'            => 'rejected',
            'alasan_ditolak'    => $request->input('alasan_ditolak'),
            'diproses_oleh_nip' => Auth::guard('verifikator')->id(),
            'diproses_pada'     => now(),
        ]);

        return redirect()
            ->route('verifikator.dashboard')
            ->with('success', 'Pengajuan dari ' . $pengajuan->sekolah->nama_sekolah . ' berhasil ditolak.');
    }

    /**
     * Form penerimaan berkas (setelah Terima ditekan). Wajib sudah dilihat
     * dokumennya. Menyiapkan data pegawai (untuk pencarian NIP) dan
     * referensi gaji (untuk perhitungan gaji pokok lama) ke tampilan.
     */
    public function terimaForm(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanMasihPending($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $npsnBinaan = $verifikator->sekolahBinaan()->pluck('npsn');
        $daftarPegawai = Pegawai::whereIn('npsn', $npsnBinaan)->get();
        $referensiGaji = ReferensiGaji::orderBy('golongan')->orderBy('masa_kerja_tahun')->get();

        return view('dashboard.verifikator.terima', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'pengajuan'            => $pengajuan,
            'daftarPegawai'        => $daftarPegawai,
            'referensiGaji'        => $referensiGaji,
            'daftarJenisSk'        => self::JENIS_SK,
            'daftarGolongan'       => self::GOLONGAN,
        ]);
    }

    /**
     * Simpan hasil pengisian form penerimaan. Pengajuan ditandai 'processed'
     * (diterima secara administratif) — tapi belum masuk Inbox sekolah
     * sampai hasil_path diisi lewat langkah "kirim" (setelah cetak + TTD).
     */
    public function terimaSimpan(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanMasihPending($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $validated = $request->validate([
            'nip_pegawai'      => ['required', 'digits:18', 'exists:pegawai,nip'],
            'pangkat_jabatan'  => ['required', 'string', 'max:150'],
            'jenis_sk'         => ['required', 'string', 'in:' . implode(',', self::JENIS_SK)],
            'nomor_sk'         => ['required', 'string', 'max:100'],
            'tanggal_sk'       => ['required', 'date'],
            'masa_kerja_tahun' => ['required', 'integer', 'min:2', 'max:40'],
            'golongan'         => ['required', 'string', 'in:' . implode(',', self::GOLONGAN)],
            'tmt_baru'         => ['required', 'date'],
        ], [
            'nip_pegawai.exists' => 'NIP tidak ditemukan di data pegawai wilayah Anda.',
        ]);

        $pegawai = Pegawai::findOrFail($validated['nip_pegawai']);

        $gajiPokokLama = ReferensiGaji::where('golongan', $validated['golongan'])
            ->where('masa_kerja_tahun', $validated['masa_kerja_tahun'] - 2)
            ->value('gaji_pokok');

        VerifikasiKgb::updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            [
                'nip_pegawai'      => $pegawai->nip,
                'nama_pegawai'     => $pegawai->nama,
                'pangkat_jabatan'  => $validated['pangkat_jabatan'],
                'jenis_sk'         => $validated['jenis_sk'],
                'nomor_sk'         => $validated['nomor_sk'],
                'tanggal_sk'       => $validated['tanggal_sk'],
                'masa_kerja_tahun' => $validated['masa_kerja_tahun'],
                'gaji_pokok_lama'  => $gajiPokokLama,
                'golongan'         => $validated['golongan'],
                'tmt_baru'         => $validated['tmt_baru'],
                'dibuat_oleh_nip'  => $verifikator->nip,
            ]
        );

        $pengajuan->update([
            'status'            => 'processed',
            'diproses_oleh_nip' => $verifikator->nip,
            'diproses_pada'     => now(),
        ]);

        return redirect()
            ->route('verifikator.berkas.cetak', $pengajuan)
            ->with('success', 'Data berhasil disimpan. Silakan cetak dokumen untuk ditandatangani pimpinan.');
    }

    /**
     * Halaman cetak: link unduh PDF + form upload hasil TTD untuk dikirim ke sekolah.
     */
    public function cetak(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        abort_if(! $pengajuan->verifikasiKgb, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        return view('dashboard.verifikator.cetak', [
            'verifikatorNama'      => Auth::guard('verifikator')->user()->nama,
            'verifikatorKecamatan' => Auth::guard('verifikator')->user()->kecamatan,
            'pengajuan'            => $pengajuan,
        ]);
    }

    /**
     * Hasilkan file PDF draf SK KGB untuk diunduh & ditandatangani pimpinan.
     */
    public function cetakPdf(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        abort_if(! $pengajuan->verifikasiKgb, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        $pdf = Pdf::loadView('pdf.kgb', ['pengajuan' => $pengajuan]);

        return $pdf->stream("KGB-{$pengajuan->sekolah->npsn}-{$pengajuan->id}.pdf");
    }

    /**
     * Upload hasil scan yang sudah ditandatangani pimpinan. Setelah ini,
     * berkas dianggap selesai: hilang dari daftar "menunggu TTD" dan
     * langsung muncul di menu Inbox sekolah.
     */
    public function kirim(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        abort_if(! $pengajuan->verifikasiKgb, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        $request->validate([
            'hasil_ttd' => ['required', 'file', 'mimes:pdf', 'max:4096'],
        ], [
            'hasil_ttd.required' => 'Unggah dulu hasil scan yang sudah ditandatangani pimpinan.',
            'hasil_ttd.mimes'    => 'Hasil TTD harus berupa file PDF.',
        ]);

        $path = $request->file('hasil_ttd')->store("hasil-ttd/{$pengajuan->npsn}", 'public');

        $pengajuan->update(['hasil_path' => $path]);

        return redirect()
            ->route('verifikator.riwayat')
            ->with('success', 'Dokumen terkirim ke ' . $pengajuan->sekolah->nama_sekolah . '. Sudah muncul di Inbox sekolah.');
    }

    /**
     * Riwayat: berkas yang sudah ditangani verifikator ini — masih menunggu
     * TTD, sudah terkirim, maupun yang ditolak.
     */
    public function riwayat(Request $request)
    {
        $verifikator = Auth::guard('verifikator')->user();

        $daftar = Pengajuan::whereHas('sekolah', fn ($q) => $q->where('verifikator_nip', $verifikator->nip))
            ->where('status', '!=', 'pending')
            ->latest('diproses_pada')
            ->get();

        return view('dashboard.verifikator.riwayat', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'daftar'               => $daftar,
        ]);
    }

    /** ---------------- Guard kecil dipakai berulang di atas ---------------- */

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

    private function pastikanMasihPending(Pengajuan $pengajuan): void
    {
        abort_if($pengajuan->status !== 'pending', 409, 'Berkas ini sudah diproses sebelumnya.');
    }
}
