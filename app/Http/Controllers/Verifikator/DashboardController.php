<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Data di controller ini MASIH DUMMY (contoh statis), sama seperti AdminController.
 *
 * Nanti setelah tabel "pengajuan" (hasil upload KGB dkk dari sekolah) dan tabel
 * "penugasan" (sekolah <-> verifikator, dikelola halaman Admin > Penugasan Sekolah)
 * sudah ada, ganti mockBerkasMasuk() di bawah dengan query:
 *   - ambil sekolah yang ditugaskan ke verifikator yang sedang login
 *   - ambil pengajuan berstatus 'pending' dari sekolah-sekolah itu
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $verifikator = Auth::guard('verifikator')->user();

        $semuaBerkas = $this->mockBerkasMasuk();

        // Verifikator hanya melihat berkas dari sekolah di kecamatan yang jadi tanggung jawabnya.
        $berkasMasuk = array_values(array_filter(
            $semuaBerkas,
            fn ($b) => $b['kecamatan'] === $verifikator->kecamatan
        ));

        return view('dashboard.verifikator.index', [
            'verifikatorNama'      => $verifikator->nama,
            'verifikatorKecamatan' => $verifikator->kecamatan,
            'berkasMasuk'          => $berkasMasuk,
        ]);
    }

    private function mockBerkasMasuk(): array
    {
        return [
            ['sekolah' => 'SDN 1 Makassar', 'npsn' => '40311001', 'kecamatan' => 'Mariso', 'layanan' => 'KGB', 'tanggal' => now()->subDays(2)->toDateString(), 'status' => 'pending'],
            ['sekolah' => 'SDN 5 Mariso', 'npsn' => '40311005', 'kecamatan' => 'Mariso', 'layanan' => 'KGB', 'tanggal' => now()->subDay()->toDateString(), 'status' => 'pending'],
            ['sekolah' => 'SMPN 2 Makassar', 'npsn' => '40311002', 'kecamatan' => 'Mamajang', 'layanan' => 'KGB', 'tanggal' => now()->toDateString(), 'status' => 'pending'],
        ];
    }
}
