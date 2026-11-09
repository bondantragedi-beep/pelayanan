<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = ['judul', 'gambar', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    /** Disajikan lewat rute aplikasi (bukan /storage/...), supaya tidak perlu `storage:link`. */
    public function getGambarUrlAttribute(): string
    {
        return route('popup.gambar', $this) . '?v=' . $this->updated_at->timestamp;
    }

    public static function yangAktif(): ?self
    {
        return static::where('aktif', true)->latest('updated_at')->first();
    }
}
