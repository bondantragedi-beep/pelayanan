<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiKgb extends Model
{
    protected $table = 'verifikasi_kgb';

    protected $fillable = [
        'pengajuan_id', 'jenis_pegawai',
        'nip_pegawai', 'nama_pegawai', 'pangkat_jabatan', 'unit_kerja',
        'sk_pejabat', 'tanggal_sk', 'nomor_sk', 'sk_berlaku_gaji',
        'golongan', 'masa_kerja_tahun', 'masa_kerja_bulan',
        'gaji_pokok_lama', 'gaji_pokok_baru', 'tmt_baru',
        'keterangan',
        'masa_perjanjian_tahun', 'perjanjian_mulai', 'perjanjian_selesai',
        'dibuat_oleh_nip',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk'         => 'date',
            'sk_berlaku_gaji'    => 'date',
            'tmt_baru'           => 'date',
            'perjanjian_mulai'   => 'date',
            'perjanjian_selesai' => 'date',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function isPppk(): bool
    {
        return $this->jenis_pegawai === 'PPPK';
    }

    /** Nama view PDF yang dipakai: surat PNS atau surat PPPK. */
    public function viewPdf(): string
    {
        return $this->isPppk() ? 'pdf.kgb-pppk' : 'pdf.kgb-pns';
    }
}
