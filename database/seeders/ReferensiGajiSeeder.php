<?php

namespace Database\Seeders;

use App\Models\ReferensiGaji;
use App\Support\TabelGaji;
use Illuminate\Database\Seeder;

class ReferensiGajiSeeder extends Seeder
{
    /**
     * Mengisi tabel referensi_gaji dari tabel gaji pokok resmi:
     *  - PNS  : Lampiran PP Nomor 5 Tahun 2024   (berlaku 1 Januari 2024)
     *  - PPPK : Lampiran Perpres Nomor 11 Tahun 2024 (berlaku 1 Januari 2024)
     *
     * Datanya ada di database/data/*.csv. Untuk mengubah angka (misalnya kalau
     * ada peraturan baru), edit file CSV-nya lalu jalankan:
     *     php artisan db:seed --class=ReferensiGajiSeeder
     * Seeder ini aman dijalankan berulang kali (updateOrCreate).
     */
    public function run(): void
    {
        $berkas = [
            database_path('data/gaji_pns_pp5_2024.csv'),
            database_path('data/gaji_pppk_perpres11_2024.csv'),
        ];

        foreach ($berkas as $file) {
            foreach (TabelGaji::baca($file) as $baris) {
                ReferensiGaji::updateOrCreate(
                    [
                        'golongan'         => $baris['golongan'],
                        'masa_kerja_tahun' => $baris['masa_kerja_tahun'],
                    ],
                    ['gaji_pokok' => $baris['gaji_pokok']]
                );
            }
        }
    }
}
