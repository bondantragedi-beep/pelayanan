@extends('layout.dashboard')

@section('title', 'Dashboard — Operator Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Beranda Operator Sekolah')

@section('content')

@php
    // Warna & ikon per layanan (selaras dengan carousel beranda)
    $layanan = [
        'kgb'       => ['c' => ['#0B2A55', '#1E5AA8'], 'ikon' => '<path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>'],
        'tunjangan' => ['c' => ['#0E3B43', '#2B8A8A'], 'ikon' => '<rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/>'],
        'cuti'      => ['c' => ['#10402A', '#3E9A6B'], 'ikon' => '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/>'],
    ];
    $dokumen = '<path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>';
    $tahapUi = [
        'pending'      => ['Menunggu verifikasi', 'warn'],
        'menunggu_ttd' => ['Sedang diproses', 'info'],
        'terkirim'     => ['Selesai — cek Inbox', 'ok'],
        'rejected'     => ['Ditolak', 'danger'],
    ];
@endphp

{{-- ============ Hero ============ --}}
<section class="hs-hero">
    <div>
        <span class="hs-eyebrow"><i></i> Operator Sekolah</span>
        <h2>Halo, {{ $sekolahNama ?? 'Operator Sekolah' }}</h2>
        <p>Pilih layanan di bawah untuk mengunggah berkas pegawai. Pantau statusnya di Daftar Berkas, dan terima surat resmi dari dinas di Inbox.</p>
        <div class="hs-cta">
            <a href="{{ route('sekolah.berkas') }}" class="hs-btn hs-btn-light">Daftar Berkas Saya</a>
            <a href="{{ route('sekolah.inbox') }}" class="hs-btn hs-btn-ghost">Buka Inbox @if ($stat['selesai'] > 0)<b>{{ $stat['selesai'] }}</b>@endif</a>
        </div>
    </div>
    <div class="hs-art" aria-hidden="true">
        <span></span><span></span><span></span>
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 8L12 4L20 8L12 12L4 8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.5V15C6.5 16.4 9 18 12 18C15 18 17.5 16.4 17.5 15V10.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
    </div>
</section>

{{-- ============ Ringkasan berkas ============ --}}
<div class="hs-stats">
    <a href="{{ route('sekolah.berkas') }}" class="hs-stat warn">
        <span class="hs-stat-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
        <span><span class="hs-stat-n">{{ $stat['menunggu'] }}</span><div class="hs-stat-l">Menunggu verifikasi</div></span>
    </a>
    <a href="{{ route('sekolah.berkas') }}" class="hs-stat info">
        <span class="hs-stat-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 12A8 8 0 1 1 16.5 5.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M20 4V8H16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span><span class="hs-stat-n">{{ $stat['diproses'] }}</span><div class="hs-stat-l">Sedang diproses</div></span>
    </a>
    <a href="{{ route('sekolah.inbox') }}" class="hs-stat ok">
        <span class="hs-stat-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M8.5 12.5L11 15L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span><span class="hs-stat-n">{{ $stat['selesai'] }}</span><div class="hs-stat-l">Selesai (di Inbox)</div></span>
    </a>
    <a href="{{ route('sekolah.berkas') }}" class="hs-stat danger">
        <span class="hs-stat-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M9 9L15 15M15 9L9 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span><span class="hs-stat-n">{{ $stat['ditolak'] }}</span><div class="hs-stat-l">Perlu diperbaiki</div></span>
    </a>
</div>

{{-- ============ Layanan ============ --}}
<div class="hs-h"><h3>Ajukan Layanan</h3></div>

<div class="sv-grid">
    @foreach ([
        ['kgb', 'sekolah.kgb', 'Kenaikan Gaji Berkala (KGB)', 'Ajukan berkas kenaikan gaji berkala pegawai sekolah Anda.'],
        ['tunjangan', 'sekolah.tunjangan', 'Tunjangan', 'Ajukan tunjangan istri/suami atau anak pegawai sekolah Anda.'],
        ['cuti', 'sekolah.cuti', 'Cuti', 'Ajukan cuti tahunan, besar, sakit, melahirkan, atau alasan penting.'],
    ] as [$kode, $rute, $judul, $desk])
        <a href="{{ route($rute) }}" class="sv-card" style="--c1: {{ $layanan[$kode]['c'][0] }}; --c2: {{ $layanan[$kode]['c'][1] }};">
            <span class="sv-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $layanan[$kode]['ikon'] !!}</svg></span>
            <h4>{{ $judul }}</h4>
            <p>{{ $desk }}</p>
            <span class="sv-foot">
                <span class="sv-tag">Tersedia</span>
                <span class="sv-go"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </span>
        </a>
    @endforeach
</div>

<div class="sv-soon">
        <div class="sv-lock" title="Layanan belum tersedia" aria-disabled="true">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span>
            <span><b>Kenaikan Pangkat</b><small>Segera hadir</small></span>
        </div>
        <div class="sv-lock" title="Layanan belum tersedia" aria-disabled="true">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span>
            <span><b>Pensiun</b><small>Segera hadir</small></span>
        </div>
        <div class="sv-lock" title="Layanan belum tersedia" aria-disabled="true">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span><b>Pencantuman Gelar</b><small>Segera hadir</small></span>
        </div>
        <div class="sv-lock" title="Layanan belum tersedia" aria-disabled="true">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span>
            <span><b>Tugas Belajar</b><small>Segera hadir</small></span>
        </div>
</div>

{{-- ============ Berkas terbaru ============ --}}
<div class="hs-h">
    <h3>Berkas Terbaru</h3>
    @if ($terbaru->isNotEmpty())<a href="{{ route('sekolah.berkas') }}">Lihat semua &rarr;</a>@endif
</div>

<div class="panel hs-recent">
    @forelse ($terbaru as $b)
        @php
            [$teksTahap, $kelasTahap] = $tahapUi[$b->tahapVerifikator()];
            $w = $layanan[$b->jenis_layanan]['c'] ?? ['#16384F', '#3A7CA5'];
            $ik = $layanan[$b->jenis_layanan]['ikon'] ?? $dokumen;
        @endphp
        <a href="{{ $b->tahapVerifikator() === 'terkirim' ? route('sekolah.inbox') : route('sekolah.berkas') }}" class="hs-row" style="--c1: {{ $w[0] }}; --c2: {{ $w[1] }};">
            <span class="hs-row-ic"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $ik !!}</svg></span>
            <span class="hs-row-t"><b>{{ $b->labelLayanan() }}</b><small>Diunggah {{ $b->created_at->translatedFormat('d M Y, H:i') }}</small></span>
            <span class="hs-chip {{ $kelasTahap }}">{{ $teksTahap }}</span>
        </a>
    @empty
        <div class="hs-empty">Belum ada berkas. Pilih salah satu layanan di atas untuk memulai.</div>
    @endforelse
</div>

@endsection
