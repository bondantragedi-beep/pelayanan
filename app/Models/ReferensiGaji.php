<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferensiGaji extends Model
{
    protected $table = 'referensi_gaji';

    protected $fillable = ['golongan', 'masa_kerja_tahun', 'gaji_pokok'];
}
