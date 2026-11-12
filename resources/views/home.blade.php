@extends('layout.guest')

@section('title', 'Beranda — Sistem Pelayanan Administrasi Kepegawaian | Dinas Pendidikan Kota Makassar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@php
    // Foto hero bersifat opsional. Letakkan di public/imager/hero.jpg
    $adaFoto = file_exists(public_path('imager/hero.jpg'));

    $layananLain = [
        ['Kenaikan Pangkat',  '<path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>', false],
        ['Pensiun',           '<circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>', false],
        ['Cuti',              '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5M8 3V6.5M16 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>', true],
        ['Pencantuman Gelar', '<path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>', false],
        ['Tugas Belajar',     '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>', false],
    ];

    // ---- Carousel hero: satu slide per layanan. -------------------------------------
    // Foto (opsional) diletakkan di public/imager/carousel/{slug}.jpg  (juga .jpeg/.png/.webp).
    // Kalau fotonya belum ada, slide tetap tampil dengan latar warna.
    $slideHero = [
        ['slug' => 'kgb', 'pendek' => 'KGB', 'ikon' => '<path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',               'judul' => 'Kenaikan Gaji Berkala',  'tersedia' => true,
         'teks' => 'Ajukan KGB pegawai sekolah secara daring. Unggah SK, dinas memverifikasi, surat resmi langsung masuk inbox.',
         'warna' => ['#0B2A55', '#1E5AA8']],
        ['slug' => 'tunjangan', 'pendek' => 'Tunjangan', 'ikon' => '<rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/>',         'judul' => 'Tunjangan Keluarga',     'tersedia' => true,
         'teks' => 'Tunjangan istri/suami dan anak untuk PNS maupun PPPK. Lengkapi berkas, pantau prosesnya dari sekolah.',
         'warna' => ['#0E3B43', '#2B8A8A']],
        ['slug' => 'kenaikan-pangkat', 'pendek' => 'Kenaikan Pangkat', 'ikon' => '<path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',  'judul' => 'Kenaikan Pangkat',       'tersedia' => false,
         'teks' => 'Usul kenaikan pangkat pegawai tanpa bolak-balik ke kantor dinas.',
         'warna' => ['#2A1B4D', '#6B4FB3']],
        ['slug' => 'pensiun', 'pendek' => 'Pensiun', 'ikon' => '<circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',           'judul' => 'Pensiun',                'tersedia' => false,
         'teks' => 'Pengurusan berkas pensiun yang lebih tertata dan mudah dipantau.',
         'warna' => ['#4A2410', '#C0703A']],
        ['slug' => 'cuti', 'pendek' => 'Cuti', 'ikon' => '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5M8 3V6.5M16 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',              'judul' => 'Cuti',                   'tersedia' => true,
         'teks' => 'Ajukan cuti tahunan, besar, sakit, melahirkan, atau alasan penting. Formulir resmi tercetak otomatis dari data pegawai.',
         'warna' => ['#10402A', '#3E9A6B']],
        ['slug' => 'pencantuman-gelar', 'pendek' => 'Pencantuman Gelar', 'ikon' => '<path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>', 'judul' => 'Pencantuman Gelar',      'tersedia' => false,
         'teks' => 'Pencantuman gelar akademik pada data kepegawaian.',
         'warna' => ['#3A1F3F', '#A04F9A']],
        ['slug' => 'tugas-belajar', 'pendek' => 'Tugas Belajar', 'ikon' => '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',     'judul' => 'Tugas Belajar',          'tersedia' => false,
         'teks' => 'Layanan tugas belajar bagi pegawai yang melanjutkan pendidikan.',
         'warna' => ['#16384F', '#3A7CA5']],
    ];

    foreach ($slideHero as &$sl) {
        $sl['foto'] = null;
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (file_exists(public_path("imager/carousel/{$sl['slug']}.{$ext}"))) {
                $sl['foto'] = asset("imager/carousel/{$sl['slug']}.{$ext}");
                break;
            }
        }
    }
    unset($sl);
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

    {{-- ================= Hero (carousel layanan) ================= --}}
    @php $nSlide = count($slideHero); @endphp
    <section class="hc" id="heroCarousel" role="region" aria-roledescription="carousel" aria-label="Layanan kepegawaian">

        <div class="hc-bg" aria-hidden="true"><span class="b1"></span><span class="b2"></span></div>

        <div class="container hc-head">
            <span class="hc-pill"><i></i> Pemerintah Kota Makassar</span>
            <h1 class="hc-h1">Sistem Pelayanan Administrasi <em>Kepegawaian</em></h1>
            <p class="hc-sub">Ajukan, pantau, dan terima surat resmi dari Dinas Pendidikan Kota Makassar.</p>
        </div>

        {{-- Panggung poster: yang aktif di tengah, tetangganya mengintip di sisi --}}
        <div class="hc-stage" id="hcStage">
            @foreach ($slideHero as $i => $sl)
                @php
                    $d = $i; if ($d > $nSlide / 2) { $d -= $nSlide; }
                    $kelas = $d === 0 ? 'is-active' : (abs($d) === 1 ? 'is-near' : 'is-far');
                @endphp
                <figure class="hc-poster {{ $kelas }}" style="--d: {{ $d }}; --c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};"
                        role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} dari {{ $nSlide }}: {{ $sl['judul'] }}"
                        @if ($i !== 0) aria-hidden="true" @endif>
                    <div class="hc-frame">
                        @if ($sl['foto'])
                            <img class="hc-img" src="{{ $sl['foto'] }}" alt="Poster layanan {{ $sl['judul'] }}" draggable="false" @if ($i > 1 && $i < $nSlide - 1) loading="lazy" @endif>
                        @else
                            <div class="hc-fallback"><span>{{ $sl['judul'] }}</span></div>
                        @endif
                        <span class="hc-gloss"></span>
                    </div>
                </figure>
            @endforeach

            <button type="button" class="hc-arrow hc-prev" data-dir="-1" aria-label="Layanan sebelumnya">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="hc-arrow hc-next" data-dir="1" aria-label="Layanan berikutnya">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>

        {{-- Kartu info melayang, menimpa tepi bawah poster --}}
        <div class="container hc-panelwrap">
            <div class="hc-panel">
                @foreach ($slideHero as $i => $sl)
                    <div class="hc-info {{ $i === 0 ? 'is-active' : '' }}" style="--c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};"
                         @if ($i !== 0) aria-hidden="true" @endif>
                        <span class="hc-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $sl['ikon'] !!}</svg></span>
                        <div class="hc-copy">
                            <div class="hc-meta">
                                <span class="hc-count">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($nSlide, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="hc-tag {{ $sl['tersedia'] ? 'on' : '' }}">{{ $sl['tersedia'] ? 'Tersedia' : 'Segera hadir' }}</span>
                            </div>
                            <h2 class="hc-title">{{ $sl['judul'] }}</h2>
                            <p class="hc-text">{{ $sl['teks'] }}</p>
                        </div>
                        <div class="hc-cta">
                            <a href="{{ route('login', ['role' => 'sekolah']) }}" class="btn btn-solid">
                                Masuk sebagai Sekolah
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="{{ route('login', ['role' => 'verifikator']) }}" class="btn btn-line">Masuk sebagai Verifikator</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Rel pilihan layanan (juga penanda progres autoplay) --}}
        <div class="container">
            <div class="hc-rail" role="tablist" aria-label="Pilih layanan">
                @foreach ($slideHero as $i => $sl)
                    <button type="button" class="hc-chip {{ $i === 0 ? 'is-active' : '' }} {{ $sl['tersedia'] ? 'is-ready' : '' }}" role="tab"
                            style="--c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};"
                            aria-label="{{ $sl['judul'] }}{{ $sl['tersedia'] ? '' : ' (segera hadir)' }}" @if ($i === 0) aria-current="true" @endif>
                        <span class="hc-chip-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $sl['ikon'] !!}</svg></span>
                        <span class="hc-chip-tx">{{ $sl['pendek'] }}</span>
                        <span class="hc-chip-bar"></span>
                    </button>
                @endforeach
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

            <ol class="al-steps reveal">
                <li class="al-step" style="--c1:#0B2A55;--c2:#1E5AA8;">
                    <span class="al-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="al-no">Langkah 1</span>
                    <h3>Sekolah mengunggah berkas</h3>
                    <p>Operator sekolah memilih layanan, melengkapi persyaratan, lalu mengunggah dokumen pegawai.</p>
                </li>
                <li class="al-step" style="--c1:#0E3B43;--c2:#2B8A8A;">
                    <span class="al-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L19 6V11.5C19 15.8 16.1 19.4 12 21C7.9 19.4 5 15.8 5 11.5V6L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8.8 12L11 14.2L15.4 9.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="al-no">Langkah 2</span>
                    <h3>Dinas memverifikasi</h3>
                    <p>Verifikator memeriksa kelengkapan berkas, lalu menerima atau menolak disertai alasan yang jelas.</p>
                </li>
                <li class="al-step" style="--c1:#10402A;--c2:#3E9A6B;">
                    <span class="al-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5.5" width="18" height="13" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 8L12 13.5L20.5 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="al-no">Langkah 3</span>
                    <h3>Surat diterima di inbox</h3>
                    <p>Surat resmi yang telah dicetak dan ditandatangani dikirim langsung ke inbox sekolah.</p>
                </li>
            </ol>
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

            {{-- Layanan yang sudah bisa dipakai --}}
            <div class="ly-grid reveal">
                @foreach (collect($slideHero)->where('tersedia', true) as $sl)
                    <a href="{{ route('login', ['role' => 'sekolah']) }}" class="ly-card" style="--c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};">
                        <span class="ly-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $sl['ikon'] !!}</svg></span>
                        <h3>{{ $sl['judul'] }}</h3>
                        <p>{{ $sl['teks'] }}</p>
                        <span class="ly-foot">
                            <span class="ly-tag">Tersedia</span>
                            <span class="ly-go">Masuk <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Layanan berikutnya --}}
            @php $segera = collect($slideHero)->where('tersedia', false); @endphp
            @if ($segera->isNotEmpty())
                <div class="ly-soon reveal">
                    <span class="ly-soon-t">Segera hadir</span>
                    @foreach ($segera as $sl)
                        <span class="ly-pill" style="--c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};">
                            <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $sl['ikon'] !!}</svg></span>
                            {{ $sl['pendek'] }}
                        </span>
                    @endforeach
                </div>
            @endif
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
<footer class="site-footer ft">
    <div class="container">
        <div class="ft-top">
            <div class="ft-brand">
                <span class="ft-logo"><img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Dinas Pendidikan Kota Makassar"></span>
                <div>
                    <strong>Dinas Pendidikan Kota Makassar</strong>
                    <span>Sistem Pelayanan Administrasi Kepegawaian</span>
                </div>
            </div>
            <nav class="ft-links" aria-label="Tautan footer">
                <a href="#alur">Alur</a>
                <a href="#layanan">Layanan</a>
                <a href="#masuk">Masuk</a>
            </nav>
            <a href="#masuk" class="btn btn-light btn-sm">Masuk ke sistem</a>
        </div>
        <div class="ft-bottom">
            <p>© {{ date('Y') }} Dinas Pendidikan Kota Makassar. Akses hanya untuk operator sekolah dan verifikator yang terdaftar resmi.</p>
            <p>Kendala akses akun? Hubungi <strong>Bidang Kepegawaian Dinas Pendidikan</strong> (0411) 872-xxx.</p>
        </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
    // Carousel hero (coverflow): otomatis ganti layanan, jeda saat disorot/disentuh,
    // bisa digeser (jari/mouse), klik poster tetangga, panah keyboard, dan rel pilihan layanan.
    (function () {
        var root = document.getElementById('heroCarousel');
        if (!root) { return; }
        var stage   = document.getElementById('hcStage');
        var posters = [].slice.call(root.querySelectorAll('.hc-poster'));
        var infos   = root.querySelectorAll('.hc-info');
        var chips   = root.querySelectorAll('.hc-chip');
        var rail    = root.querySelector('.hc-rail');
        var n = posters.length, i = 0, timer = null, JEDA = 6500;
        var kurangiGerak = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        root.style.setProperty('--hc-dur', JEDA + 'ms');

        function tampil(k) {
            i = ((k % n) + n) % n;
            for (var a = 0; a < n; a++) {
                var d = ((a - i) % n + n) % n;      // jarak melingkar terpendek ke slide aktif
                if (d > n / 2) { d -= n; }
                var kelas = d === 0 ? 'is-active' : (Math.abs(d) === 1 ? 'is-near' : 'is-far');
                posters[a].style.setProperty('--d', d);
                posters[a].classList.remove('is-active', 'is-near', 'is-far');
                posters[a].classList.add(kelas);
                posters[a].setAttribute('aria-hidden', d === 0 ? 'false' : 'true');

                infos[a].classList.toggle('is-active', d === 0);
                infos[a].setAttribute('aria-hidden', d === 0 ? 'false' : 'true');

                chips[a].classList.remove('is-active');
                chips[a].removeAttribute('aria-current');
            }
            void root.offsetWidth;                  // mulai ulang animasi progres
            chips[i].classList.add('is-active');
            chips[i].setAttribute('aria-current', 'true');

            if (rail.scrollWidth > rail.clientWidth) {   // layar kecil: rel ikut menggulir ke chip aktif
                rail.scrollTo({ left: chips[i].offsetLeft - (rail.clientWidth - chips[i].offsetWidth) / 2, behavior: 'smooth' });
            }
        }
        function mulai() {
            berhenti();
            if (kurangiGerak || n < 2) { return; }
            root.classList.remove('is-paused');
            timer = setInterval(function () { tampil(i + 1); }, JEDA);
        }
        function berhenti() { if (timer) { clearInterval(timer); timer = null; } }
        function jeda()   { berhenti(); root.classList.add('is-paused'); }
        function lanjut() { tampil(i); mulai(); }
        function ke(k)    { tampil(k); mulai(); }

        root.querySelectorAll('.hc-arrow').forEach(function (b) {
            b.addEventListener('click', function () { ke(i + parseInt(b.dataset.dir, 10)); });
        });
        chips.forEach(function (c, k) { c.addEventListener('click', function () { ke(k); }); });

        [stage, root.querySelector('.hc-panelwrap'), rail].forEach(function (el) {
            el.addEventListener('mouseenter', jeda);
            el.addEventListener('mouseleave', lanjut);
        });
        root.addEventListener('focusin', jeda);
        root.addEventListener('focusout', lanjut);
        document.addEventListener('visibilitychange', function () { document.hidden ? jeda() : lanjut(); });

        root.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { ke(i - 1); }
            if (e.key === 'ArrowRight') { ke(i + 1); }
        });

        /* Geser dengan jari / mouse; klik poster tetangga untuk pindah ke sana */
        var x0 = null, geser = false;
        stage.addEventListener('pointerdown', function (e) {
            if (e.target.closest('.hc-arrow')) { return; }
            x0 = e.clientX; geser = false; jeda();
        });
        stage.addEventListener('pointermove', function (e) {
            if (x0 !== null && Math.abs(e.clientX - x0) > 8) { geser = true; }
            if (e.pointerType !== 'mouse' || x0 !== null || kurangiGerak) { return; }
            var r = stage.getBoundingClientRect();     // kemiringan 3D halus mengikuti mouse
            stage.style.setProperty('--ry', (((e.clientX - r.left) / r.width - .5) * 6).toFixed(2) + 'deg');
            stage.style.setProperty('--rx', (((e.clientY - r.top) / r.height - .5) * -4).toFixed(2) + 'deg');
        });
        stage.addEventListener('pointerleave', function () {
            stage.style.setProperty('--rx', '0deg'); stage.style.setProperty('--ry', '0deg');
        });
        stage.addEventListener('pointerup', function (e) {
            if (x0 === null) { return; }
            var dx = e.clientX - x0;
            if (Math.abs(dx) > 50) { tampil(i + (dx < 0 ? 1 : -1)); }
            else if (!geser) {
                var p = e.target.closest('.hc-poster.is-near');
                if (p) { tampil(posters.indexOf(p)); }
            }
            x0 = null;
            if (e.pointerType !== 'mouse') { lanjut(); }
        });
        stage.addEventListener('pointercancel', function () { x0 = null; lanjut(); });

        if (kurangiGerak) { root.classList.add('is-paused'); }
        mulai();
    })();
</script>
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
