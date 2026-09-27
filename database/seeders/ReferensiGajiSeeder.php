<?php

namespace Database\Seeders;

use App\Models\ReferensiGaji;
use Illuminate\Database\Seeder;

class ReferensiGajiSeeder extends Seeder
{
    /**
     * CONTOH KASAR — bukan angka resmi. Ganti dengan tabel gaji pokok PNS/PPPK
     * yang berlaku (per golongan & masa kerja golongan, biasanya kelipatan 2
     * tahun) begitu datanya tersedia.
     */
    public function run(): void
    {
        $golonganList = ['III/a', 'III/b', 'III/c', 'III/d'];
        $gajiAwal = [
            'III/a' => 2900000,
            'III/b' => 3000000,
            'III/c' => 3100000,
            'III/d' => 3200000,
        ];

        foreach ($golonganList as $golongan) {
            $gaji = $gajiAwal[$golongan];

            // Masa kerja 0, 2, 4, 6, ... 20 tahun — tiap naik 2 tahun, gaji naik contoh 3%.
            for ($masaKerja = 0; $masaKerja <= 20; $masaKerja += 2) {
                ReferensiGaji::updateOrCreate(
                    ['golongan' => $golongan, 'masa_kerja_tahun' => $masaKerja],
                    ['gaji_pokok' => (int) round($gaji)]
                );
                $gaji *= 1.03;
            }
        }
    }
}
