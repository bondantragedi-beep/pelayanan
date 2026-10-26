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

    /** "Tunjangan Anak Ke-1" / "Tunjangan Istri" / "Tunjangan Suami" */
    public function labelTunjangan(): string
    {
        return match ($this->jenis_tunjangan) {
            'anak'  => 'Tunjangan Anak Ke-' . ($this->anak_ke ?: 1),
            'istri' => 'Tunjangan Istri',
            'suami' => 'Tunjangan Suami',
            default => 'Tunjangan',
        };
    }

    /** Kalimat di kolom Uraian surat: "Usul Permohonan Tunjangan Anak Ke-1 PPPK a.n :" */
    public function uraian(): string
    {
        return 'Usul Permohonan ' . $this->labelTunjangan() . ' ' . $this->jenis_pegawai . ' a.n :';
    }
}
