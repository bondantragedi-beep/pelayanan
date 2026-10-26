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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BerkasController extends Controller
{
    /** Saran "pejabat penetap SK terakhir" di form (boleh juga diketik bebas). */
    private const SK_PEJABAT = [
        'PRESIDEN REPUBLIK INDONESIA',
        'Walikota Makassar',
        'Kepala Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Kota Makassar',
        'Kepala Dinas Pendidikan Kota Makassar',
    ];

    /** Golongan ruang PNS. */
    private const GOLONGAN_PNS = [
        'I/a', 'I/b', 'I/c', 'I/d',
        'II/a', 'II/b', 'II/c', 'II/d',
        'III/a', 'III/b', 'III/c', 'III/d',
        'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e',
    ];

    /** Golongan PPPK (angka romawi saja, tanpa huruf ruang). */
    private const GOLONGAN_PPPK = [
        'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX',
        'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII',
    ];

    private const KETERANGAN_PNS = 'ybs adalah Pegawai Negeri Sipil Kota Makassar';

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
     * dokumennya. Form punya dua bentuk (PNS / PPPK) sesuai format surat KGB
     * masing-masing; pilihannya di dropdown "Jenis Pegawai".
     *
     * Form juga bisa dibuka lagi untuk MENGUBAH isian selama surat belum
     * dikirim ke sekolah (status processed tapi hasil_path masih kosong).
     */
    public function terimaForm(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanBukanTunjangan($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        $npsnBinaan    = $verifikator->sekolahBinaan()->pluck('npsn');
        $daftarPegawai = Pegawai::with('sekolah')->whereIn('npsn', $npsnBinaan)->get();
        $referensiGaji = ReferensiGaji::orderBy('golongan')->orderBy('masa_kerja_tahun')->get();

        return view('dashboard.verifikator.terima', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'pengajuan'            => $pengajuan,
            'verifikasi'           => $pengajuan->verifikasiKgb,
            'daftarPegawai'        => $daftarPegawai,
            'referensiGaji'        => $referensiGaji,
            'daftarSkPejabat'      => self::SK_PEJABAT,
            'golonganPns'          => self::GOLONGAN_PNS,
            'golonganPppk'         => self::GOLONGAN_PPPK,
        ]);
    }

    /**
     * Simpan isian form penerimaan. Pengajuan ditandai 'processed' (diterima
     * secara administratif); surat baru masuk Inbox sekolah setelah hasil
     * tanda tangan diunggah lewat langkah "kirim".
     */
    public function terimaSimpan(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanBukanTunjangan($pengajuan);
        $this->pastikanSudahDilihat($pengajuan);
        $this->pastikanBisaDiisi($pengajuan);

        $verifikator = Auth::guard('verifikator')->user();

        // Gaji boleh diketik dengan titik pemisah ("5.687.200"); sisakan angkanya saja.
        $request->merge([
            'gaji_pokok_lama' => preg_replace('/\D/', '', (string) $request->input('gaji_pokok_lama')),
            'gaji_pokok_baru' => preg_replace('/\D/', '', (string) $request->input('gaji_pokok_baru')),
        ]);

        $jenis   = $request->input('jenis_pegawai') === 'PPPK' ? 'PPPK' : 'PNS';
        $isPppk  = $jenis === 'PPPK';
        $golonganValid = $isPppk ? self::GOLONGAN_PPPK : self::GOLONGAN_PNS;

        $d = $request->validate([
            'jenis_pegawai'    => ['required', 'in:PNS,PPPK'],
            'nip_pegawai'      => ['required', 'digits:18', 'exists:pegawai,nip'],
            'pangkat_jabatan'  => ['required', 'string', 'max:255'],
            'unit_kerja'       => ['required', 'string', 'max:200'],

            'sk_pejabat'       => ['required', 'string', 'max:200'],
            'tanggal_sk'       => ['required', 'date'],
            'nomor_sk'         => ['required', 'string', 'max:100'],

            'golongan'         => ['required', 'string', Rule::in($golonganValid)],
            'masa_kerja_tahun' => ['required', 'integer', 'min:2', 'max:50'],
            'masa_kerja_bulan' => ['nullable', 'integer', 'min:0', 'max:11'],
            'gaji_pokok_lama'  => ['required', 'integer', 'min:1'],
            'gaji_pokok_baru'  => ['required', 'integer', 'min:1'],
            'tmt_baru'         => ['required', 'date'],

            // Khusus PNS
            'keterangan'       => ['nullable', 'string', 'max:255'],

            // Khusus PPPK
            'sk_berlaku_gaji'       => ['nullable', 'date', 'required_if:jenis_pegawai,PPPK'],
            'perjanjian_mulai'      => ['nullable', 'date', 'required_if:jenis_pegawai,PPPK'],
            'perjanjian_selesai'    => ['nullable', 'date', 'after:perjanjian_mulai', 'required_if:jenis_pegawai,PPPK'],
            'masa_perjanjian_tahun' => ['nullable', 'integer', 'min:0', 'max:50', 'required_if:jenis_pegawai,PPPK'],
        ], [
            'nip_pegawai.exists'            => 'NIP tidak ditemukan di data pegawai.',
            'golongan.in'                   => 'Golongan tidak sesuai dengan jenis pegawai yang dipilih.',
            'masa_kerja_tahun.min'          => 'Masa kerja minimal 2 tahun (KGB diberikan tiap 2 tahun).',
            'gaji_pokok_lama.required'      => 'Gaji pokok lama wajib diisi.',
            'gaji_pokok_baru.required'      => 'Gaji pokok baru wajib diisi.',
            'perjanjian_selesai.after'      => 'Tanggal akhir perjanjian harus setelah tanggal mulainya.',
            '*.required_if'                 => 'Wajib diisi untuk pegawai PPPK.',
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

        VerifikasiKgb::updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            [
                'jenis_pegawai'   => $jenis,
                'nip_pegawai'     => $pegawai->nip,
                'nama_pegawai'    => $pegawai->nama,
                'pangkat_jabatan' => $d['pangkat_jabatan'],
                'unit_kerja'      => $d['unit_kerja'],

                'sk_pejabat'      => $d['sk_pejabat'],
                'tanggal_sk'      => $d['tanggal_sk'],
                'nomor_sk'        => $d['nomor_sk'],
                'sk_berlaku_gaji' => $isPppk ? $d['sk_berlaku_gaji'] : null,

                'golongan'         => $d['golongan'],
                'masa_kerja_tahun' => $d['masa_kerja_tahun'],
                'masa_kerja_bulan' => $isPppk ? (int) ($d['masa_kerja_bulan'] ?? 0) : 0,
                'gaji_pokok_lama'  => $d['gaji_pokok_lama'],
                'gaji_pokok_baru'  => $d['gaji_pokok_baru'],
                'tmt_baru'         => $d['tmt_baru'],

                'keterangan' => $isPppk ? null : (trim((string) ($d['keterangan'] ?? '')) ?: self::KETERANGAN_PNS),

                'masa_perjanjian_tahun' => $isPppk ? $d['masa_perjanjian_tahun'] : null,
                'perjanjian_mulai'      => $isPppk ? $d['perjanjian_mulai'] : null,
                'perjanjian_selesai'    => $isPppk ? $d['perjanjian_selesai'] : null,

                'dibuat_oleh_nip' => $verifikator->nip,
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
            ->with('success', 'Data tersimpan. Silakan unduh PDF surat untuk diunggah ke sistem persuratan.');
    }

    /**
     * Halaman cetak: link unduh PDF + form upload hasil TTD untuk dikirim ke sekolah.
     */
    public function cetak(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        abort_if(! $pengajuan->dataVerifikasi(), 404, 'Berkas ini belum diisi formulir penerimaannya.');

        return view('dashboard.verifikator.cetak', [
            'verifikatorNama'      => Auth::guard('verifikator')->user()->nama,
            'verifikatorKecamatan' => Auth::guard('verifikator')->user()->kecamatan,
            'pengajuan'            => $pengajuan,
        ]);
    }

    /**
     * Unduh PDF surat KGB (format PNS atau PPPK sesuai isian). Kode seperti
     * ${nomor_naskah} dan ${tanggal_naskah} sengaja dibiarkan apa adanya di PDF,
     * karena akan diganti oleh sistem persuratan lain.
     */
    public function cetakPdf(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        $this->pastikanBukanTunjangan($pengajuan);

        $v = $pengajuan->verifikasiKgb;
        abort_if(! $v, 404, 'Berkas ini belum diisi formulir penerimaannya.');

        $pdf = Pdf::loadView($v->viewPdf(), ['v' => $v])->setPaper('legal', 'portrait');

        return $pdf->download("KGB-{$v->jenis_pegawai}-{$v->nip_pegawai}.pdf");
    }

    /**
     * Upload hasil scan yang sudah ditandatangani pimpinan. Setelah ini,
     * berkas dianggap selesai: hilang dari daftar "menunggu TTD" dan
     * langsung muncul di menu Inbox sekolah.
     */
    public function kirim(Request $request, Pengajuan $pengajuan)
    {
        $this->pastikanWilayahSama($pengajuan);
        abort_if(! $pengajuan->dataVerifikasi(), 404, 'Berkas ini belum diisi formulir penerimaannya.');

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

    /** Form & PDF di controller ini khusus KGB; Tunjangan punya controller sendiri. */
    private function pastikanBukanTunjangan(Pengajuan $pengajuan): void
    {
        abort_if($pengajuan->isTunjangan(), 404, 'Gunakan form tunjangan untuk berkas ini.');
    }

    private function pastikanSudahDilihat(Pengajuan $pengajuan): void
    {
        if (! $pengajuan->dilihat_pada) {
            throw ValidationException::withMessages([
                'dokumen' => 'Anda harus membuka & memeriksa dokumen terlebih dahulu sebelum bisa Terima/Tolak.',
            ]);
        }
    }

    /** Boleh diisi/diubah: masih pending, atau sudah diisi tetapi surat belum dikirim ke sekolah. */
    private function pastikanBisaDiisi(Pengajuan $pengajuan): void
    {
        $bisa = $pengajuan->status === 'pending'
            || ($pengajuan->status === 'processed' && ! $pengajuan->hasil_path);

        abort_unless($bisa, 409, 'Berkas ini sudah selesai diproses dan tidak bisa diubah lagi.');
    }

    private function pastikanMasihPending(Pengajuan $pengajuan): void
    {
        abort_if($pengajuan->status !== 'pending', 409, 'Berkas ini sudah diproses sebelumnya.');
    }
}
