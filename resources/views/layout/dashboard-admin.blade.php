<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard Admin — Dinas Pendidikan Kota Makassar')</title>

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
    $adminMenu = [
        [
            'key'   => 'ringkasan',
            'label' => 'Ringkasan',
            'route' => 'admin.dashboard',
            'icon'  => '<rect x="3.5" y="3.5" width="7.5" height="7.5" rx="1.6" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3.5" width="7.5" height="7.5" rx="1.6" stroke="currentColor" stroke-width="1.6"/><rect x="3.5" y="13" width="7.5" height="7.5" rx="1.6" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="13" width="7.5" height="7.5" rx="1.6" stroke="currentColor" stroke-width="1.6"/>',
        ],
        [
            'key'   => 'penugasan',
            'label' => 'Penugasan Sekolah',
            'route' => 'admin.penugasan',
            'icon'  => '<circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 8.5H21M18.5 6V11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        ],
        [
            'key'   => 'data-masuk',
            'label' => 'Data Masuk & Monitoring',
            'route' => 'admin.data-masuk',
            'icon'  => '<path d="M4 6H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 12H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 18H14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
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
            <p class="school-name">{{ $adminNama ?? 'Admin' }}</p>
            <p class="school-npsn">NIP: {{ $adminNip ?? '—' }}</p>
        </div>

        <nav class="dash-nav">
            <p class="dash-nav-label">Menu Admin</p>

            @foreach ($adminMenu as $item)
                <a href="{{ route($item['route']) }}"
                   class="dash-nav-item {{ $activeMenu === $item['key'] ? 'is-active' : '' }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $item['icon'] !!}</svg>
                    </span>
                    <span class="nav-text">{{ $item['label'] }}</span>
                </a>
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
                <h1>@yield('page-title', 'Dashboard Admin')</h1>
                @hasSection('breadcrumb')
                    <p class="dash-breadcrumb">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="dash-user-chip">
                <span class="avatar">{{ strtoupper(substr($adminNama ?? 'A', 0, 1)) }}</span>
                <span>
                    <span class="u-name" style="display:block;">{{ $adminNama ?? 'Admin' }}</span>
                    <span class="u-role">Admin</span>
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
