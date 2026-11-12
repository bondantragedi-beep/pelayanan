@extends('layout.dashboard-verifikator')

@section('title', 'Berkas Masuk | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Berkas Masuk')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb', 'Berkas dari sekolah di wilayah Anda')

@section('content')

<div class="stat-grid">
    <div class="stat-card warn">
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></div>
        <p class="stat-value">{{ $ringkasan['pending'] }}</p>
        <p class="stat-label">Menunggu Anda periksa</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 18.5L9.2 17.4L18.4 8.2C19.2 7.4 19.2 6.1 18.4 5.3L18.2 5.1C17.4 4.3 16.1 4.3 15.3 5.1L6.1 14.3L5 18.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></div>
        <p class="stat-value">{{ $ringkasan['menunggu_ttd'] }}</p>
        <p class="stat-label">Menunggu tanda tangan</p>
    </div>
    <div class="stat-card ok">
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M8.5 12.5L11 15L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <p class="stat-value">{{ $ringkasan['terkirim'] }}</p>
        <p class="stat-label">Sudah terkirim ke sekolah</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M9 9L15 15M15 9L9 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <p class="stat-value">{{ $ringkasan['rejected'] }}</p>
        <p class="stat-label">Ditolak</p>
    </div>
</div>

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Menunggu diproses — Kecamatan {{ $verifikatorKecamatan ?? '—' }}</h2>
        <span class="count-pill">{{ $berkasMasuk->count() }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sekolah</th>
                        <th>Layanan</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($berkasMasuk as $row)
                        <tr>
                            <td>
                                <span class="cell-strong">{{ $row->sekolah->nama_sekolah }}</span>
                                <span class="cell-sub">NPSN {{ $row->npsn }}</span>
                            </td>
                            <td>{{ $row->labelLayanan() }}</td>
                            <td>{{ $row->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge badge-pending">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    Menunggu diproses
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('verifikator.berkas.show', $row) }}" class="btn-save-row" style="text-decoration:none;display:inline-flex;">
                                    Periksa Berkas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="table-empty">Tidak ada berkas masuk dari sekolah di wilayah Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
