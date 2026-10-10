<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Contoh pegawai di sekolah NPSN 40311001 (SDN 1 Makassar), memakai
     * kolom yang sama dengan Excel data pegawai. Data fiktif, hanya untuk uji coba.
     */
    public function run(): void
    {
        $daftar = [
            [
                'nip' => '198002012005011001', 'nama' => 'Muhammad Ikbal, S.Pd.',
                'tempat_tanggal_lahir' => 'Makassar, 01 Februari 1980',
                'golongan_pangkat' => 'Penata Muda Tk. I (III/b)', 'tmt_golongan' => '2021-04-01',
                'nama_jabatan' => 'Guru Ahli Pertama', 'tmt_jabatan' => '2019-01-01',
                'status_pegawai' => 'PNS', 'tmt_pegawai' => '2005-01-01',
                'masa_kerja_tahun' => 18, 'masa_kerja_bulan' => 2,
                'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'status_perkawinan' => 'Kawin',
                'pendidikan_awal' => 'SMA', 'pendidikan_akhir' => 'S1',
            ],
            [
                'nip' => '198512202009022002', 'nama' => 'Nur Fadillah, S.Pd.',
                'tempat_tanggal_lahir' => 'Gowa, 20 Desember 1985',
                'golongan_pangkat' => 'Penata (III/c)', 'tmt_golongan' => '2022-10-01',
                'nama_jabatan' => 'Guru Ahli Muda', 'tmt_jabatan' => '2021-10-01',
                'status_pegawai' => 'PNS', 'tmt_pegawai' => '2009-02-20',
                'masa_kerja_tahun' => 14, 'masa_kerja_bulan' => 6,
                'jenis_kelamin' => 'Perempuan', 'agama' => 'Islam', 'status_perkawinan' => 'Kawin',
                'pendidikan_awal' => 'SMA', 'pendidikan_akhir' => 'S1',
            ],
            [
                'nip' => '199003152015031003', 'nama' => 'Hasanuddin, S.Kom.',
                'tempat_tanggal_lahir' => 'Maros, 15 Maret 1990',
                'golongan_pangkat' => 'IX', 'tmt_golongan' => '2021-03-01',
                'nama_jabatan' => 'Guru TIK', 'tmt_jabatan' => '2021-03-01',
                'status_pegawai' => 'PPPK', 'tmt_pegawai' => '2021-03-01',
                'masa_kerja_tahun' => 5, 'masa_kerja_bulan' => 0,
                'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'status_perkawinan' => 'Belum Kawin',
                'pendidikan_awal' => 'SMA', 'pendidikan_akhir' => 'S1',
            ],
        ];

        foreach ($daftar as $p) {
            Pegawai::updateOrCreate(
                ['nip' => $p['nip']],
                $p + [
                    'unit_kerja' => 'SDN 1 Makassar',
                    'npsn'       => '40311001',
                ]
            );
        }
    }
}
