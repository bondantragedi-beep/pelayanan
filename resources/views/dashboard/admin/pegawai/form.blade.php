@extends('layout.dashboard-admin')

@section('title', ($mode === 'create' ? 'Tambah' : 'Ubah') . ' Pegawai | Dinas Pendidikan Kota Makassar')
@section('page-title', ($mode === 'create' ? 'Tambah Pegawai' : 'Ubah Pegawai'))
@section('active-menu', 'data-master-pegawai')
@section('breadcrumb')
    <a href="{{ route('admin.data-master.pegawai.index') }}" style="color:inherit;text-decoration:none;">Kelola Pegawai</a> / <span>{{ $mode === 'create' ? 'Tambah' : 'Ubah' }}</span>
@endsection

@section('content')

<div class="panel">
    <form method="POST" action="{{ $mode === 'create' ? route('admin.data-master.pegawai.store') : route('admin.data-master.pegawai.update', $pegawai) }}">
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif

        <div class="form-grid">
            <div class="form-field">
                <label>NIP <span class="req">*</span></label>
                <input type="text" name="nip" maxlength="18"
                       value="{{ old('nip', $pegawai->nip ?? '') }}"
                       {{ $mode === 'edit' ? 'readonly style=background:var(--blue-50)' : '' }} required>
                @error('nip')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Nama <span class="req">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $pegawai->nama ?? '') }}" required>
                @error('nama')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Status Kepegawaian <span class="req">*</span></label>
                <select name="status" required>
                    @foreach (['PNS','PPPK'] as $st)
                        <option value="{{ $st }}" {{ old('status', $pegawai->status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                @error('status')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Sekolah (NPSN) <span class="req">*</span></label>
                <select name="npsn" required>
                    <option value="">— Pilih Sekolah —</option>
                    @foreach ($daftarSekolah as $s)
                        <option value="{{ $s->npsn }}" {{ old('npsn', $pegawai->npsn ?? '') === $s->npsn ? 'selected' : '' }}>{{ $s->nama_sekolah }} ({{ $s->npsn }})</option>
                    @endforeach
                </select>
                @error('npsn')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Pangkat / Jabatan</label>
                <input type="text" name="pangkat_jabatan" value="{{ old('pangkat_jabatan', $pegawai->pangkat_jabatan ?? '') }}">
                @error('pangkat_jabatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Golongan</label>
                <select name="golongan">
                    <option value="">— Pilih —</option>
                    @foreach ($daftarGolongan as $g)
                        <option value="{{ $g }}" {{ old('golongan', $pegawai->golongan ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
                @error('golongan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan', $pegawai->kecamatan ?? '') }}">
                @error('kecamatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>TMT</label>
                <input type="date" name="tmt" value="{{ old('tmt', optional($pegawai->tmt ?? null)->toDateString()) }}">
                @error('tmt')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.data-master.pegawai.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

@endsection
