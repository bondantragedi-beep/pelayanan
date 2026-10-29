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
    ];

    // ---- Carousel hero: satu slide per layanan. -------------------------------------
    // Foto (opsional) diletakkan di public/imager/carousel/{slug}.jpg  (juga .jpeg/.png/.webp).
    // Kalau fotonya belum ada, slide tetap tampil dengan latar warna.
    $slideHero = [
        ['slug' => 'kgb',               'judul' => 'Kenaikan Gaji Berkala',  'tersedia' => true,
         'teks' => 'Ajukan KGB pegawai sekolah secara daring. Unggah SK, dinas memverifikasi, surat resmi langsung masuk inbox.',
         'warna' => ['#0B2A55', '#1E5AA8']],
        ['slug' => 'tunjangan',         'judul' => 'Tunjangan Keluarga',     'tersedia' => true,
         'teks' => 'Tunjangan istri/suami dan anak untuk PNS maupun PPPK. Lengkapi berkas, pantau prosesnya dari sekolah.',
         'warna' => ['#0E3B43', '#2B8A8A']],
        ['slug' => 'kenaikan-pangkat',  'judul' => 'Kenaikan Pangkat',       'tersedia' => false,
         'teks' => 'Usul kenaikan pangkat pegawai tanpa bolak-balik ke kantor dinas.',
         'warna' => ['#2A1B4D', '#6B4FB3']],
        ['slug' => 'pensiun',           'judul' => 'Pensiun',                'tersedia' => false,
         'teks' => 'Pengurusan berkas pensiun yang lebih tertata dan mudah dipantau.',
         'warna' => ['#4A2410', '#C0703A']],
        ['slug' => 'cuti',              'judul' => 'Cuti',                   'tersedia' => false,
         'teks' => 'Pengajuan cuti pegawai dengan alur persetujuan yang jelas.',
         'warna' => ['#10402A', '#3E9A6B']],
        ['slug' => 'pencantuman-gelar', 'judul' => 'Pencantuman Gelar',      'tersedia' => false,
         'teks' => 'Pencantuman gelar akademik pada data kepegawaian.',
         'warna' => ['#3A1F3F', '#A04F9A']],
        ['slug' => 'tugas-belajar',     'judul' => 'Tugas Belajar',          'tersedia' => false,
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
    <section class="hc" id="heroCarousel" role="region" aria-roledescription="carousel" aria-label="Layanan kepegawaian">

        <div class="hc-slides">
            @foreach ($slideHero as $i => $sl)
                <article class="hc-slide {{ $i === 0 ? 'is-active' : '' }}"
                         style="--c1: {{ $sl['warna'][0] }}; --c2: {{ $sl['warna'][1] }};"
                         role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} dari {{ count($slideHero) }}"
                         @if ($i !== 0) aria-hidden="true" @endif>
                    @if ($sl['foto'])
                        <img class="hc-img" src="{{ $sl['foto'] }}" alt="" @if ($i !== 0) loading="lazy" @endif>
                    @endif
                    <div class="hc-shade"></div>

                    <div class="container hc-body">
                        <span class="hc-pill"><i></i> Pemerintah Kota Makassar</span>
                        <h1 class="hc-title">{{ $sl['judul'] }}</h1>
                        <p class="hc-text">{{ $sl['teks'] }}</p>
                        <span class="hc-tag {{ $sl['tersedia'] ? 'on' : '' }}">{{ $sl['tersedia'] ? 'Tersedia' : 'Segera hadir' }}</span>

                        <div class="hc-cta">
                            <a href="{{ route('login', ['role' => 'sekolah']) }}" class="btn btn-solid">
                                Masuk sebagai Sekolah
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="{{ route('login', ['role' => 'verifikator']) }}" class="btn btn-ghost">Masuk sebagai Verifikator</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="hc-controls container">
            <div class="hc-dots" role="tablist" aria-label="Pilih layanan">
                @foreach ($slideHero as $i => $sl)
                    <button type="button" class="hc-dot {{ $i === 0 ? 'is-active' : '' }}" role="tab"
                            aria-label="{{ $sl['judul'] }}" @if ($i === 0) aria-current="true" @endif><span></span></button>
                @endforeach
            </div>
            <div class="hc-arrows">
                <button type="button" class="hc-arrow" data-dir="-1" aria-label="Layanan sebelumnya">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" class="hc-arrow" data-dir="1" aria-label="Layanan berikutnya">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
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

                <div class="svc-tile is-on">
                    <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/></svg></span>
                    <h3>Tunjangan</h3>
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
    // Carousel hero: ganti layanan otomatis, jeda saat disentuh / disorot, bisa digeser & pakai panah keyboard.
    (function () {
        var root = document.getElementById('heroCarousel');
        if (!root) { return; }
        var slides = root.querySelectorAll('.hc-slide');
        var dots   = root.querySelectorAll('.hc-dot');
        var n = slides.length, i = 0, timer = null, JEDA = 6000;
        var kurangiGerak = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        root.style.setProperty('--hc-dur', JEDA + 'ms');

        function tampil(k) {
            i = (k + n) % n;
            for (var a = 0; a < n; a++) {
                var aktif = a === i;
                slides[a].classList.toggle('is-active', aktif);
                slides[a].setAttribute('aria-hidden', aktif ? 'false' : 'true');
                dots[a].classList.remove('is-active');
                dots[a].removeAttribute('aria-current');
            }
            void root.offsetWidth; // mulai ulang animasi progres titik
            dots[i].classList.add('is-active');
            dots[i].setAttribute('aria-current', 'true');
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

        root.querySelectorAll('.hc-arrow').forEach(function (b) {
            b.addEventListener('click', function () { tampil(i + parseInt(b.dataset.dir, 10)); mulai(); });
        });
        dots.forEach(function (d, k) { d.addEventListener('click', function () { tampil(k); mulai(); }); });

        root.addEventListener('mouseenter', jeda);
        root.addEventListener('mouseleave', lanjut);
        root.addEventListener('focusin', jeda);
        root.addEventListener('focusout', lanjut);
        document.addEventListener('visibilitychange', function () { document.hidden ? jeda() : lanjut(); });

        root.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { tampil(i - 1); mulai(); }
            if (e.key === 'ArrowRight') { tampil(i + 1); mulai(); }
        });

        var x0 = null;
        root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; jeda(); }, { passive: true });
        root.addEventListener('touchend', function (e) {
            if (x0 !== null) {
                var dx = e.changedTouches[0].clientX - x0;
                if (Math.abs(dx) > 50) { tampil(i + (dx < 0 ? 1 : -1)); }
            }
            x0 = null; lanjut();
        });

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
