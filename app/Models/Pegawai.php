<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip', 'nama', 'status', 'npsn', 'pangkat_jabatan', 'golongan', 'kecamatan', 'tmt',
    ];

    protected function casts(): array
    {
        return ['tmt' => 'date'];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }
}
