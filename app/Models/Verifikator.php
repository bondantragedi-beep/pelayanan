<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Verifikator extends Authenticatable
{
    protected $table = 'verifikators';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama',
        'kecamatan',
        'is_admin',
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
            'is_admin' => 'boolean',
        ];
    }
}
