<?php

namespace App\Support;

use App\Models\Pengajuan;

/**
 * Pengelompokan tahap berkas untuk kartu ringkasan di dashboard.
 * Berdiri sendiri (hanya membaca kolom status & hasil_path), jadi dashboard
 * tidak bergantung pada method lain di model Pengajuan.
 */
class RingkasanBerkas
{
    /** pending | menunggu_ttd | terkirim | rejected */
    public static function tahap(Pengajuan $p): string
    {
        return match (true) {
            $p->status === 'rejected'                          => 'rejected',
            $p->status === 'processed' && ! empty($p->hasil_path) => 'terkirim',
            $p->status === 'processed'                         => 'menunggu_ttd',
            default                                            => 'pending',
        };
    }

    /** @return array{pending:int, menunggu_ttd:int, terkirim:int, rejected:int} */
    public static function hitung(iterable $daftar): array
    {
        $hasil = ['pending' => 0, 'menunggu_ttd' => 0, 'terkirim' => 0, 'rejected' => 0];
        foreach ($daftar as $p) {
            $hasil[self::tahap($p)]++;
        }

        return $hasil;
    }
}
