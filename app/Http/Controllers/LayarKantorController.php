<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Verifikator;
use App\Support\RingkasanBerkas;
use App\Support\UrutanBerkas;
use Illuminate\Http\Request;

class LayarKantorController extends Controller
{
    /**
     * Layar monitoring kantor: dipasang di TV/monitor untuk dilihat atasan.
     *
     * URUTAN (lihat App\Support\UrutanBerkas):
     *   1. Tertunda  — belum selesai & sudah >= config('berkas.batas_tertunda_hari') hari (default 7), paling atas
     *   2. Dalam proses
     *   3. Ditolak
     *   4. Terkirim ke sekolah — otomatis turun ke paling bawah
     *
     * Halaman ini TIDAK memakai middleware auth (sengaja) supaya bisa langsung
     * ditampilkan di layar kantor tanpa perlu ada yang login terus-menerus.
     * Kalau nanti perlu dibatasi, tambahkan middleware di routes/web.php.
     */
    public function index(Request $request)
    {
        $verifikatorPerKecamatan = Verifikator::where('is_admin', false)->get()->keyBy('kecamatan');
        $verifikatorPerNip       = Verifikator::all()->keyBy('nip');
        $batas                   = UrutanBerkas::batasHari();

        $urut = UrutanBerkas::urutkan(Pengajuan::with('sekolah')->get());

        $no = 0;
        $data = $urut->map(function (Pengajuan $item) use ($verifikatorPerKecamatan, $verifikatorPerNip, &$no) {
            // Prioritas: yang memproses berkas -> verifikator yang ditugaskan ke sekolah (penugasan admin)
            // -> cadangan terakhir: verifikator dengan kecamatan yang sama.
            if ($item->diproses_oleh_nip && $verifikatorPerNip->has($item->diproses_oleh_nip)) {
                $namaVerifikator = $verifikatorPerNip[$item->diproses_oleh_nip]->nama;
            } elseif (($nipTugas = $item->sekolah->verifikator_nip ?? null) && $verifikatorPerNip->has($nipTugas)) {
                $namaVerifikator = $verifikatorPerNip[$nipTugas]->nama;
            } else {
                $kecamatan = $item->sekolah->kecamatan ?? null;
                $namaVerifikator = $verifikatorPerKecamatan->get($kecamatan)?->nama ?? 'Belum ada verifikator';
            }

            $umur = UrutanBerkas::umurHari($item);

            return [
                'no'          => ++$no,
                'sekolah'     => $item->sekolah->nama_sekolah ?? '—',
                'kecamatan'   => $item->sekolah->kecamatan ?? '—',
                'layanan'     => $item->labelLayanan(),
                'jenis'       => $item->jenis_layanan,
                'verifikator' => $namaVerifikator,
                'tanggal'     => $item->created_at,
                'umur'        => $umur,
                'umur_teks'   => $umur === 0 ? 'Hari ini' : ($umur === 1 ? 'Kemarin' : "{$umur} hari lalu"),
                'tahap'       => RingkasanBerkas::tahap($item), // pending | menunggu_ttd | terkirim | rejected
                'tingkat'     => UrutanBerkas::tingkat($item),
            ];
        });

        $per = $data->groupBy('tingkat');

        $kelompok = collect([
            UrutanBerkas::TERTUNDA => ['kunci' => 'tertunda', 'judul' => 'Perlu perhatian', 'ket' => "Belum selesai lebih dari {$batas} hari"],
            UrutanBerkas::PROSES   => ['kunci' => 'proses',   'judul' => 'Dalam proses',    'ket' => "Belum selesai kurang dari {$batas} hari"],
            UrutanBerkas::DITOLAK  => ['kunci' => 'ditolak',  'judul' => 'Ditolak',         'ket' => 'Menunggu perbaikan dari sekolah'],
            UrutanBerkas::TERKIRIM => ['kunci' => 'terkirim', 'judul' => 'Selesai',         'ket' => 'Surat sudah terkirim ke sekolah'],
        ])->map(fn ($g, $tingkat) => $g + ['baris' => $per->get($tingkat, collect())->values()])
          ->filter(fn ($g) => $g['baris']->isNotEmpty())
          ->values();

        return view('layar-kantor', [
            'kelompok'  => $kelompok,
            'total'     => $data->count(),
            'batas'     => $batas,
            'ringkasan' => [
                'tertunda'     => $per->get(UrutanBerkas::TERTUNDA, collect())->count(),
                'menunggu'     => $data->where('tahap', 'pending')->count(),
                'menunggu_ttd' => $data->where('tahap', 'menunggu_ttd')->count(),
                'terkirim'     => $data->where('tahap', 'terkirim')->count(),
                'ditolak'      => $data->where('tahap', 'rejected')->count(),
            ],
        ]);
    }
}
