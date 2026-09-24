@extends('layout.dashboard-admin')

@section('title', 'Penugasan Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Penugasan Sekolah')
@section('active-menu', 'penugasan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Penugasan Sekolah</span>
@endsection

@section('content')

<div class="mock-note">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
    <span>Daftar sekolah di bawah masih data contoh. Nanti akan diambil dari data sekolah (NPSN, nama, jenjang, status, kelurahan, kecamatan) yang kamu unggah ke database.</span>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.penugasan') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;width:100%;">
        <div class="filter-field">
            <label for="fKecamatan">Kecamatan</label>
            <select name="kecamatan" id="fKecamatan">
                <option value="">Semua kecamatan</option>
                @foreach (($daftarKecamatan ?? []) as $kec)
                    <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="fStatusTugas">Status penugasan</label>
            <select name="status_tugas" id="fStatusTugas">
                <option value="">Semua</option>
                <option value="assigned" {{ request('status_tugas') === 'assigned' ? 'selected' : '' }}>Sudah ada verifikator</option>
                <option value="unassigned" {{ request('status_tugas') === 'unassigned' ? 'selected' : '' }}>Belum ada verifikator</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21L16.5 16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Terapkan
            </button>
            <a href="{{ route('admin.penugasan') }}" class="btn-reset">Reset</a>
        </div>
    </form>
</div>

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Daftar Sekolah</h2>
        <span class="count-pill">{{ count($daftarSekolah ?? []) }} sekolah</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Jenjang</th>
                        <th>Kecamatan</th>
                        <th>Verifikator Penanggung Jawab</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($daftarSekolah ?? []) as $sekolah)
                        <tr>
                            <td>{{ $sekolah['npsn'] }}</td>
                            <td>
                                <span class="cell-strong">{{ $sekolah['nama'] }}</span>
                                <span class="cell-sub">{{ $sekolah['status'] }}</span>
                            </td>
                            <td>{{ $sekolah['jenjang'] }}</td>
                            <td>{{ $sekolah['kecamatan'] }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.penugasan.update', $sekolah['npsn']) }}" class="row-actions">
                                    @csrf
                                    <select name="verifikator_id" class="assign-select {{ empty($sekolah['verifikator']) ? 'is-unassigned' : '' }}">
                                        <option value="">— Belum ditugaskan —</option>
                                        @foreach (($daftarVerifikator ?? []) as $v)
                                            <option value="{{ $v['id'] }}" {{ ($sekolah['verifikator'] ?? null) === $v['nama'] ? 'selected' : '' }}>
                                                {{ $v['nama'] }} ({{ $v['kecamatan'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-save-row">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="table-empty">Belum ada data sekolah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
