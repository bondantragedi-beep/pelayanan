@extends('layouts.app') {{-- SESUAIKAN: nama layout admin Anda --}}

@section('content') {{-- SESUAIKAN: nama section di layout Anda --}}
<div style="max-width:900px;margin:auto;padding:16px">
    <h2>Popup Pengumuman Sekolah</h2>

    @if(session('success'))
        <div style="background:#d1fae5;padding:10px;border-radius:6px;margin-bottom:12px">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:10px;border-radius:6px;margin-bottom:12px">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data"
          style="border:1px solid #ddd;padding:16px;border-radius:8px;margin-bottom:24px">
        @csrf
        <h4>Upload Gambar Baru</h4>
        <input type="text" name="judul" placeholder="Judul (mis. Hari Budaya)" required style="width:100%;margin-bottom:8px">
        <input type="file" name="gambar" accept="image/*" required>
        <button type="submit">Upload</button>
        <small style="display:block;color:#666;margin-top:6px">JPG/PNG/WEBP, maksimal 3 MB.</small>
    </form>

    @forelse($popups as $p)
        <div style="border:2px solid {{ $p->aktif ? '#16a34a' : '#ddd' }};padding:12px;border-radius:8px;margin-bottom:16px;display:flex;gap:16px;flex-wrap:wrap">
            <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" style="width:180px;border-radius:6px;object-fit:cover">

            <div style="flex:1;min-width:240px">
                <strong>{{ $p->judul }}</strong>
                <span style="color:{{ $p->aktif ? '#16a34a' : '#999' }}">
                    ● {{ $p->aktif ? 'AKTIF (tampil ke sekolah)' : 'Nonaktif' }}
                </span>

                <form action="{{ route('admin.popup.update', $p) }}" method="POST" enctype="multipart/form-data" style="margin:8px 0">
                    @csrf @method('PUT')
                    <input type="text" name="judul" value="{{ $p->judul }}" required>
                    <input type="file" name="gambar" accept="image/*">
                    <button type="submit">Simpan / Ganti Gambar</button>
                </form>

                <form action="{{ route('admin.popup.toggle', $p) }}" method="POST" style="display:inline">
                    @csrf @method('PATCH')
                    <button type="submit">{{ $p->aktif ? 'Matikan' : 'Aktifkan' }}</button>
                </form>

                <form action="{{ route('admin.popup.destroy', $p) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('Hapus popup ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="color:#dc2626">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <p>Belum ada gambar popup.</p>
    @endforelse
</div>
@endsection
