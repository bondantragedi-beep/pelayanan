@extends('layouts.guest')

@section('title', 'Login — Sistem Pelayanan Administrasi Kepegawaian | Dinas Pendidikan Kota Makassar')

@section('content')

<div class="top-flag" aria-hidden="true"></div>

<svg class="pattern" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <pattern id="motif" width="64" height="64" patternUnits="userSpaceOnUse" patternTransform="rotate(8)">
            <circle cx="8" cy="8" r="1.4" fill="#2E86C8" opacity="0.10"/>
            <circle cx="40" cy="24" r="1.1" fill="#0B3D63" opacity="0.07"/>
            <circle cx="24" cy="48" r="1.4" fill="#2E86C8" opacity="0.09"/>
        </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#motif)"/>
</svg>

<div class="wrap">
    <main class="card" role="main">

        <div class="card-header">
            <div class="emblem">
                <img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Dinas Pendidikan Kota Makassar" class="emblem-logo">
            </div>
            <p class="eyebrow">PEMERINTAH KOTA MAKASSAR</p>
            <h1 class="org-title">Dinas Pendidikan</h1>
            <p class="org-sub">Sistem Pelayanan Administrasi Kepegawaian</p>
        </div>

        <div class="card-body">

            {{-- Pemilih peran login --}}
            <div class="role-tabs" role="tablist" aria-label="Pilih jenis akun">
                <button type="button"
                        class="role-tab {{ old('login_type', 'sekolah') === 'sekolah' ? 'active' : '' }}"
                        data-role="sekolah"
                        role="tab"
                        aria-selected="{{ old('login_type', 'sekolah') === 'sekolah' ? 'true' : 'false' }}"
                        data-title="Masuk ke akun sekolah"
                        data-hint="Gunakan NPSN dan kata sandi resmi sekolah untuk mengakses layanan kepegawaian.">
                    Sekolah
                </button>
                <button type="button"
                        class="role-tab {{ old('login_type') === 'verifikator' ? 'active' : '' }}"
                        data-role="verifikator"
                        role="tab"
                        aria-selected="{{ old('login_type') === 'verifikator' ? 'true' : 'false' }}"
                        data-title="Masuk sebagai verifikator"
                        data-hint="Gunakan NIP (18 digit) dan kata sandi untuk memverifikasi dokumen yang diunggah sekolah.">
                    Verifikator
                </button>
            </div>

            <h2 class="form-title" id="formTitle">
                {{ old('login_type') === 'verifikator' ? 'Masuk sebagai verifikator' : 'Masuk ke akun sekolah' }}
            </h2>
            <p class="form-hint" id="formHint">
                {{ old('login_type') === 'verifikator'
                    ? 'Gunakan NIP (18 digit) dan kata sandi untuk memverifikasi dokumen yang diunggah sekolah.'
                    : 'Gunakan NPSN dan kata sandi resmi sekolah untuk mengakses layanan kepegawaian.' }}
            </p>

            {{-- Pesan error umum, misalnya NPSN/NIP atau kata sandi salah dari controller --}}
            @if (session('error'))
                <div class="status-msg show">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="8.3" stroke="#8E2027" stroke-width="1.4"/><path d="M10 6.2V10.6" stroke="#8E2027" stroke-width="1.4" stroke-linecap="round"/><circle cx="10" cy="13.4" r="0.9" fill="#8E2027"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="status-msg show">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="8.3" stroke="#8E2027" stroke-width="1.4"/><path d="M10 6.2V10.6" stroke="#8E2027" stroke-width="1.4" stroke-linecap="round"/><circle cx="10" cy="13.4" r="0.9" fill="#8E2027"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="status-msg" id="statusMsg">
                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="8.3" stroke="#8E2027" stroke-width="1.4"/><path d="M10 6.2V10.6" stroke="#8E2027" stroke-width="1.4" stroke-linecap="round"/><circle cx="10" cy="13.4" r="0.9" fill="#8E2027"/></svg>
                <span id="statusMsgText">Lengkapi data akun dan kata sandi terlebih dahulu.</span>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- Menandai controller: 'sekolah' atau 'verifikator' --}}
                <input type="hidden" name="login_type" id="loginType" value="{{ old('login_type', 'sekolah') }}">

                {{-- Field NPSN (tampil saat tab Sekolah aktif) --}}
                <div class="field" id="fieldNpsn" {{ old('login_type') === 'verifikator' ? 'style="display:none"' : '' }}>
                    <label for="npsn">NPSN Sekolah</label>
                    <div class="input-shell">
                        <span class="icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 21H21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M5 21V9.5L12 5L19 9.5V21" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="M9.5 21V15C9.5 14.4 9.9 14 10.5 14H13.5C14.1 14 14.5 14.4 14.5 15V21" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M9.5 10.2H14.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input type="text" id="npsn" name="npsn" value="{{ old('npsn') }}" placeholder="Contoh: 40311521" inputmode="numeric" autocomplete="username" {{ old('login_type', 'sekolah') === 'sekolah' ? 'required' : 'disabled' }}>
                    </div>
                    @error('npsn')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Field NIP (tampil saat tab Verifikator aktif) --}}
                <div class="field" id="fieldNip" {{ old('login_type') === 'verifikator' ? '' : 'style="display:none"' }}>
                    <label for="nip">NIP Verifikator</label>
                    <div class="input-shell">
                        <span class="icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8.2" r="3.2" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M5 20C5 16.4 8.1 13.5 12 13.5C15.9 13.5 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M9 17.3L11 19.2L15.2 15.2" stroke="#C1272D" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="18 digit, contoh: 198501012010011003" inputmode="numeric" maxlength="18" pattern="\d{18}" title="NIP terdiri dari 18 digit angka" autocomplete="username" {{ old('login_type') === 'verifikator' ? 'required' : 'disabled' }}>
                    </div>
                    <p class="field-note">NIP terdiri dari 18 digit angka.</p>
                    @error('nip')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <div class="input-shell">
                        <span class="icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="5" y="10.5" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M8 10.5V7.5C8 5 9.8 3.5 12 3.5C14.2 3.5 16 5 16 7.5V10.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <circle cx="12" cy="15.2" r="1.3" fill="currentColor"/>
                                <path d="M12 16.5V18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row-between">
                    <label class="remember">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya di perangkat ini
                    </label>
                    <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}" class="forgot">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 4H18C19.1 4 20 4.9 20 6V18C20 19.1 19.1 20 18 20H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10 8L14 12L10 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 12H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span id="submitLabel">Masuk ke Sistem</span>
                </button>
            </form>

            <div class="divider">layanan resmi kepegawaian</div>

            <p class="help-line">Kendala akses akun? Hubungi <strong>Bidang Kepegawaian Dinas Pendidikan</strong><br>(0411) 872-xxx &nbsp;•&nbsp; [email protected]</p>
        </div>
    </main>
</div>

<p class="footer-note">© {{ date('Y') }} Dinas Pendidikan Kota Makassar. Sistem Pelayanan Administrasi Kepegawaian.<br>Akses hanya untuk operator sekolah dan verifikator yang terdaftar resmi.</p>

@endsection
