<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard Sekolah — Dinas Pendidikan Kota Makassar')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('imager/logo disdik.PNG') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @stack('styles')
</head>
<body class="dash-body">

@php
    /*
      Menu layanan sekolah.
      'route' => null berarti menu masih terkunci (tampilan saja, belum bisa dibuka).
      Tambahkan controller + route baru lalu isi 'route' di sini saat modul lain sudah siap.
    */
    $sekolahMenu = [
        [
            'key'   => 'kgb',
            'label' => 'Kenaikan Gaji Berkala (KGB)',
            'route' => 'sekolah.kgb',
            'icon'  => '<path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
        [
            'key'   => 'pangkat',
            'label' => 'Kenaikan Pangkat',
            'route' => null,
            'icon'  => '<path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
        ],
        [
            'key'   => 'pensiun',
            'label' => 'Pensiun',
            'route' => null,
            'icon'  => '<circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M9 3.2L12 1L15 3.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
        [
            'key'   => 'cuti',
            'label' => 'Cuti',
            'route' => null,
            'icon'  => '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        ],
        [
            'key'   => 'gelar',
            'label' => 'Pencantuman Gelar',
            'route' => null,
            'icon'  => '<path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
        [
            'key'   => 'tugas-belajar',
            'label' => 'Tugas Belajar',
            'route' => null,
            'icon'  => '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
        ],
        [
            'key'   => 'tunjangan',
            'label' => 'Tunjangan',
            'route' => null,
            'icon'  => '<rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/>',
        ],
    ];

    $activeMenu = trim($__env->yieldContent('active-menu', ''));
@endphp

<div class="dash-shell">

    <div id="sidebarOverlay"></div>

    <aside class="dash-sidebar" id="dashSidebar">

        <div class="dash-sidebar-head">
            <div class="emblem-sm">
                <img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Disdik">
            </div>
            <div class="org-name">
                Dinas Pendidikan
                <small>Kota Makassar</small>
            </div>
        </div>

        <div class="dash-sidebar-school">
            <p class="school-label">Sekolah</p>
            <p class="school-name">{{ $sekolahNama ?? 'Nama Sekolah' }}</p>
            <p class="school-npsn">NPSN: {{ $sekolahNpsn ?? '—' }}</p>
        </div>

        <nav class="dash-nav">
            <p class="dash-nav-label">Layanan</p>

            @foreach ($sekolahMenu as $item)
                @if ($item['route'])
                    <a href="{{ route($item['route']) }}"
                       class="dash-nav-item {{ $activeMenu === $item['key'] ? 'is-active' : '' }}">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $item['icon'] !!}</svg>
                        </span>
                        <span class="nav-text">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="dash-nav-item is-locked" title="Menu belum tersedia" aria-disabled="true">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $item['icon'] !!}</svg>
                        </span>
                        <span class="nav-text">{{ $item['label'] }}</span>
                        <svg class="lock-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M8 10.5V7.8C8 5.7 9.8 4 12 4C14.2 4 16 5.7 16 7.8V10.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </span>
                @endif
            @endforeach
        </nav>

        <div class="dash-sidebar-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dash-logout">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="dash-content">
        <header class="dash-topbar">
            <button class="dash-burger" id="sidebarToggle" aria-label="Buka menu">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
            <div>
                <h1>@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <p class="dash-breadcrumb">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="dash-user-chip">
                <span class="avatar">{{ strtoupper(substr($sekolahNama ?? 'S', 0, 1)) }}</span>
                <span>
                    <span class="u-name" style="display:block;">{{ $sekolahNama ?? 'Operator Sekolah' }}</span>
                    <span class="u-role">Operator Sekolah</span>
                </span>
            </div>
        </header>

        <main class="dash-main">
            @if (session('success'))
                <div class="dash-alert success">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="dash-alert error">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
                    <span>Periksa kembali berkas yang diunggah: {{ $errors->first() }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
@stack('scripts')
</body>
</html>
