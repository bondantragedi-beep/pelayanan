@extends('layout.dashboard-admin')

@section('title', ($mode === 'create' ? 'Tambah' : 'Ubah') . ' Verifikator | Dinas Pendidikan Kota Makassar')
@section('page-title', ($mode === 'create' ? 'Tambah Verifikator' : 'Ubah Verifikator'))
@section('active-menu', 'data-master-verifikator')
@section('breadcrumb')
    <a href="{{ route('admin.data-master.verifikator.index') }}" style="color:inherit;text-decoration:none;">Kelola Verifikator</a> / <span>{{ $mode === 'create' ? 'Tambah' : 'Ubah' }}</span>
@endsection

@section('content')

<div class="panel">
    <form method="POST" action="{{ $mode === 'create' ? route('admin.data-master.verifikator.store') : route('admin.data-master.verifikator.update', $verifikator) }}">
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif

        <div class="form-grid">
            <div class="form-field">
                <label>NIP <span class="req">*</span></label>
                <input type="text" name="nip" maxlength="18"
                       value="{{ old('nip', $verifikator->nip ?? '') }}"
                       {{ $mode === 'edit' ? 'readonly style=background:var(--blue-50)' : '' }} required>
                @error('nip')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Nama <span class="req">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $verifikator->nama ?? '') }}" required>
                @error('nama')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan', $verifikator->kecamatan ?? '') }}" placeholder="Untuk keterangan wilayah (opsional)">
                @error('kecamatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Peran</label>
                <div class="checkbox-row">
                    <input type="checkbox" name="is_admin" id="isAdmin" value="1" {{ old('is_admin', $verifikator->is_admin ?? false) ? 'checked' : '' }}>
                    <label for="isAdmin" style="font-weight:600;font-size:13px;">Jadikan sebagai Admin</label>
                </div>
                <span class="form-hint">Admin login pakai NIP yang sama, tapi masuk ke Dashboard Admin, bukan dashboard Verifikator biasa.</span>
            </div>

            <div class="form-field full">
                <label>{{ $mode === 'create' ? 'Password Login' : 'Ganti Password (opsional)' }} @if($mode === 'create')<span class="req">*</span>@endif</label>
                <input type="password" name="password" placeholder="{{ $mode === 'edit' ? 'Kosongkan jika tidak ingin mengganti' : '' }}" {{ $mode === 'create' ? 'required' : '' }}>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.data-master.verifikator.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

@endsection
