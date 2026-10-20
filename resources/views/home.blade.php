@extends('layout.guest')

@section('title', 'Beranda — Sistem Pelayanan Administrasi Kepegawaian | Dinas Pendidikan Kota Makassar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@php
    // Foto hero bersifat opsional. Letakkan di public/imager/hero.jpg
    $adaFoto = file_exists(public_path('imager/hero.jpg'));

    $layananLain = [
        ['Kenaikan Pangkat',  '<path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>'],
        ['Pensiun',           '<circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'],
        ['Cuti',              '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5M8 3V6.5M16 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'],
        ['Pencantuman Gelar', '<path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'],
        ['Tugas Belajar',     '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>'],
        ['Tunjangan',         '<rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/>'],
    ];
@endphp

@section('content')

<script>document.documentElement.classList.add('js');</script>

{{-- ================= Navigasi ================= --}}
<header class="site-nav" id="siteNav">
    <div class="container nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Dinas Pendidikan Kota Makassar">
            <span>Dinas Pendidikan<small>Kota Makassar</small></span>
        </a>
        <nav class="nav-links" aria-label="Navigasi utama">
            <a href="#alur" class="nav-link">Alur</a>
            <a href="#layanan" class="nav-link">Layanan</a>
            <a href="#masuk" class="btn btn-solid btn-sm">Masuk</a>
        </nav>
    </div>
</header>

<main>

    {{-- ================= Hero ================= --}}
    <section class="hero">
        <div class="container hero-grid">

            <div class="reveal">
                <span class="hero-pill"><i></i> Pemerintah Kota Makassar</span>
                <h1>Layanan kepegawaian sekolah, <em>lebih ringkas.</em></h1>
                <p class="hero-lead">Sistem pelayanan administrasi kepegawaian Dinas Pendidikan. Unggah berkas dari sekolah, diverifikasi oleh dinas, dan surat resmi langsung diterima secara digital.</p>

                <div class="hero-cta">
                    <a href="{{ route('login', ['role' => 'sekolah']) }}" class="btn btn-solid">
                        Masuk sebagai Sekolah
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="{{ route('login', ['role' => 'verifikator']) }}" class="btn btn-line">Masuk sebagai Verifikator</a>
                </div>

                <ul class="hero-checks">
                    @foreach (['Pantau status berkas', 'Diverifikasi oleh dinas', 'Surat resmi digital'] as $poin)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $poin }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="hero-visual reveal {{ $adaFoto ? 'has-photo' : '' }}">
                @if ($adaFoto)
                    <img class="hero-photo" src="{{ asset('imager/hero.jpg') }}" alt="">
                @else
                    <img class="hero-mark" src="{{ asset('imager/logo disdik.PNG') }}" alt="" aria-hidden="true">
                @endif

                <div class="status-card" aria-hidden="true">
                    <div class="status-card-head">
                        <div>
                            <p class="status-card-title">Pengajuan KGB</p>
                            <p class="status-card-sub">Contoh alur berkas</p>
                        </div>
                        <span class="mini-badge">Diproses</span>
                    </div>
                    <ul class="status-steps">
                        <li class="done">
                            <span class="dot"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12.5L10 17.5L19 7.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Berkas diunggah sekolah
                        </li>
                        <li class="now">
                            <span class="dot"></span>
                            Diverifikasi verifikator
                        </li>
                        <li class="wait">
                            <span class="dot"></span>
                            Surat dicetak &amp; ditandatangani
                        </li>
                        <li class="wait">
                            <span class="dot"></span>
                            Dikirim ke inbox sekolah
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= Alur ================= --}}
    <section class="section section-tint" id="alur">
        <div class="container">
            <div class="section-head reveal">
                <span class="section-eyebrow">Alur layanan</span>
                <h2 class="section-title">Tiga langkah, tanpa bolak-balik ke kantor.</h2>
                <p class="section-desc">Seluruh proses berjalan di dalam sistem, dari pengajuan sampai surat diterima.</p>
            </div>

            <div class="steps reveal">
                <div class="step">
                    <span class="step-num">1</span>
                    <h3>Sekolah mengunggah berkas</h3>
                    <p>Operator sekolah memilih layanan, melengkapi persyaratan, lalu mengunggah dokumen pegawai.</p>
                </div>
                <div class="step">
                    <span class="step-num">2</span>
                    <h3>Dinas memverifikasi</h3>
                    <p>Verifikator memeriksa kelengkapan berkas, lalu menerima atau menolak disertai alasan yang jelas.</p>
                </div>
                <div class="step">
                    <span class="step-num">3</span>
                    <h3>Surat diterima di inbox</h3>
                    <p>Surat resmi yang telah dicetak dan ditandatangani dikirim langsung ke inbox sekolah.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Layanan ================= --}}
    <section class="section" id="layanan">
        <div class="container">
            <div class="section-head reveal">
                <span class="section-eyebrow">Layanan</span>
                <h2 class="section-title">Satu sistem untuk banyak urusan.</h2>
                <p class="section-desc">Layanan akan terus ditambah secara bertahap.</p>
            </div>

            <div class="svc-list reveal">
                <div class="svc-tile is-on">
                    <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>Kenaikan Gaji Berkala (KGB)</h3>
                    <span class="tag">Tersedia</span>
                </div>

                @foreach ($layananLain as [$nama, $ikon])
                    <div class="svc-tile">
                        <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $ikon !!}</svg></span>
                        <h3>{{ $nama }}</h3>
                        <span class="tag">Segera hadir</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= Pilih peran ================= --}}
    <section class="section section-tint" id="masuk">
        <div class="container">
            <div class="section-head reveal">
                <span class="section-eyebrow">Masuk</span>
                <h2 class="section-title">Pilih jenis akun Anda.</h2>
                <p class="section-desc">Setiap peran memiliki akses dan kewenangan yang berbeda di dalam sistem.</p>
            </div>

            <div class="role-grid reveal">

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
        </div>
    </section>

</main>

{{-- ================= Footer ================= --}}
<footer class="site-footer">
    <div class="container">
      <div class="footer-inner">
        <p>
            © {{ date('Y') }} Dinas Pendidikan Kota Makassar. Sistem Pelayanan Administrasi Kepegawaian.<br>
            Akses hanya untuk operator sekolah dan verifikator yang terdaftar resmi. Kendala akses akun? Hubungi <strong>Bidang Kepegawaian Dinas Pendidikan</strong> (0411) 872-xxx.
        </p>
        <a href="#masuk" class="btn btn-line btn-sm">Masuk ke sistem</a>
      </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
    // Bayangan garis pada navigasi saat halaman digulir + animasi muncul halus
    (function () {
        var nav = document.getElementById('siteNav');
        function onScroll() { nav.classList.toggle('is-scrolled', window.scrollY > 8); }
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        var items = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12 });
        items.forEach(function (el) { io.observe(el); });
    })();
</script>
@endpush
