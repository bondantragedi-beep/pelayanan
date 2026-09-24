<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard Verifikator — Dinas Pendidikan Kota Makassar')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('imager/logo disdik.PNG') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body class="dash-body">

@php
    // 'route' => null berarti menu masih terkunci (langkah selanjutnya, belum dikerjakan).
    $verifikatorMenu = [
        [
            'key'   => 'berkas-masuk',
            'label' => 'Berkas Masuk',
            'route' => 'verifikator.dashboard',
            'icon'  => '<path d="M4 6H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 12H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 18H14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
        ],
        [
            'key'   => 'riwayat',
            'label' => 'Riwayat Diproses',
            'route' => null,
            'icon'  => '<circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
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
            <p class="school-label">Masuk sebagai</p>
            <p class="school-name">{{ $verifikatorNama ?? 'Verifikator' }}</p>
            <p class="school-npsn">Wilayah: {{ $verifikatorKecamatan ?? '—' }}</p>
        </div>

        <nav class="dash-nav">
            <p class="dash-nav-label">Menu Verifikator</p>

            @foreach ($verifikatorMenu as $item)
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
                <h1>@yield('page-title', 'Berkas Masuk')</h1>
                @hasSection('breadcrumb')
                    <p class="dash-breadcrumb">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="dash-user-chip">
                <span class="avatar">{{ strtoupper(substr($verifikatorNama ?? 'V', 0, 1)) }}</span>
                <span>
                    <span class="u-name" style="display:block;">{{ $verifikatorNama ?? 'Verifikator' }}</span>
                    <span class="u-role">Verifikator Dinas</span>
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

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
@stack('scripts')
</body>
</html>
