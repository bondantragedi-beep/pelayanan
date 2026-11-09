<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = ['judul', 'gambar', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function getGambarUrlAttribute(): string
    {
        // Relatif terhadap host yang sedang diakses, supaya tetap tampil walau APP_URL di .env keliru.
        return asset('storage/' . ltrim($this->gambar, '/'));
    }

    public static function yangAktif(): ?self
    {
        return static::where('aktif', true)->latest('updated_at')->first();
    }
}
