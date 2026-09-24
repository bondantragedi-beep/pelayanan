<?php

namespace Database\Seeders;

use App\Models\Verifikator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VerifikatorSeeder extends Seeder
{
    /**
     * Akun contoh untuk uji coba login Verifikator & Admin.
     *
     * Admin   -> NIP: 198501012010011001   Password: admin123
     * Verif.  -> NIP: 198706152011012002   Password: verif123
     *            (dashboard verifikator biasa belum tersedia, jadi
     *             akun ini akan mendapat pesan "modul belum tersedia"
     *             saat mencoba login, sampai modul itu dibuat)
     */
    public function run(): void
    {
        Verifikator::updateOrCreate(
            ['nip' => '198501012010011001'],
            [
                'nama'      => 'Admin Dinas Pendidikan',
                'kecamatan' => null,
                'is_admin'  => true,
                'password'  => Hash::make('admin123'),
            ]
        );

        Verifikator::updateOrCreate(
            ['nip' => '198706152011012002'],
            [
                'nama'      => 'Andi Rahman, S.Pd.',
                'kecamatan' => 'Mariso',
                'is_admin'  => false,
                'password'  => Hash::make('verif123'),
            ]
        );
    }
}
