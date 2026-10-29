<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function edit()
    {
        $sekolah = Auth::guard('sekolah')->user();

        return view('dashboard.sekolah.password', [
            'sekolahNama' => $sekolah->nama_sekolah,
            'sekolahNpsn' => $sekolah->npsn,
            'wajib'       => $sekolah->wajib_ganti_password,
        ]);
    }

    public function update(Request $request)
    {
        $sekolah = Auth::guard('sekolah')->user();

        $request->validate([
            'password_lama' => ['required', 'current_password:sekolah'],
            'password'      => [
                'required',
                'confirmed',
                'different:password_lama',
                'not_in:' . implode(',', ['123456', '12345678', '123456789', 'password', 'sekolah123', $sekolah->npsn]),
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'password_lama.required'                => 'Password lama wajib diisi.',
            'password_lama.current_password'        => 'Password lama salah.',
            'password.required'                     => 'Password baru wajib diisi.',
            'password.confirmed'                    => 'Konfirmasi password baru tidak sama.',
            'password.different'                    => 'Password baru tidak boleh sama dengan password lama.',
            'password.not_in'                       => 'Password baru terlalu mudah ditebak. Jangan pakai 123456, NPSN, atau password umum.',
            'password.min'                          => 'Password baru minimal 8 karakter.',
            'password.letters'                      => 'Password baru harus mengandung huruf.',
            'password.numbers'                      => 'Password baru harus mengandung angka.',
        ]);

        // Cast 'hashed' pada model Sekolah otomatis meng-hash password.
        $sekolah->password = $request->input('password');
        $sekolah->wajib_ganti_password = false;
        $sekolah->password_diubah_pada = now();
        $sekolah->save();

        return redirect()
            ->route('sekolah.dashboard')
            ->with('success', 'Password berhasil diubah. Gunakan password baru saat login berikutnya.');
    }
}
