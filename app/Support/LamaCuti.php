<?php

namespace App\Support;

use DateTimeImmutable;

/**
 * Menghitung "Selama ... (hari/bulan/tahun)" pada formulir cuti dan memeriksa
 * batas maksimum tiap jenis cuti (Peraturan BKN No. 24 Tahun 2017).
 *
 * Dipakai di dua tempat supaya hasilnya selalu sama: sisi sekolah (validasi
 * saat mengajukan) dan sisi verifikator (isi formulir & PDF).
 */
class LamaCuti
{
    public const JENIS = [
        'tahunan'        => 'Cuti Tahunan',
        'besar'          => 'Cuti Besar',
        'sakit'          => 'Cuti Sakit',
        'melahirkan'     => 'Cuti Melahirkan',
        'alasan_penting' => 'Cuti Karena Alasan Penting',
        'luar_tanggungan' => 'Cuti di Luar Tanggungan Negara',
    ];

    /** Jenis yang boleh dipilih sekolah. Cuti di luar tanggungan negara butuh persetujuan BKN, diproses terpisah. */
    public const JENIS_SEKOLAH = ['tahunan', 'besar', 'sakit', 'melahirkan', 'alasan_penting'];

    /**
     * Nama berkas tambahan yang wajib dilampirkan menurut jenis cuti, atau null bila tidak ada.
     * Berkas utama (formulir, SK pangkat, rekap absen) wajib untuk semua jenis.
     */
    public static function berkasTambahan(string $jenis, bool $ibadah = false): ?string
    {
        if ($jenis === 'tahunan' && $ibadah) {
            return 'Surat Bukti Umroh/Haji (kwitansi travel atau jadwal keberangkatan)';
        }

        return match ($jenis) {
            'melahirkan'     => 'Surat Keterangan dari RS Bersalin',
            'sakit'          => 'Surat Keterangan Dokter',
            'alasan_penting' => 'Surat Keterangan Pendukung (rawat inap / RT / akta nikah, sesuai alasan)',
            default          => null,
        };
    }

    /** Hari kerja (Senin–Jumat) dikurangi hari libur pada config('cuti.hari_libur'), inklusif. */
    public static function hariKerja($mulai, $selesai): int
    {
        $a = self::tgl($mulai);
        $b = self::tgl($selesai);
        if (! $a || ! $b || $b < $a) {
            return 0;
        }

        $libur = config('cuti.hari_libur', []);
        $total = 0;
        for ($d = $a; $d <= $b; $d = $d->modify('+1 day')) {
            if ((int) $d->format('N') >= 6) {
                continue;
            }
            if (isset($libur[$d->format('Y-m-d')])) {
                continue;
            }
            $total++;
        }

        return $total;
    }

    /** Jumlah hari kalender, inklusif. */
    public static function hariKalender($mulai, $selesai): int
    {
        $a = self::tgl($mulai);
        $b = self::tgl($selesai);
        if (! $a || ! $b || $b < $a) {
            return 0;
        }

        return (int) $a->diff($b)->days + 1;
    }

    /** Tanggal selesai untuk cuti sepanjang N bulan: 1 Maret + 3 bulan -> 31 Mei. */
    public static function selesaiSetelahBulan($mulai, int $bulan): ?string
    {
        $a = self::tgl($mulai);
        if (! $a) {
            return null;
        }

        return self::tambahBulan($a, $bulan)->modify('-1 day')->format('Y-m-d');
    }

    /**
     * @return array{angka:int, satuan:string, hari_kerja:int, hari_kalender:int}
     *   Tahunan selalu dihitung dalam hari kerja. Jenis lain: bila tepat N bulan
     *   penuh -> "N bulan", selain itu hari kalender.
     */
    public static function hitung(string $jenis, $mulai, $selesai): array
    {
        $kerja    = self::hariKerja($mulai, $selesai);
        $kalender = self::hariKalender($mulai, $selesai);

        if ($jenis === 'tahunan') {
            return ['angka' => $kerja, 'satuan' => 'hari', 'hari_kerja' => $kerja, 'hari_kalender' => $kalender];
        }

        $bulan = self::bulanPenuh($mulai, $selesai);
        if ($bulan !== null) {
            return ['angka' => $bulan, 'satuan' => 'bulan', 'hari_kerja' => $kerja, 'hari_kalender' => $kalender];
        }

        return ['angka' => $kalender, 'satuan' => 'hari', 'hari_kerja' => $kerja, 'hari_kalender' => $kalender];
    }

