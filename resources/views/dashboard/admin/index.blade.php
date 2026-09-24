@extends('layout.dashboard-admin')

@section('title', 'Ringkasan Admin | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Ringkasan')
@section('active-menu', 'ringkasan')
@section('breadcrumb', 'Beranda Admin')

@section('content')

<div class="mock-note">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
    <span>Angka di bawah ini masih data contoh (dummy). Setelah database sekolah, pegawai, dan pengajuan siap, halaman ini akan menghitung otomatis dari data asli.</span>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="stat-value">{{ $totalSekolah ?? 0 }}</p>
        <p class="stat-label">Total sekolah terdaftar</p>
    </div>
    <div class="stat-card">
        <span class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </span>
        <p class="stat-value">{{ $totalVerifikator ?? 0 }}</p>
        <p class="stat-label">Verifikator aktif</p>
    </div>
    <div class="stat-card warn">
        <span class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        </span>
        <p class="stat-value">{{ $belumDiproses ?? 0 }}</p>
        <p class="stat-label">Belum diproses verifikator</p>
    </div>
    <div class="stat-card ok">
        <span class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        </span>
        <p class="stat-value">{{ $sudahDiproses ?? 0 }}</p>
        <p class="stat-label">Sudah diproses hari ini</p>
    </div>
    <div class="stat-card danger">
        <span class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.8C8 5.7 9.8 4 12 4C14.2 4 16 5.7 16 7.8V10.5" stroke="currentColor" stroke-width="1.8"/></svg>
        </span>
        <p class="stat-value">{{ $sekolahBelumDitugaskan ?? 0 }}</p>
        <p class="stat-label">Sekolah belum ada verifikator</p>
    </div>
</div>

<div class="panel">
    <div class="section-heading">
        <h2>Data masuk terbaru</h2>
        <a href="{{ route('admin.data-masuk') }}" class="count-pill" style="text-decoration:none;">Lihat semua →</a>
    </div>

    <div class="table-wrap">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sekolah</th>
                        <th>Layanan</th>
                        <th>Verifikator</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($dataMasukTerbaru ?? []) as $row)
                        <tr>
                            <td class="cell-strong">{{ $row['sekolah'] }}</td>
                            <td>{{ $row['layanan'] }}</td>
                            <td>{{ $row['verifikator'] ?? '—' }}</td>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>
                                @if ($row['status'] === 'processed')
                                    <span class="badge badge-processed">Sudah diproses</span>
                                @elseif ($row['status'] === 'rejected')
                                    <span class="badge badge-rejected">Ditolak</span>
                                @else
                                    <span class="badge badge-pending">Belum diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="table-empty">Belum ada data masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
