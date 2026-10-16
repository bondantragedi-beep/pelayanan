<?php

namespace App\Support;

/**
 * Membaca file CSV tabel gaji pokok (database/data/*.csv).
 *
 * Format kolom: golongan, masa_kerja_tahun, gaji_pokok
 *  - PNS  : golongan "I/a" ... "IV/e"  (PP No. 5 Tahun 2024)
 *  - PPPK : golongan "I" ... "XVII"    (Perpres No. 11 Tahun 2024)
 *
 * Ditulis dengan PHP biasa (tanpa Laravel) supaya mudah diuji sendiri.
 */
class TabelGaji
{
    /**
     * @return array<int, array{golongan: string, masa_kerja_tahun: int, gaji_pokok: int}>
     */
    public static function baca(string $file): array
    {
        if (! is_file($file)) {
            throw new \RuntimeException("File tabel gaji tidak ditemukan: {$file}");
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        if ($header !== ['golongan', 'masa_kerja_tahun', 'gaji_pokok']) {
            fclose($handle);
            throw new \RuntimeException("Header CSV tidak sesuai di {$file}. Harus: golongan,masa_kerja_tahun,gaji_pokok");
        }

        $hasil = [];
        $sudah = [];
        $baris = 1;

        while (($r = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $baris++;
            if ($r === [null] || count($r) < 3) {
                continue; // baris kosong
            }

            [$golongan, $mkg, $gaji] = [trim($r[0]), trim($r[1]), trim($r[2])];

            if ($golongan === '' || ! ctype_digit($mkg) || ! ctype_digit($gaji) || (int) $gaji <= 0) {
                fclose($handle);
                throw new \RuntimeException("Baris {$baris} di {$file} tidak valid: {$golongan},{$mkg},{$gaji}");
            }

            $kunci = $golongan . '|' . (int) $mkg;
            if (isset($sudah[$kunci])) {
                fclose($handle);
                throw new \RuntimeException("Baris {$baris} di {$file} duplikat: golongan {$golongan}, MKG {$mkg}");
            }
            $sudah[$kunci] = true;

            $hasil[] = [
                'golongan'         => $golongan,
                'masa_kerja_tahun' => (int) $mkg,
                'gaji_pokok'       => (int) $gaji,
            ];
        }

        fclose($handle);

        return $hasil;
    }
}
