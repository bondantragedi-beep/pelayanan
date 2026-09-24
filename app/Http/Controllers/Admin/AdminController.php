<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Semua data di controller ini MASIH DUMMY (contoh statis).
 *
 * Nanti setelah database berikut ini siap, ganti setiap method di bawah
 * agar mengambil data dari tabel yang sesungguhnya:
 *   - tabel sekolah        : npsn, nama_sekolah, jenjang, status, kelurahan, kecamatan
 *   - tabel pegawai        : nip, nama, status, npsn, tmt, kecamatan, golongan
 *   - tabel verifikator    : nip, nama, kecamatan yang ditangani
 *   - tabel penugasan      : relasi sekolah <-> verifikator (dikelola halaman Penugasan Sekolah)
 *   - tabel pengajuan      : sekolah_id, jenis_layanan, verifikator_id, tanggal_masuk, status
 *                            (pending / processed / rejected), alasan_ditolak
 */
class AdminController extends Controller
{
    /**
     * Halaman ringkasan admin.
     */
    public function index(Request $request)
    {
        $dataMasukTerbaru = $this->mockDataMasuk();

        return view('dashboard.admin.index', [
            'adminNama' => $request->session()->get('admin_nama', 'Admin'),
            'adminNip'  => $request->session()->get('admin_nip', '-'),

            'totalSekolah'            => 42,
            'totalVerifikator'        => 6,
            'belumDiproses'           => collect($dataMasukTerbaru)->where('status', 'pending')->count(),
            'sudahDiproses'           => collect($dataMasukTerbaru)->where('status', 'processed')->count(),
            'sekolahBelumDitugaskan'  => 3,

            'dataMasukTerbaru' => array_slice($dataMasukTerbaru, 0, 5),
        ]);
    }

    /**
     * Halaman penugasan sekolah ke verifikator.
     */
    public function penugasan(Request $request)
    {
        $daftarVerifikator = $this->mockVerifikator();
        $daftarSekolah = $this->mockSekolah();

        // Filter sederhana berdasarkan query string (masih di atas data dummy).
        if ($kecamatan = $request->query('kecamatan')) {
            $daftarSekolah = array_values(array_filter($daftarSekolah, fn ($s) => $s['kecamatan'] === $kecamatan));
        }
        if ($statusTugas = $request->query('status_tugas')) {
            $daftarSekolah = array_values(array_filter($daftarSekolah, function ($s) use ($statusTugas) {
                return $statusTugas === 'assigned' ? !empty($s['verifikator']) : empty($s['verifikator']);
            }));
        }

        return view('dashboard.admin.penugasan', [
            'adminNama' => $request->session()->get('admin_nama', 'Admin'),
            'adminNip'  => $request->session()->get('admin_nip', '-'),

            'daftarSekolah'     => $daftarSekolah,
            'daftarVerifikator' => $daftarVerifikator,
            'daftarKecamatan'   => collect($this->mockSekolah())->pluck('kecamatan')->unique()->values()->all(),
        ]);
    }

    /**
     * Simpan penugasan verifikator untuk satu sekolah.
     *
     * TODO: setelah tabel penugasan ada, simpan relasi npsn <-> verifikator_id ke database.
     * Saat ini baru redirect kembali dengan pesan sukses (belum benar-benar tersimpan).
     */
    public function updatePenugasan(Request $request, string $npsn)
    {
        $request->validate([
            'verifikator_id' => ['nullable', 'string'],
        ]);

        // TODO: SekolahVerifikator::updateOrCreate(['npsn' => $npsn], ['verifikator_id' => $request->verifikator_id]);

        return redirect()
            ->route('admin.penugasan')
            ->with('success', "Penugasan verifikator untuk sekolah NPSN {$npsn} berhasil disimpan.");
    }

