<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DATA PERCOBAAN — hanya untuk mencoba halaman Penugasan Sekolah dengan jumlah
 * yang realistis: 314 SD + 54 SMP di 15 kecamatan Kota Makassar.
 *
 * Nama sekolah & NPSN di sini FIKTIF (semua nama diakhiri "(DEMO)", NPSN
 * diawali 99). Jangan dipakai untuk data asli.
 *
 * Jalankan   : php artisan db:seed --class=SekolahDemoSeeder
 * Hapus lagi : php artisan tinker --execute="App\Models\Sekolah::where('nama_sekolah','like','%(DEMO)')->delete();"
 * Password semua sekolah demo: sekolah123
 */
class SekolahDemoSeeder extends Seeder
{
    /** kecamatan => [jumlah SD, jumlah SMP]  (total 314 SD dan 54 SMP) */
    private const SEBARAN = [
        'Mariso'       => [14, 2],
        'Mamajang'     => [20, 3],
        'Tamalate'     => [33, 5],
        'Rappocini'    => [31, 5],
        'Makassar'     => [17, 3],
        'Ujung Pandang' => [7, 2],
        'Wajo'         => [13, 2],
        'Bontoala'     => [13, 3],
        'Ujung Tanah'  => [15, 3],
        'Tallo'        => [24, 4],
        'Panakkukang'  => [27, 5],
        'Manggala'     => [28, 5],
        'Biringkanaya' => [34, 6],
        'Tamalanrea'   => [17, 4],
        'Sangkarrang'  => [21, 2],
    ];

    public function run(): void
    {
        $password = Hash::make('sekolah123'); // di-hash sekali saja supaya cepat

        $urut = 0;
        foreach (self::SEBARAN as $kecamatan => [$jumlahSd, $jumlahSmp]) {
            for ($i = 1; $i <= $jumlahSd; $i++) {
                $this->buat('SD', '10', ++$urut, $i, $kecamatan, $password);
            }
            for ($i = 1; $i <= $jumlahSmp; $i++) {
                $this->buat('SMP', '20', ++$urut, $i, $kecamatan, $password);
            }
        }
    }

    private function buat(string $jenjang, string $kode, int $urut, int $nomor, string $kecamatan, string $password): void
    {
        $negeri = $nomor % 5 !== 0; // kira-kira 80% negeri, sisanya swasta
        $awalan = $negeri ? "{$jenjang} Negeri" : "{$jenjang} Swasta";

        Sekolah::updateOrCreate(
            ['npsn' => '99' . $kode . str_pad((string) $urut, 4, '0', STR_PAD_LEFT)],
            [
                'nama_sekolah' => "{$awalan} {$nomor} {$kecamatan} (DEMO)",
                'jenjang'      => $jenjang,
                'status'       => $negeri ? 'Negeri' : 'Swasta',
                'kelurahan'    => 'Kelurahan Demo',
                'kecamatan'    => $kecamatan,
                'password'     => $password,
            ]
        );
    }
}
