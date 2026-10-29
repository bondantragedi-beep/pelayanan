@extends('layout.dashboard')

@section('title', 'Ubah Password | Dinas Pendidikan Kota Makassar')
@section('page-title', $wajib ? 'Buat Password Baru' : 'Ubah Password')
@section('active-menu', 'password')
@section('error-prefix', 'Password belum berhasil diubah:')
@section('breadcrumb')
    @if ($wajib)
        <span>Ganti password awal</span>
    @else
        <a href="{{ route('sekolah.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Ubah Password</span>
    @endif
@endsection

@section('content')

@if ($wajib)
    <div class="dash-alert error" style="margin-bottom:18px;">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        <span><strong>Demi keamanan, ganti password awal Anda terlebih dahulu.</strong> Menu lain baru bisa dibuka setelah password diganti. Password awal dipakai bersama oleh banyak sekolah, jadi tidak aman dipakai terus.</span>
    </div>
@endif

<div class="panel" style="max-width:560px;">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10.5V7.8C8 5.7 9.8 4 12 4C14.2 4 16 5.7 16 7.8V10.5" stroke="currentColor" stroke-width="1.6"/></svg>
        </div>
        <h2>{{ $wajib ? 'Password Baru' : 'Ubah Password' }}</h2>
    </div>

    <form method="POST" action="{{ route('sekolah.password.update') }}" autocomplete="off" style="display:grid;gap:16px;">
        @csrf
        @method('PUT')

        <div class="form-field">
            <label for="password_lama">{{ $wajib ? 'Password awal (yang diberikan Dinas)' : 'Password lama' }} <span class="req">*</span></label>
            <input type="password" id="password_lama" name="password_lama" required autocomplete="current-password">
            @error('password_lama')<p class="field-error" style="margin:4px 0 0;font-size:12.5px;color:var(--danger);">{{ $message }}</p>@enderror
        </div>

        <div class="form-field">
            <label for="password">Password baru <span class="req">*</span></label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <span class="form-hint" style="font-size:12px;color:var(--muted);">Minimal 8 karakter, kombinasi huruf dan angka. Jangan pakai 123456 atau NPSN.</span>
            @error('password')<p class="field-error" style="margin:4px 0 0;font-size:12.5px;color:var(--danger);">{{ $message }}</p>@enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation">Ulangi password baru <span class="req">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-2);cursor:pointer;">
            <input type="checkbox" id="tampilkanPassword"> Tampilkan password
        </label>

        <div class="form-actions" style="margin-top:4px;">
            @unless ($wajib)
                <a href="{{ route('sekolah.dashboard') }}" class="btn-secondary">Batal</a>
            @endunless
            <button type="submit" class="btn-primary">Simpan Password Baru</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('tampilkanPassword').addEventListener('change', function () {
        ['password_lama', 'password', 'password_confirmation'].forEach(function (id) {
            document.getElementById(id).type = this.checked ? 'text' : 'password';
        }, this);
    });
</script>
@endpush
