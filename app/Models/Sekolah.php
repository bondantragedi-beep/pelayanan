<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Sekolah extends Authenticatable
{
    protected $table = 'sekolah';

    protected $primaryKey = 'npsn';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'jenjang',
        'status',
        'kelurahan',
        'kecamatan',
        'verifikator_nip',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Verifikator::class, 'verifikator_nip', 'nip');
    }

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'npsn', 'npsn');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'npsn', 'npsn');
    }
}
