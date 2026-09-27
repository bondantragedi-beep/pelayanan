<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Contoh pegawai di sekolah NPSN 40311001 (kecamatan Mariso), supaya
     * bisa dites pencarian NIP oleh akun verifikator contoh (yang juga
     * bertanggung jawab atas kecamatan Mariso).
     */
    public function run(): void
    {
        $daftar = [
            ['nip' => '198002012005011001', 'nama' => 'Muhammad Ikbal, S.Pd.', 'status' => 'PNS', 'pangkat_jabatan' => 'Guru Kelas', 'golongan' => 'III/b', 'tmt' => '2005-01-01'],
            ['nip' => '198512202009022002', 'nama' => 'Nur Fadillah, S.Pd.', 'status' => 'PNS', 'pangkat_jabatan' => 'Guru Mata Pelajaran', 'golongan' => 'III/c', 'tmt' => '2009-02-20'],
            ['nip' => '199003152015031003', 'nama' => 'Hasanuddin, S.Kom.', 'status' => 'PPPK', 'pangkat_jabatan' => 'Guru TIK', 'golongan' => 'IX', 'tmt' => '2015-03-15'],
        ];

        foreach ($daftar as $p) {
            Pegawai::updateOrCreate(
                ['nip' => $p['nip']],
                [
                    'nama'            => $p['nama'],
                    'status'          => $p['status'],
                    'npsn'            => '40311001',
                    'pangkat_jabatan' => $p['pangkat_jabatan'],
                    'golongan'        => $p['golongan'],
                    'kecamatan'       => 'Mariso',
                    'tmt'             => $p['tmt'],
                ]
            );
        }
    }
}