    /**
     * Halaman data masuk & monitoring, dengan filter tanggal/bulan/tahun/status.
     */
    public function dataMasuk(Request $request)
    {
        $dataMasuk = $this->mockDataMasuk();

        if ($tanggal = $request->query('tanggal')) {
            $dataMasuk = array_values(array_filter($dataMasuk, fn ($d) => $d['tanggal'] === $tanggal));
        }
        if ($bulan = $request->query('bulan')) {
            $dataMasuk = array_values(array_filter($dataMasuk, fn ($d) => (int) date('n', strtotime($d['tanggal'])) === (int) $bulan));
        }
        if ($tahun = $request->query('tahun')) {
            $dataMasuk = array_values(array_filter($dataMasuk, fn ($d) => (int) date('Y', strtotime($d['tanggal'])) === (int) $tahun));
        }
        if ($status = $request->query('status')) {
            $dataMasuk = array_values(array_filter($dataMasuk, fn ($d) => $d['status'] === $status));
        }

        // Urutkan: data terlama di atas (sesuai kebutuhan layar dashboard kantor nanti).
        usort($dataMasuk, fn ($a, $b) => strtotime($a['tanggal']) <=> strtotime($b['tanggal']));

        return view('dashboard.admin.data-masuk', [
            'adminNama' => $request->session()->get('admin_nama', 'Admin'),
            'adminNip'  => $request->session()->get('admin_nip', '-'),

            'dataMasuk'    => $dataMasuk,
            'daftarTahun'  => [date('Y'), date('Y') - 1],
        ]);
    }

    /** ---------------- Data dummy (sementara) ---------------- */

    private function mockVerifikator(): array
    {
        return [
            ['id' => 'v1', 'nama' => 'Andi Rahman, S.Pd.', 'kecamatan' => 'Mariso'],
            ['id' => 'v2', 'nama' => 'Siti Nurhaliza, S.E.', 'kecamatan' => 'Mamajang'],
            ['id' => 'v3', 'nama' => 'Muh. Yusuf, S.Kom.', 'kecamatan' => 'Tamalate'],
            ['id' => 'v4', 'nama' => 'Rina Wulandari, S.Pd.', 'kecamatan' => 'Rappocini'],
        ];
    }

    private function mockSekolah(): array
    {
        return [
            ['npsn' => '40311001', 'nama' => 'SDN 1 Makassar', 'jenjang' => 'SD', 'status' => 'Negeri', 'kecamatan' => 'Mariso', 'verifikator' => 'Andi Rahman, S.Pd.'],
            ['npsn' => '40311002', 'nama' => 'SMPN 2 Makassar', 'jenjang' => 'SMP', 'status' => 'Negeri', 'kecamatan' => 'Mamajang', 'verifikator' => 'Siti Nurhaliza, S.E.'],
            ['npsn' => '40311003', 'nama' => 'SD Islam Terpadu Cendekia', 'jenjang' => 'SD', 'status' => 'Swasta', 'kecamatan' => 'Tamalate', 'verifikator' => null],
            ['npsn' => '40311004', 'nama' => 'SMAN 4 Makassar', 'jenjang' => 'SMA', 'status' => 'Negeri', 'kecamatan' => 'Rappocini', 'verifikator' => 'Rina Wulandari, S.Pd.'],
            ['npsn' => '40311005', 'nama' => 'SDN 5 Mariso', 'jenjang' => 'SD', 'status' => 'Negeri', 'kecamatan' => 'Mariso', 'verifikator' => null],
        ];
    }

    private function mockDataMasuk(): array
    {
        return [
            ['sekolah' => 'SDN 1 Makassar', 'layanan' => 'KGB', 'verifikator' => 'Andi Rahman, S.Pd.', 'tanggal' => now()->subDays(5)->toDateString(), 'status' => 'processed'],
            ['sekolah' => 'SMPN 2 Makassar', 'layanan' => 'KGB', 'verifikator' => 'Siti Nurhaliza, S.E.', 'tanggal' => now()->subDays(3)->toDateString(), 'status' => 'pending'],
            ['sekolah' => 'SD Islam Terpadu Cendekia', 'layanan' => 'KGB', 'verifikator' => null, 'tanggal' => now()->subDays(2)->toDateString(), 'status' => 'pending'],
            ['sekolah' => 'SMAN 4 Makassar', 'layanan' => 'KGB', 'verifikator' => 'Rina Wulandari, S.Pd.', 'tanggal' => now()->subDay()->toDateString(), 'status' => 'rejected'],
            ['sekolah' => 'SDN 5 Mariso', 'layanan' => 'KGB', 'verifikator' => null, 'tanggal' => now()->toDateString(), 'status' => 'pending'],
        ];
    }
}
