<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login (form dengan tab Sekolah / Verifikator).
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Proses login. Menangani dua jenis akun sekaligus:
     *  - login_type = sekolah      -> guard 'sekolah', pakai npsn + password
     *  - login_type = verifikator  -> guard 'verifikator', pakai nip + password
     *
     * Admin TIDAK punya guard/tabel terpisah: admin login lewat guard
     * 'verifikator' yang sama, lalu diarahkan ke dashboard admin jika
     * kolom is_admin bernilai true.
     */
    public function store(Request $request)
    {
        $request->validate([
            'login_type' => ['required', 'in:sekolah,verifikator'],
            'password'   => ['required', 'string'],
            'npsn'       => ['required_if:login_type,sekolah', 'string'],
            'nip'        => ['required_if:login_type,verifikator', 'digits:18'],
        ]);

        if ($request->login_type === 'sekolah') {
            return $this->attemptSekolah($request);
        }

        return $this->attemptVerifikator($request);
    }

    private function attemptSekolah(Request $request)
    {
        $credentials = [
            'npsn'     => $request->input('npsn'),
            'password' => $request->input('password'),
        ];

        if (! Auth::guard('sekolah')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'npsn' => 'NPSN atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('sekolah.dashboard'));
    }

    private function attemptVerifikator(Request $request)
    {
        $credentials = [
            'nip'      => $request->input('nip'),
            'password' => $request->input('password'),
        ];

        if (! Auth::guard('verifikator')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'nip' => 'NIP atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        $verifikator = Auth::guard('verifikator')->user();

        // Admin login lewat akun verifikator yang sama, dibedakan lewat is_admin.
        if ($verifikator->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('verifikator.dashboard'));
    }

    /**
     * Logout dari guard manapun yang sedang aktif (sekolah atau verifikator).
     */
    public function destroy(Request $request)
    {
        if (Auth::guard('sekolah')->check()) {
            Auth::guard('sekolah')->logout();
        }
        if (Auth::guard('verifikator')->check()) {
            Auth::guard('verifikator')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
