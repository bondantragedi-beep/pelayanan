@extends('layout.dashboard-admin')

@section('title', 'Popup Pengumuman | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Popup Pengumuman')
@section('active-menu', 'popup')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Popup Pengumuman</span>
@endsection

@section('content')

@if ($errors->any())
    <div class="dash-alert error">
        <span>@foreach ($errors->all() as $e){{ $e }} @endforeach</span>
    </div>
@endif

<div class="panel">
    <div class="panel-head"><h2>Upload Gambar Baru</h2></div>
    <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-field">
                <label>Judul <span class="req">*</span></label>
                <input type="text" name="judul" maxlength="255" placeholder="Contoh: Hari Budaya" value="{{ old('judul') }}" required>
            </div>
            <div class="form-field">
                <label>Gambar <span class="req">*</span></label>
                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" required>
                <span class="form-hint">JPG/PNG/WEBP, maksimal 3 MB.</span>
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin-top:12px;font-size:13px;font-weight:600;cursor:pointer;">
            <input type="checkbox" name="aktifkan" value="1" checked style="width:17px;height:17px;accent-color:var(--blue-700);">
            Langsung tampilkan ke sekolah (popup yang aktif sebelumnya dimatikan)
        </label>
        <div class="form-actions"><button type="submit" class="btn-primary">Upload</button></div>
    </form>
</div>

<div class="panel">
    <div class="panel-head"><h2>Daftar Popup</h2></div>
    <p style="font-size:12.5px;color:var(--muted);margin:0 0 14px;line-height:1.6;">
        Hanya satu popup yang aktif. Popup tampil ke operator sekolah <strong>sekali setiap login</strong>.
        Mengganti gambar atau judul membuatnya tampil lagi pada login berikutnya.
    </p>

    @forelse ($popups as $p)
        <div style="border:2px solid {{ $p->aktif ? '#16a34a' : 'var(--line)' }};padding:12px;border-radius:12px;margin-bottom:14px;display:flex;gap:16px;flex-wrap:wrap;">
            <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" style="width:180px;border-radius:8px;object-fit:cover;align-self:flex-start;">
            <div style="flex:1;min-width:240px;">
                <strong>{{ $p->judul }}</strong>
                <span style="margin-left:8px;font-size:12.5px;font-weight:700;color:{{ $p->aktif ? '#16a34a' : 'var(--muted)' }};">
                    ● {{ $p->aktif ? 'AKTIF (tampil ke sekolah)' : 'Nonaktif' }}
                </span>

                <form action="{{ route('admin.popup.update', $p) }}" method="POST" enctype="multipart/form-data" style="margin:10px 0;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    @csrf @method('PUT')
                    <input type="text" name="judul" value="{{ $p->judul }}" maxlength="255" required style="height:38px;border:1px solid var(--line-strong);border-radius:10px;padding:0 10px;">
                    <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
                    <button type="submit" class="btn-secondary">Simpan / Ganti Gambar</button>
                </form>

                <div style="display:flex;gap:8px;">
                    <form action="{{ route('admin.popup.toggle', $p) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary">{{ $p->aktif ? 'Matikan' : 'Aktifkan' }}</button>
                    </form>
                    <form action="{{ route('admin.popup.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus popup ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-secondary" style="color:#dc2626;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p style="color:var(--muted);">Belum ada gambar popup.</p>
    @endforelse
</div>

@endsection
