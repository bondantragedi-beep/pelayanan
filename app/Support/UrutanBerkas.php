<?php

namespace App\Support;

use App\Models\Pengajuan;
use Illuminate\Support\Collection;

/**
 * Aturan urutan berkas untuk layar monitoring (Layar Kantor & Data Masuk admin).
 *
 *  Tingkat 0  TERTUNDA        belum selesai & sudah >= batas hari  -> paling atas, terlama dulu
 *  Tingkat 1  DALAM PROSES    belum selesai & masih < batas hari   -> terlama dulu
 *  Tingkat 2  DITOLAK         sudah ditangani dinas, menunggu perbaikan sekolah
 *  Tingkat 3  TERKIRIM        surat sudah terkirim ke sekolah       -> paling bawah
 *
 * "Belum selesai" = tahap pending atau menunggu_ttd (lihat RingkasanBerkas).
 * Tingkat 2 dan 3 diurutkan dari yang terbaru ditangani, supaya yang baru
 * selesai tetap terlihat di bagian atas kelompoknya.
 */
class UrutanBerkas
{
    public const TERTUNDA = 0;
    public const PROSES   = 1;
    public const DITOLAK  = 2;
    public const TERKIRIM = 3;

    public static function batasHari(): int
    {
        return max(1, (int) config('berkas.batas_tertunda_hari', 7));
    }

    /** Umur berkas dalam hari kalender sejak tanggal masuk (0 = hari ini). */
    public static function umurHari(Pengajuan $p, $sekarang = null): int
    {
        $sekarang = $sekarang ? \Illuminate\Support\Carbon::parse($sekarang) : now();

        return max(0, (int) $p->created_at->copy()->startOfDay()->diffInDays($sekarang->copy()->startOfDay()));
    }

    public static function tingkat(Pengajuan $p, $sekarang = null): int
    {
        return match (RingkasanBerkas::tahap($p)) {
            'terkirim' => self::TERKIRIM,
            'rejected' => self::DITOLAK,
            default    => self::umurHari($p, $sekarang) >= self::batasHari() ? self::TERTUNDA : self::PROSES,
        };
    }

    public static function tertunda(Pengajuan $p, $sekarang = null): bool
    {
        return self::tingkat($p, $sekarang) === self::TERTUNDA;
    }

    /** @param Collection<int,Pengajuan> $daftar */
    public static function urutkan(Collection $daftar, $sekarang = null): Collection
    {
        return $daftar->sort(function (Pengajuan $a, Pengajuan $b) use ($sekarang) {
            $ta = self::tingkat($a, $sekarang);
            $tb = self::tingkat($b, $sekarang);
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }

            // Belum selesai: yang paling lama menunggu di atas. Sudah ditangani: yang terbaru di atas.
            return $ta <= self::PROSES
                ? [$a->created_at, $a->id] <=> [$b->created_at, $b->id]
                : [$b->updated_at, $b->id] <=> [$a->updated_at, $a->id];
        })->values();
    }
}
