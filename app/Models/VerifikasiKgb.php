<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiKgb extends Model
{
    protected $table = 'verifikasi_kgb';

    protected $fillable = [
        'pengajuan_id', 'nip_pegawai', 'nama_pegawai', 'pangkat_jabatan',
        'jenis_sk', 'nomor_sk', 'tanggal_sk',
        'masa_kerja_tahun', 'gaji_pokok_lama',
        'golongan', 'tmt_baru', 'dibuat_oleh_nip',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tmt_baru'   => 'date',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}
