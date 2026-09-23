@extends('layout.guest')

@section('title', 'Beranda — Sistem Pelayanan Administrasi Kepegawaian | Dinas Pendidikan Kota Makassar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

<div class="top-flag" aria-hidden="true"></div>

<div class="landing-wrap">

    <header class="landing-header">
        <div class="landing-emblem">
            <img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Dinas Pendidikan Kota Makassar">
        </div>
        <p class="landing-eyebrow">Pemerintah Kota Makassar</p>
        <h1 class="landing-title">Dinas Pendidikan Kota Makassar</h1>
        <p class="landing-sub">Sistem Pelayanan Administrasi Kepegawaian — layanan digital terpadu untuk pengelolaan dokumen kepegawaian sekolah dan verifikasi oleh dinas.</p>
    </header>

    <main class="landing-body">

        <div class="landing-intro">
            <h2>Masuk ke sistem</h2>
            <p>Pilih jenis akun Anda untuk melanjutkan ke halaman masuk. Setiap peran memiliki akses dan kewenangan yang berbeda di dalam sistem.</p>
        </div>

        <div class="role-grid">

            {{-- Operator Sekolah --}}
            <a href="{{ route('login', ['role' => 'sekolah']) }}" class="role-card is-sekolah">
                <span class="role-card-badge">NPSN</span>
                <span class="role-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 21H21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        <path d="M5 21V9.5L12 5L19 9.5V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M9.5 21V15C9.5 14.4 9.9 14 10.5 14H13.5C14.1 14 14.5 14.4 14.5 15V21" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M9.5 10.2H14.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <h3 class="role-card-title">Operator Sekolah</h3>
                <p class="role-card-desc">Masuk menggunakan NPSN dan kata sandi resmi sekolah untuk mengunggah dan mengelola dokumen administrasi kepegawaian.</p>
                <span class="role-card-cta">
                    Masuk sebagai Operator Sekolah
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>

            {{-- Verifikator Dinas --}}
            <a href="{{ route('login', ['role' => 'verifikator']) }}" class="role-card is-verifikator">
                <span class="role-card-badge">NIP</span>
                <span class="role-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8.2" r="3.2" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M5 20C5 16.4 8.1 13.5 12 13.5C15.9 13.5 19 16.4 19 20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        <path d="M9 17.3L11 19.2L15.2 15.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h3 class="role-card-title">Verifikator Dinas</h3>
                <p class="role-card-desc">Masuk menggunakan NIP (18 digit) dan kata sandi untuk memverifikasi dokumen kepegawaian yang diunggah oleh sekolah.</p>
                <span class="role-card-cta">
                    Masuk sebagai Verifikator Dinas
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </a>

        </div>

        <p class="landing-footer-note">
            © {{ date('Y') }} Dinas Pendidikan Kota Makassar. Sistem Pelayanan Administrasi Kepegawaian.<br>
            Akses hanya untuk operator sekolah dan verifikator yang terdaftar resmi. Kendala akses akun? Hubungi <strong>Bidang Kepegawaian Dinas Pendidikan</strong> (0411) 872-xxx.
        </p>

    </main>

</div>

@endsection
