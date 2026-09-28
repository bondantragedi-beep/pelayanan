@extends('layout.dashboard-admin')

@section('title', ($mode === 'create' ? 'Tambah' : 'Ubah') . ' Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', ($mode === 'create' ? 'Tambah Sekolah' : 'Ubah Sekolah'))
@section('active-menu', 'data-master-sekolah')
@section('breadcrumb')
    <a href="{{ route('admin.data-master.sekolah.index') }}" style="color:inherit;text-decoration:none;">Kelola Sekolah</a> / <span>{{ $mode === 'create' ? 'Tambah' : 'Ubah' }}</span>
@endsection

@section('content')

<div class="panel">
    <form method="POST" action="{{ $mode === 'create' ? route('admin.data-master.sekolah.store') : route('admin.data-master.sekolah.update', $sekolah) }}">
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif

        <div class="form-grid">
            <div class="form-field">
                <label>NPSN <span class="req">*</span></label>
                <input type="text" name="npsn" maxlength="20"
                       value="{{ old('npsn', $sekolah->npsn ?? '') }}"
                       {{ $mode === 'edit' ? 'readonly style=background:var(--blue-50)' : '' }} required>
                @error('npsn')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Nama Sekolah <span class="req">*</span></label>
                <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $sekolah->nama_sekolah ?? '') }}" required>
                @error('nama_sekolah')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Jenjang <span class="req">*</span></label>
                <select name="jenjang" required>
                    <option value="">— Pilih —</option>
                    @foreach (['TK','SD','SMP','SMA','SMK'] as $j)
                        <option value="{{ $j }}" {{ old('jenjang', $sekolah->jenjang ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                @error('jenjang')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Status <span class="req">*</span></label>
                <select name="status" required>
                    <option value="">— Pilih —</option>
                    @foreach (['Negeri','Swasta'] as $st)
                        <option value="{{ $st }}" {{ old('status', $sekolah->status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                @error('status')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Kelurahan</label>
                <input type="text" name="kelurahan" value="{{ old('kelurahan', $sekolah->kelurahan ?? '') }}">
                @error('kelurahan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Kecamatan <span class="req">*</span></label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan', $sekolah->kecamatan ?? '') }}" required>
                @error('kecamatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field full">
                <label>{{ $mode === 'create' ? 'Password Login' : 'Ganti Password (opsional)' }} @if($mode === 'create')<span class="req">*</span>@endif</label>
                <input type="password" name="password" placeholder="{{ $mode === 'edit' ? 'Kosongkan jika tidak ingin mengganti' : '' }}" {{ $mode === 'create' ? 'required' : '' }}>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Dipakai sekolah untuk login di halaman Operator Sekolah (NPSN + password ini).</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.data-master.sekolah.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

@endsection