    /**
     * Pesan kesalahan bila lama cuti melanggar batas, atau null bila aman.
     * $ibadah = true: batas cuti tahunan tidak berlaku (umroh/haji).
     */
    public static function periksaBatas(string $jenis, $mulai, $selesai, bool $ibadah = false): ?string
    {
        $a = self::tgl($mulai);
        $b = self::tgl($selesai);
        if (! $a || ! $b) {
            return 'Tanggal mulai dan tanggal selesai wajib diisi.';
        }
        if ($b < $a) {
            return 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        }

        if ($jenis === 'tahunan') {
            $kerja = self::hariKerja($a, $b);
            if ($kerja < 1) {
                return 'Rentang tanggal ini tidak mengandung hari kerja (hanya Sabtu/Minggu/hari libur).';
            }
            $maks = (int) config('cuti.maks_tahunan', 12);
            if (! $ibadah && $kerja > $maks) {
                return "Cuti tahunan paling lama {$maks} hari kerja (rentang ini {$kerja} hari kerja). "
                     . 'Centang "cuti karena ibadah" bila untuk umroh/haji.';
            }

            return null;
        }

        $maksBulan = config("cuti.maks_bulan.{$jenis}");
        if ($maksBulan) {
            $batas = self::tambahBulan($a, (int) $maksBulan)->modify('-1 day');
            if ($b > $batas) {
                $label = strtolower(self::JENIS[$jenis] ?? $jenis);
                return "{$label} paling lama {$maksBulan} bulan (sampai " . FormatSurat::tanggal($batas) . ').';
            }
        }

        return null;
    }

    /** Teks satuan untuk dicetak dengan satuan yang tidak dipakai dicoret. */
    public static function satuanDicoret(string $dipakai): array
    {
        return array_map(
            fn ($s) => ['teks' => $s, 'coret' => $s !== $dipakai],
            ['hari', 'bulan', 'tahun']
        );
    }

    /** Jumlah bulan bila rentang tepat bulat (1 Mar – 31 Mei = 3), selain itu null. */
    private static function bulanPenuh($mulai, $selesai): ?int
    {
        $a = self::tgl($mulai);
        $b = self::tgl($selesai);
        if (! $a || ! $b || $b < $a) {
            return null;
        }

        $selisih = ((int) $b->format('Y') - (int) $a->format('Y')) * 12 + ((int) $b->format('n') - (int) $a->format('n')) + 1;
        for ($n = max(1, $selisih - 1); $n <= $selisih + 1; $n++) {
            if (self::tambahBulan($a, $n)->modify('-1 day')->format('Y-m-d') === $b->format('Y-m-d')) {
                return $n;
            }
        }

        return null;
    }

    /** Tambah bulan tanpa "melompat" (31 Jan + 1 bln = 28/29 Feb, bukan 3 Mar). */
    private static function tambahBulan(DateTimeImmutable $d, int $bulan): DateTimeImmutable
    {
        $hari   = (int) $d->format('j');
        $awal   = $d->modify('first day of this month')->modify("+{$bulan} months");
        $maksHr = (int) $awal->format('t');

        return $awal->setDate((int) $awal->format('Y'), (int) $awal->format('n'), min($hari, $maksHr));
    }

    private static function tgl($t): ?DateTimeImmutable
    {
        if (empty($t)) {
            return null;
        }
        if ($t instanceof \DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($t)->setTime(0, 0);
        }

        try {
            return (new DateTimeImmutable((string) $t))->setTime(0, 0);
        } catch (\Throwable) {
            return null;
        }
    }
}
