<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SekolahSeeder extends Seeder
{
    /**
     * Akun contoh untuk uji coba login Operator Sekolah.
     * NPSN: 40311001   Password: sekolah123
     */
    public function run(): void
    {
        Sekolah::updateOrCreate(
            ['npsn' => '40311001'],
            [
                'nama_sekolah' => 'SDN 1 Makassar',
                'jenjang'      => 'SD',
                'status'       => 'Negeri',
                'kelurahan'    => 'Mario',
                'kecamatan'    => 'Mariso',
                'password'     => Hash::make('sekolah123'),
            ]
        );
    }
}
