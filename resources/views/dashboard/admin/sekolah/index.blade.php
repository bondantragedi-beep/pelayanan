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

    <div style="padding:14px 22px 0;">
        <input type="search" id="cariSekolah" placeholder="Cari NPSN, nama sekolah, kecamatan, atau jenjang..." autocomplete="off"
               style="width:100%;max-width:420px;height:40px;padding:0 13px;border:1px solid var(--line-strong);border-radius:10px;font:inherit;font-size:13.5px;">
        <span id="jumlahSekolah" style="margin-left:10px;font-size:12.5px;color:var(--muted);">{{ $daftarSekolah->count() }} sekolah</span>
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
                        <tr data-cari="{{ strtolower($s->npsn . ' ' . $s->nama_sekolah . ' ' . $s->kecamatan . ' ' . $s->jenjang) }}">
                            <td>{{ $s->npsn }}</td>
                            <td>
                                <span class="cell-strong">{{ $s->nama_sekolah }}</span>
                                <span class="cell-sub">{{ $s->status }}</span>
                                @if ($s->wajib_ganti_password)
                                    <span class="badge badge-unassigned" title="Sekolah ini belum mengganti password awal">Belum ganti password</span>
                                @endif
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
                                    <form method="POST" action="{{ route('admin.data-master.sekolah.reset-password', $s) }}" onsubmit="return confirm({{ \Illuminate\Support\Js::from('Reset password ' . $s->nama_sekolah . ' ke ' . \App\Models\Sekolah::PASSWORD_AWAL . '? Sekolah wajib menggantinya saat login berikutnya.') }})">
                                        @csrf
                                        <button type="submit" class="icon-btn" title="Reset password ke {{ \App\Models\Sekolah::PASSWORD_AWAL }}">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="15" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M10.5 12.5L19 4M16 7L18.5 9.5M13.5 9.5L15.5 11.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.data-master.sekolah.destroy', $s) }}" onsubmit="return confirm('Hapus {{ $s->nama_sekolah }}? Pengajuan milik sekolah ini akan ikut terhapus. Data pegawai tetap tersimpan tetapi jadi belum terhubung ke sekolah.');">
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

@push('scripts')
<script>
    // Pencarian langsung di halaman (tanpa muat ulang), berguna untuk ratusan sekolah.
    (function () {
        var kotak = document.getElementById('cariSekolah');
        var info  = document.getElementById('jumlahSekolah');
        var baris = document.querySelectorAll('tbody tr[data-cari]');
        kotak.addEventListener('input', function () {
            var q = kotak.value.trim().toLowerCase(), n = 0;
            baris.forEach(function (tr) {
                var cocok = q === '' || tr.dataset.cari.indexOf(q) !== -1;
                tr.style.display = cocok ? '' : 'none';
                if (cocok) { n++; }
            });
            info.textContent = n + ' sekolah';
        });
    })();
</script>
@endpush
