<?php

namespace App\Http\Controllers;

use App\Models\Popup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Menyajikan gambar popup langsung dari aplikasi (storage/app/public/popups),
 * jadi TIDAK bergantung pada `php artisan storage:link` / symlink public/storage
 * yang sering gagal di XAMPP Windows. Pola yang sama dengan unduhan dokumen berkas.
 *
 * Hanya untuk yang sudah login (operator sekolah atau verifikator/admin).
 */
class PopupGambarController extends Controller
{
    public function show(Popup $popup)
    {
        abort_unless(Auth::guard('sekolah')->check() || Auth::guard('verifikator')->check(), 403);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($popup->gambar), 404, 'File gambar popup tidak ditemukan di penyimpanan.');

        // ?v=<updated_at> pada URL membuat cache browser otomatis diganti saat gambar diganti.
        return $disk->response($popup->gambar, null, ['Cache-Control' => 'private, max-age=86400']);
    }
}
