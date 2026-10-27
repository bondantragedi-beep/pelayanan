<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiTunjangan extends Model
{
    protected $table = 'verifikasi_tunjangan';

    protected $fillable = [
        'pengajuan_id', 'jenis_pegawai', 'jenis_tunjangan', 'anak_ke',
        'nip_pegawai', 'nama_pegawai', 'pangkat_golongan', 'jabatan', 'unit_kerja',
        'dibuat_oleh_nip',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    /** "Tunjangan Anak Ke-1" / "Tunjangan Istri" / "Tunjangan Suami" / "Tunjangan Istri dan Anak Ke-1" */
    public function labelTunjangan(): string
    {
        $anak = $this->labelAnak();

        return match ($this->jenis_tunjangan) {
            'anak'       => "Tunjangan {$anak}",
            'istri'      => 'Tunjangan Istri',
            'suami'      => 'Tunjangan Suami',
            'istri_anak' => "Tunjangan Istri dan {$anak}",
            'suami_anak' => "Tunjangan Suami dan {$anak}",
            default      => 'Tunjangan',
        };
    }

    /** Nomor anak yang diajukan, terurut. Contoh: [1] atau [1, 2]. */
    public function daftarAnakKe(): array
    {
        $nomor = array_unique(array_filter(array_map('intval', explode(',', (string) $this->anak_ke))));
        sort($nomor);

        return array_values($nomor) ?: [1];
    }

    /** "Anak Ke-1" / "Anak Ke-1 dan Ke-2" / "Anak Ke-1, Ke-2, dan Ke-3" */
    public function labelAnak(): string
    {
        $ke = array_map(fn ($n) => "Ke-{$n}", $this->daftarAnakKe());
        $akhir = array_pop($ke);

        return 'Anak ' . ($ke ? implode(', ', $ke) . ($this->jumlahAnakLebihDari2($ke) ? ', dan ' : ' dan ') : '') . $akhir;
    }

    private function jumlahAnakLebihDari2(array $sisa): bool
    {
        return count($sisa) >= 2;
    }

    /** Kalimat di kolom Uraian surat: "Usul Permohonan Tunjangan Anak Ke-1 PPPK a.n :" */
    public function uraian(): string
    {
        return 'Usul Permohonan ' . $this->labelTunjangan() . ' ' . $this->jenis_pegawai . ' a.n :';
    }
}
