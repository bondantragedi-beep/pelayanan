<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'npsn',
        'jenis_layanan',
        'berkas',
        'status',
        'dilihat_pada',
        'dilihat_oleh_nip',
        'alasan_ditolak',
        'hasil_path',
        'diproses_oleh_nip',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'berkas'        => 'array',
            'dilihat_pada'  => 'datetime',
            'diproses_pada' => 'datetime',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }

    public function verifikasiKgb(): HasOne
    {
        return $this->hasOne(VerifikasiKgb::class, 'pengajuan_id');
    }

    /** Label ramah-baca untuk jenis layanan, dipakai di tampilan. */
    public function labelLayanan(): string
    {
        return match ($this->jenis_layanan) {
            'kgb' => 'Kenaikan Gaji Berkala (KGB)',
            default => ucfirst($this->jenis_layanan),
        };
    }

    /**
     * Tahap pengajuan dari sudut pandang Verifikator/Admin — dipisah dari
     * "status" mentah karena satu status 'processed' masih punya 2 kondisi:
     * sudah diisi formnya tapi belum dikirim (menunggu TTD pimpinan),
     * atau sudah benar-benar dikirim ke sekolah (hasil_path terisi).
     */
    public function tahapVerifikator(): string
    {
        if ($this->status === 'rejected') {
            return 'rejected';
        }
        if ($this->status === 'processed' && $this->hasil_path) {
            return 'terkirim';
        }
        if ($this->status === 'processed') {
            return 'menunggu_ttd';
        }

        return 'pending';
    }
}
