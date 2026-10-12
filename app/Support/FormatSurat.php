<?php

namespace App\Support;

/**
 * Pembantu pemformatan untuk surat KGB (PDF).
 *
 * Sengaja ditulis dengan PHP biasa (tanpa bergantung pada pengaturan
 * bahasa/locale Laravel atau Carbon), supaya nama bulan selalu keluar dalam
 * Bahasa Indonesia di mana pun aplikasinya dijalankan.
 */
class FormatSurat
{
    private const BULAN = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    /**
     * 2026-09-01 -> "1 September 2026"
     * Dengan $duaDigitHari = true -> "01 September 2026" (dipakai pada kolom
     * "Perpanjangan Perjanjian Kerja" di surat PPPK, sesuai contoh aslinya).
     */
    public static function tanggal($tanggal, bool $duaDigitHari = false): string
    {
        $d = self::keTanggal($tanggal);
        if (! $d) {
            return '-';
        }

        $hari = (int) $d->format('j');
        $hari = $duaDigitHari ? str_pad((string) $hari, 2, '0', STR_PAD_LEFT) : (string) $hari;

        return $hari . ' ' . self::BULAN[(int) $d->format('n') - 1] . ' ' . $d->format('Y');
    }

    /** Tambah (atau kurangi) tahun tanpa mengubah objek aslinya. */
    public static function tambahTahun($tanggal, int $tahun): ?\DateTimeImmutable
    {
        $d = self::keTanggal($tanggal);
        if (! $d) {
            return null;
        }

        return $d->modify(($tahun >= 0 ? '+' : '') . $tahun . ' years');
    }

    /** 5687200 -> "Rp. 5.687.200,-" */
    public static function rupiah($angka): string
    {
        return 'Rp. ' . number_format((int) $angka, 0, ',', '.') . ',-';
    }

    /** (32) -> "32 Tahun" ; (4, 0, true) -> "4 Tahun 0 Bulan" */
    public static function masaKerja(int $tahun, int $bulan = 0, bool $denganBulan = false): string
    {
        return $denganBulan ? "{$tahun} Tahun {$bulan} Bulan" : "{$tahun} Tahun";
    }

    private static function keTanggal($tanggal): ?\DateTimeImmutable
    {
        if (empty($tanggal)) {
            return null;
        }
        if ($tanggal instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($tanggal);
        }

        return new \DateTimeImmutable((string) $tanggal);
    }
}
