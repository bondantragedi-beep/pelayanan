<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'npsn',
        'jenis_layanan',
        'berkas',
        'status',
        'alasan_ditolak',
        'hasil_path',
        'diproses_oleh_nip',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'berkas'         => 'array',
            'diproses_pada'  => 'datetime',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }

    /** Label ramah-baca untuk jenis layanan, dipakai di tampilan. */
    public function labelLayanan(): string
    {
        return match ($this->jenis_layanan) {
            'kgb' => 'Kenaikan Gaji Berkala (KGB)',
            default => ucfirst($this->jenis_layanan),
        };
    }
}
