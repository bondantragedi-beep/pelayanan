@extends('layout.dashboard-admin')

@section('title', 'Kelola Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Kelola Sekolah')
@section('active-menu', 'data-master-sekolah')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Kelola Sekolah</span>
@endsection

@section('content')

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Daftar Sekolah</h2>
        <a href="{{ route('admin.data-master.sekolah.create') }}" class="btn-add">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah Sekolah
        </a>
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
                        <th>Verifikator</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarSekolah as $s)
                        <tr>
                            <td>{{ $s->npsn }}</td>
                            <td>
                                <span class="cell-strong">{{ $s->nama_sekolah }}</span>
                                <span class="cell-sub">{{ $s->status }}</span>
                            </td>
                            <td>{{ $s->jenjang }}</td>
                            <td>{{ $s->kecamatan }}</td>
                            <td>
                                @if ($s->verifikator)
                                    {{ $s->verifikator->nama }}
                                @else
                                    <span class="badge badge-unassigned">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.data-master.sekolah.edit', $s) }}" class="icon-btn" title="Ubah">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 20L4.8 16.6L15.6 5.8C16.2 5.2 17.2 5.2 17.8 5.8L18.2 6.2C18.8 6.8 18.8 7.8 18.2 8.4L7.4 19.2L4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.data-master.sekolah.destroy', $s) }}" onsubmit="return confirm('Hapus {{ $s->nama_sekolah }}? Data pegawai & pengajuan sekolah ini akan ikut terhapus.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-btn danger" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7H20M9 7V4.5C9 4 9.4 3.5 10 3.5H14C14.6 3.5 15 4 15 4.5V7M18 7L17.3 19.5C17.3 20.3 16.6 21 15.8 21H8.2C7.4 21 6.7 20.3 6.7 19.5L6 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div>
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
