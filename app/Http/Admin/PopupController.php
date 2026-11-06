<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::latest()->get();
        return view('admin.popup.index', compact('popups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'  => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        Popup::create([
            'judul'  => $data['judul'],
            'gambar' => $request->file('gambar')->store('popups', 'public'),
            'aktif'  => false,
        ]);

        return back()->with('success', 'Gambar berhasil diupload. Klik "Aktifkan" untuk menampilkannya.');
    }

    public function update(Request $request, Popup $popup)
    {
        $data = $request->validate([
            'judul'  => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($popup->gambar);
            $popup->gambar = $request->file('gambar')->store('popups', 'public');
        }

        $popup->judul = $data['judul'];
        $popup->save(); // updated_at berubah, popup muncul lagi ke pengguna

        return back()->with('success', 'Popup berhasil diperbarui.');
    }

    public function toggle(Popup $popup)
    {
        if ($popup->aktif) {
            $popup->update(['aktif' => false]);
            $pesan = 'Popup dimatikan.';
        } else {
            Popup::where('aktif', true)->update(['aktif' => false]);
            $popup->update(['aktif' => true]);
            $pesan = 'Popup diaktifkan.';
        }

        return back()->with('success', $pesan);
    }

    public function destroy(Popup $popup)
    {
        Storage::disk('public')->delete($popup->gambar);
        $popup->delete();

        return back()->with('success', 'Popup dihapus.');
    }
}
