<?php

namespace Database\Seeders;

use App\Models\Pengajuan;
use Illuminate\Database\Seeder;

class PengajuanSeeder extends Seeder
{
    /**
     * Contoh data untuk sekolah NPSN 40311001 (akun contoh dari SekolahSeeder),
     * supaya halaman "Daftar Berkas Saya" tidak kosong saat dites.
     *
     * Catatan: contoh "processed" sengaja TIDAK disertakan di sini karena
     * hasil_path (PDF hasil tanda tangan pimpinan) baru bisa terisi lewat
     * modul Verifikator (belum dibuat) — kalau dipaksakan di sini, link
     * unduh di Inbox akan mengarah ke file yang tidak ada.
     */
    public function run(): void
    {
        Pengajuan::updateOrCreate(
            ['npsn' => '40311001', 'jenis_layanan' => 'kgb', 'status' => 'pending'],
            [
                'berkas' => [
                    'sk_pangkat_terakhir' => 'kgb/40311001/sk-pangkat-terakhir/contoh.pdf',
                    'sk_kgb_terakhir'     => 'kgb/40311001/sk-kgb-terakhir/contoh.pdf',
                ],
            ]
        );

        Pengajuan::updateOrCreate(
            ['npsn' => '40311001', 'jenis_layanan' => 'kgb', 'status' => 'rejected'],
            [
                'berkas' => [
                    'sk_pangkat_terakhir' => 'kgb/40311001/sk-pangkat-terakhir/contoh2.pdf',
                    'sk_kgb_terakhir'     => 'kgb/40311001/sk-kgb-terakhir/contoh2.pdf',
                ],
                'alasan_ditolak' => 'Scan SK KGB Terakhir buram, mohon unggah ulang dengan hasil pindai yang lebih jelas.',
                'diproses_pada'  => now()->subDay(),
            ]
        );
    }
}
