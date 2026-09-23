@extends('layout.dashboard')

@section('title', 'Kenaikan Gaji Berkala (KGB) | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Kenaikan Gaji Berkala (KGB)')
@section('active-menu', 'kgb')
@section('breadcrumb')
    <a href="{{ route('sekolah.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Kenaikan Gaji Berkala (KGB)</span>
@endsection

@section('content')

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        </div>
        <h2>Persyaratan Umum</h2>
    </div>
    <ul class="req-list">
        <li><span class="num">1</span> Telah memenuhi masa kerja 2 (dua) tahun dari KGB terakhir.</li>
        <li><span class="num">2</span> Diharapkan untuk menyetor kelengkapan berkas 1 (satu) bulan sebelum TMT KGB.</li>
    </ul>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3V8H19" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
        </div>
        <h2>Persyaratan Berkas</h2>
    </div>

    <form action="{{ route('sekolah.kgb.store') }}" method="POST" enctype="multipart/form-data" data-upload-form>
        @csrf

        <div class="upload-grid">

            {{-- SK Pangkat Terakhir --}}
            <div class="upload-field">
                <label class="upload-label">SK Pangkat Terakhir <span class="required-mark">*</span></label>

                <div class="dropzone @error('sk_pangkat_terakhir') is-dragover @enderror" data-dropzone>
                    <input type="file" name="sk_pangkat_terakhir" id="skPangkat" accept=".pdf,.jpg,.jpeg,.png" required>
                    <span class="dz-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <p class="dz-text">Klik atau seret file ke sini</p>
                    <p class="dz-hint">PDF, JPG, atau PNG — maks. 2 MB</p>
                </div>

                <div class="file-chip" data-file-chip>
                    <span class="fc-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="fc-name" data-file-name></span>
                    <button type="button" class="fc-remove" data-file-remove aria-label="Hapus file">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 5L19 19M19 5L5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                @error('sk_pangkat_terakhir')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- SK KGB Terakhir --}}
            <div class="upload-field">
                <label class="upload-label">SK KGB Terakhir <span class="required-mark">*</span></label>

                <div class="dropzone @error('sk_kgb_terakhir') is-dragover @enderror" data-dropzone>
                    <input type="file" name="sk_kgb_terakhir" id="skKgb" accept=".pdf,.jpg,.jpeg,.png" required>
                    <span class="dz-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <p class="dz-text">Klik atau seret file ke sini</p>
                    <p class="dz-hint">PDF, JPG, atau PNG — maks. 2 MB</p>
                </div>

                <div class="file-chip" data-file-chip>
                    <span class="fc-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="fc-name" data-file-name></span>
                    <button type="button" class="fc-remove" data-file-remove aria-label="Hapus file">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 5L19 19M19 5L5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                @error('sk_kgb_terakhir')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <p class="req-note">
            Pastikan seluruh berkas terbaca jelas (tidak buram/terpotong) sebelum diunggah. Berkas yang sudah dikirim akan diverifikasi oleh Verifikator Dinas Pendidikan.
        </p>

        <div class="form-actions">
            <a href="{{ route('sekolah.dashboard') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" data-submit-btn disabled>
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Ajukan Berkas KGB
            </button>
        </div>
    </form>
</div>

@endsection
