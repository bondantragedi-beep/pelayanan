<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Verifikator;
use Illuminate\Http\Request;

class LayarKantorController extends Controller
{
    /**
     * Dashboard layar kantor: dipasang di TV/monitor kantor untuk dilihat
     * atasan. Menampilkan semua pengajuan (data masuk dari sekolah),
     * diurutkan dari yang PALING LAMA di atas, lengkap dengan nama
     * verifikator penanggung jawabnya.
     *
     * Halaman ini TIDAK memakai middleware auth (sengaja) supaya bisa
     * langsung ditampilkan di layar kantor tanpa perlu ada yang login
     * terus-menerus. Kalau nanti perlu dibatasi, tambahkan middleware
     * di routes/web.php.
     */
    public function index(Request $request)
    {
        $verifikatorPerKecamatan = Verifikator::where('is_admin', false)
            ->get()
            ->keyBy('kecamatan');

        $verifikatorPerNip = Verifikator::all()->keyBy('nip');

        $data = Pengajuan::with('sekolah')
            ->oldest() // data terlama di atas
            ->get()
            ->map(function (Pengajuan $item) use ($verifikatorPerKecamatan, $verifikatorPerNip) {
                if ($item->diproses_oleh_nip && $verifikatorPerNip->has($item->diproses_oleh_nip)) {
                    $namaVerifikator = $verifikatorPerNip[$item->diproses_oleh_nip]->nama;
                } else {
                    $kecamatan = $item->sekolah->kecamatan ?? null;
                    $namaVerifikator = $verifikatorPerKecamatan->get($kecamatan)?->nama ?? 'Belum ada verifikator';
                }

                return [
                    'sekolah'     => $item->sekolah->nama_sekolah ?? '—',
                    'kecamatan'   => $item->sekolah->kecamatan ?? '—',
                    'layanan'     => $item->labelLayanan(),
                    'verifikator' => $namaVerifikator,
                    'tanggal'     => $item->created_at,
                    'tahap'       => $item->tahapVerifikator(), // pending | menunggu_ttd | terkirim | rejected
                ];
            });

        return view('layar-kantor', [
            'data'  => $data,
            'ringkasan' => [
                'menunggu'     => $data->where('tahap', 'pending')->count(),
                'menunggu_ttd' => $data->where('tahap', 'menunggu_ttd')->count(),
                'terkirim'     => $data->where('tahap', 'terkirim')->count(),
                'ditolak'      => $data->where('tahap', 'rejected')->count(),
            ],
        ]);
    }
}
