@extends('layout.dashboard-admin')

@section('title', 'Data Masuk & Monitoring | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Data Masuk & Monitoring')
@section('active-menu', 'data-masuk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Data Masuk & Monitoring</span>
@endsection

@section('content')

<div class="mock-note">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
    <span>Data pada tabel masih contoh. Nanti diambil dari tabel pengajuan berkas (tanggal masuk, sekolah, verifikator, status) begitu database pengajuan sudah dibuat.</span>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.data-masuk') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;width:100%;">
        <div class="filter-field">
            <label for="fTanggal">Tanggal</label>
            <input type="date" name="tanggal" id="fTanggal" value="{{ request('tanggal') }}">
        </div>
        <div class="filter-field">
            <label for="fBulan">Bulan</label>
            <select name="bulan" id="fBulan">
                <option value="">Semua bulan</option>
                @foreach ([
                    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                ] as $num => $label)
                    <option value="{{ $num }}" {{ (string) request('bulan') === (string) $num ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="fTahun">Tahun</label>
            <select name="tahun" id="fTahun">
                <option value="">Semua tahun</option>
                @foreach (($daftarTahun ?? [date('Y')]) as $th)
                    <option value="{{ $th }}" {{ (string) request('tahun') === (string) $th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="fStatus">Status</label>
            <select name="status" id="fStatus">
                <option value="">Semua status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum diproses</option>
                <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Sudah diproses</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21L16.5 16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Terapkan
            </button>
            <a href="{{ route('admin.data-masuk') }}" class="btn-reset">Reset</a>
        </div>
    </form>
</div>

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Data Masuk</h2>
        <span class="count-pill">{{ count($dataMasuk ?? []) }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
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
                    @forelse (($dataMasuk ?? []) as $row)
                        <tr>
                            <td class="cell-strong">{{ $row['sekolah'] }}</td>
                            <td>{{ $row['layanan'] }}</td>
                            <td>{{ $row['verifikator'] ?? '—' }}</td>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>
                                @if ($row['status'] === 'processed')
                                    <span class="badge badge-processed">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12.5L9.5 17L19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Sudah diproses
                                    </span>
                                @elseif ($row['status'] === 'rejected')
                                    <span class="badge badge-rejected">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="badge badge-pending">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        Belum diproses
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="table-empty">Tidak ada data untuk filter yang dipilih.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
