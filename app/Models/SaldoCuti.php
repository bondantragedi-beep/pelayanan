<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pemakaian cuti tahunan per pegawai per tahun. Sisa = hak tahunan - terpakai.
 * Dipakai mengisi "V. CATATAN CUTI" (N-2, N-1, N) pada formulir cuti.
 */
class SaldoCuti extends Model
{
    protected $table = 'saldo_cuti';

    protected $fillable = ['nip', 'tahun', 'terpakai'];

    /** Sisa cuti tahunan pada $tahun. Belum ada catatan = dianggap belum terpakai (sisa penuh). */
    public static function sisa(string $nip, int $tahun): int
    {
        $terpakai = (int) static::where('nip', $nip)->where('tahun', $tahun)->value('terpakai');

        return max(0, (int) config('cuti.hak_tahunan', 12) - $terpakai);
    }

    /** Set pemakaian tahun tertentu dari nilai sisa (dipakai saat verifikator menyimpan catatan). */
    public static function aturSisa(string $nip, int $tahun, int $sisa): void
    {
        $hak = (int) config('cuti.hak_tahunan', 12);

        static::updateOrCreate(
            ['nip' => $nip, 'tahun' => $tahun],
            ['terpakai' => max(0, $hak - max(0, $sisa))]
        );
    }

    /** Tambah pemakaian (dipanggil sekali saat cuti tahunan resmi dikirim ke sekolah). */
    public static function tambahTerpakai(string $nip, int $tahun, int $hari): void
    {
        $baris = static::firstOrCreate(['nip' => $nip, 'tahun' => $tahun], ['terpakai' => 0]);
        $baris->increment('terpakai', max(0, $hari));
    }
}
