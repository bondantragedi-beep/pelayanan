<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Popup extends Model
{
    protected $fillable = ['judul', 'gambar', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function getGambarUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->gambar);
    }

    public static function yangAktif(): ?self
    {
        return static::where('aktif', true)->latest('updated_at')->first();
    }
}
