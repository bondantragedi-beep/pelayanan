@extends('layout.dashboard-admin')

@section('title', 'Kelola Pegawai | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Kelola Pegawai')
@section('active-menu', 'data-master-pegawai')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Kelola Pegawai</span>
@endsection

@section('content')

@if ($totalBelumTerhubung > 0)
    <div class="mock-note">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        <span>
            <strong>{{ $totalBelumTerhubung }} pegawai belum terhubung ke sekolah.</strong>
            Pegawai tanpa sekolah tidak akan muncul di pencarian NIP milik verifikator. Pilih sekolahnya lewat tombol Ubah.
            <a href="{{ route('admin.data-master.pegawai.index', ['belum_terhubung' => 1]) }}" style="color:inherit;font-weight:700;">Tampilkan yang belum terhubung →</a>
        </span>
    </div>
@endif

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.data-master.pegawai.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;width:100%;">
        <div class="filter-field" style="flex:1 1 260px;">
            <label for="fCari">Cari</label>
            <input type="text" name="q" id="fCari" value="{{ $q }}" placeholder="NIP, nama, atau unit kerja..." style="width:100%;height:38px;padding:0 12px;border-radius:10px;border:1.5px solid var(--line);font-size:13px;">
        </div>
        <div class="filter-field">
            <label style="display:flex;align-items:center;gap:8px;height:38px;text-transform:none;font-size:12.5px;">
                <input type="checkbox" name="belum_terhubung" value="1" {{ request()->boolean('belum_terhubung') ? 'checked' : '' }}>
                Hanya yang belum terhubung ke sekolah
            </label>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">Terapkan</button>
            <a href="{{ route('admin.data-master.pegawai.index') }}" class="btn-reset">Reset</a>
        </div>
    </form>
</div>

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Daftar Pegawai <span class="count-pill" style="margin-left:8px;">{{ $daftarPegawai->total() }} orang</span></h2>
        <a href="{{ route('admin.data-master.pegawai.create') }}" class="btn-add">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah Pegawai
        </a>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Unit Kerja</th>
                        <th>Golongan / Pangkat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPegawai as $p)
                        <tr>
                            <td>{{ $p->nip }}</td>
                            <td>
                                <span class="cell-strong">{{ $p->nama }}</span>
                                <span class="cell-sub">{{ $p->nama_jabatan }}</span>
                            </td>
                            <td>{{ $p->status_pegawai ?? '—' }}</td>
                            <td>
                                {{ $p->unit_kerja ?: ($p->sekolah->nama_sekolah ?? '—') }}
                                @if (! $p->npsn)
                                    <span class="cell-sub"><span class="badge badge-unassigned">Belum terhubung ke sekolah</span></span>
                                @endif
                            </td>
                            <td>{{ $p->golongan_pangkat ?? '—' }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.data-master.pegawai.edit', $p) }}" class="icon-btn" title="Ubah">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 20L4.8 16.6L15.6 5.8C16.2 5.2 17.2 5.2 17.8 5.8L18.2 6.2C18.8 6.8 18.8 7.8 18.2 8.4L7.4 19.2L4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.data-master.pegawai.destroy', $p) }}" onsubmit="return confirm('Hapus data pegawai {{ $p->nama }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-btn danger" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7H20M9 7V4.5C9 4 9.4 3.5 10 3.5H14C14.6 3.5 15 4 15 4.5V7M18 7L17.3 19.5C17.3 20.3 16.6 21 15.8 21H8.2C7.4 21 6.7 20.3 6.7 19.5L6 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="table-empty">Tidak ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($daftarPegawai->lastPage() > 1)
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 22px;border-top:1px solid var(--line);">
            <span style="font-size:12.5px;color:var(--muted);">Halaman {{ $daftarPegawai->currentPage() }} dari {{ $daftarPegawai->lastPage() }}</span>
            <div style="display:flex;gap:8px;">
                @if ($daftarPegawai->previousPageUrl())
                    <a href="{{ $daftarPegawai->previousPageUrl() }}" class="btn-reset">← Sebelumnya</a>
                @endif
                @if ($daftarPegawai->nextPageUrl())
                    <a href="{{ $daftarPegawai->nextPageUrl() }}" class="btn-reset">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection
