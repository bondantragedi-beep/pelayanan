@extends('layout.dashboard')

@section('title', 'Dashboard — Operator Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Beranda Operator Sekolah')

@section('content')

<div class="panel" style="margin-bottom:22px;">
    <div class="panel-head" style="margin-bottom:6px;">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h2>Selamat datang, {{ $sekolahNama ?? 'Operator Sekolah' }}</h2>
    </div>
    <p style="font-size:13px;color:var(--muted);margin:0;line-height:1.6;">
        Pilih jenis layanan kepegawaian di bawah ini untuk mengunggah berkas. Layanan yang masih bertanda gembok belum dapat digunakan.
    </p>
</div>

<div class="svc-grid">

    <a href="{{ route('sekolah.kgb') }}" class="svc-card is-open">
        <span class="svc-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 6H20V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <h3 class="svc-title">Kenaikan Gaji Berkala (KGB)</h3>
        <p class="svc-desc">Ajukan berkas kenaikan gaji berkala pegawai sekolah Anda.</p>
        <span class="svc-status open">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tersedia
        </span>
    </a>

    @php
        $lockedServices = [
            ['label' => 'Kenaikan Pangkat', 'desc' => 'Pengajuan kenaikan pangkat pegawai.', 'icon' => '<path d="M12 3L14.6 8.4L20.5 9.3L16.3 13.4L17.3 19.3L12 16.5L6.7 19.3L7.7 13.4L3.5 9.3L9.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>'],
            ['label' => 'Pensiun', 'desc' => 'Pengajuan berkas persiapan pensiun pegawai.', 'icon' => '<circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'],
            ['label' => 'Cuti', 'desc' => 'Pengajuan cuti pegawai sekolah.', 'icon' => '<rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/>'],
            ['label' => 'Pencantuman Gelar', 'desc' => 'Pengajuan pencantuman gelar akademik pegawai.', 'icon' => '<path d="M12 4L21 8.5L12 13L3 8.5L12 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6.5 10.7V15.3C6.5 15.3 8.7 17.5 12 17.5C15.3 17.5 17.5 15.3 17.5 15.3V10.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'],
            ['label' => 'Tugas Belajar', 'desc' => 'Pengajuan izin tugas belajar pegawai.', 'icon' => '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H12V20H5.5C4.7 20 4 19.3 4 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M20 5.5C20 4.7 19.3 4 18.5 4H12V20H18.5C19.3 20 20 19.3 20 18.5V5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>'],
            ['label' => 'Tunjangan', 'desc' => 'Pengajuan tunjangan pegawai sekolah.', 'icon' => '<rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/><circle cx="16.5" cy="14.3" r="1.3" stroke="currentColor" stroke-width="1.4"/>'],
        ];
    @endphp

    @foreach ($lockedServices as $svc)
        <span class="svc-card is-locked" title="Layanan belum tersedia" aria-disabled="true">
            <span class="svc-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $svc['icon'] !!}</svg>
            </span>
            <h3 class="svc-title">{{ $svc['label'] }}</h3>
            <p class="svc-desc">{{ $svc['desc'] }}</p>
            <span class="svc-status locked">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.8C8 5.7 9.8 4 12 4C14.2 4 16 5.7 16 7.8V10.5" stroke="currentColor" stroke-width="1.8"/></svg>
                Segera hadir
            </span>
        </span>
    @endforeach

</div>

@endsection
