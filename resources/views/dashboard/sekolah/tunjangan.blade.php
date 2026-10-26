@extends('layout.dashboard')

@section('title', 'Tunjangan | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Tunjangan')
@section('active-menu', 'tunjangan')
@section('breadcrumb')
    <a href="{{ route('sekolah.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Tunjangan</span>
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
        <li><span class="num">1</span> Pilih dulu status pegawai (PNS atau PPPK) dan jenis tunjangan yang diajukan.</li>
        <li><span class="num">2</span> Berkas wajib: KP4, Kartu Keluarga (KK), SK Terakhir, dan Buku Nikah.</li>
        <li><span class="num">3</span> Khusus <strong>tunjangan anak</strong>, wajib melampirkan Akta Kelahiran Anak. Untuk tunjangan istri/suami tidak perlu.</li>
    </ul>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="17" cy="9" r="2.4" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
        <h2>Jenis Pengajuan</h2>
    </div>

    <style>
        .pilih-grup{display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;}
        .pilih-grup label{display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--line);border-radius:12px;cursor:pointer;font-size:13.5px;font-weight:600;background:#fff;}
        .pilih-grup label:has(input:checked){border-color:var(--blue-700);background:var(--blue-50);color:var(--blue-800);}
        .pilih-judul{font-size:12.5px;font-weight:700;color:var(--muted);margin:14px 0 0;}
        .pilih-judul:first-of-type{margin-top:0;}
    </style>

    <p class="pilih-judul">Pegawai yang mengajukan <span class="required-mark">*</span></p>
    <div class="pilih-grup">
        <label><input type="radio" name="jenis_pegawai" value="PNS" form="formTunjangan" {{ old('jenis_pegawai') === 'PNS' ? 'checked' : '' }} required> PNS</label>
        <label><input type="radio" name="jenis_pegawai" value="PPPK" form="formTunjangan" {{ old('jenis_pegawai') === 'PPPK' ? 'checked' : '' }}> PPPK</label>
    </div>
    @error('jenis_pegawai')<p class="field-error">{{ $message }}</p>@enderror

    <p class="pilih-judul">Tunjangan yang diajukan <span class="required-mark">*</span></p>
    <div class="pilih-grup">
        <label><input type="radio" name="jenis_tunjangan" value="istri_suami" form="formTunjangan" {{ old('jenis_tunjangan') === 'istri_suami' ? 'checked' : '' }} required> Tunjangan Istri / Suami</label>
        <label><input type="radio" name="jenis_tunjangan" value="anak" form="formTunjangan" {{ old('jenis_tunjangan') === 'anak' ? 'checked' : '' }}> Tunjangan Anak</label>
    </div>
    @error('jenis_tunjangan')<p class="field-error">{{ $message }}</p>@enderror
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3V8H19" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
        </div>
        <h2>Persyaratan Berkas</h2>
    </div>

    <form id="formTunjangan" action="{{ route('sekolah.tunjangan.store') }}" method="POST" enctype="multipart/form-data" data-upload-form>
        @csrf

        <div class="upload-grid">

            {{-- KP4 --}}
            <div class="upload-field">
                <label class="upload-label">KP4 <span class="required-mark">*</span></label>

                <div class="dropzone @error('kp4') is-dragover @enderror" data-dropzone>
                    <input type="file" name="kp4" id="fKp4" accept=".pdf,.jpg,.jpeg,.png" required>
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

                @error('kp4')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- Akta Kelahiran Anak (hanya jika tunjangan anak) --}}
            <div class="upload-field" id="aktaField" style="display:none;">
                <label class="upload-label">Akta Kelahiran Anak <span class="required-mark" data-akta-mark style="display:none;">*</span></label>

                <div class="dropzone @error('akta_kelahiran_anak') is-dragover @enderror" data-dropzone>
                    <input type="file" name="akta_kelahiran_anak" id="fAkta" accept=".pdf,.jpg,.jpeg,.png">
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

                @error('akta_kelahiran_anak')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- KK --}}
            <div class="upload-field">
                <label class="upload-label">Kartu Keluarga (KK) <span class="required-mark">*</span></label>

                <div class="dropzone @error('kk') is-dragover @enderror" data-dropzone>
                    <input type="file" name="kk" id="fKk" accept=".pdf,.jpg,.jpeg,.png" required>
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

                @error('kk')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- SK Terakhir --}}
            <div class="upload-field">
                <label class="upload-label">SK Terakhir <span class="required-mark">*</span></label>

                <div class="dropzone @error('sk_terakhir') is-dragover @enderror" data-dropzone>
                    <input type="file" name="sk_terakhir" id="fSk" accept=".pdf,.jpg,.jpeg,.png" required>
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

                @error('sk_terakhir')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- Buku Nikah --}}
            <div class="upload-field">
                <label class="upload-label">Buku Nikah <span class="required-mark">*</span></label>

                <div class="dropzone @error('buku_nikah') is-dragover @enderror" data-dropzone>
                    <input type="file" name="buku_nikah" id="fNikah" accept=".pdf,.jpg,.jpeg,.png" required>
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

                @error('buku_nikah')
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
                Ajukan Berkas Tunjangan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form     = document.getElementById('formTunjangan');
    var akta     = document.getElementById('aktaField');
    var aktaFile = document.getElementById('fAkta');
    var mark     = akta.querySelector('[data-akta-mark]');
    var submit   = form.querySelector('[data-submit-btn]');

    function terpilih(nama) { return document.querySelector('input[name="' + nama + '"]:checked'); }

    // Tombol kirim aktif hanya jika PNS/PPPK + jenis tunjangan sudah dipilih
    // dan semua berkas wajib sudah terisi. (Pengecekan sebenarnya tetap di server.)
    function siap() {
        var ok = !!terpilih('jenis_pegawai') && !!terpilih('jenis_tunjangan');
        form.querySelectorAll('input[type=file][required]').forEach(function (f) {
            if (!f.files || !f.files[0]) ok = false;
        });
        submit.disabled = !ok;
    }

    function aturAkta() {
        var j = terpilih('jenis_tunjangan');
        var tampil = !!j && j.value === 'anak';
        akta.style.display = tampil ? '' : 'none';
        mark.style.display = tampil ? '' : 'none';
        aktaFile.required = tampil;
        if (!tampil && aktaFile.files && aktaFile.files.length) {
            aktaFile.value = '';
            var chip = akta.querySelector('[data-file-chip]');
            var zone = akta.querySelector('[data-dropzone]');
            if (chip) chip.classList.remove('show');
            if (zone) zone.classList.remove('has-file');
        }
    }

    // Listener di form berjalan setelah listener tiap input (fase bubble),
    // jadi hasil akhir tombol kirim ditentukan oleh fungsi siap() di atas.
    ['change', 'drop', 'click'].forEach(function (evt) {
        form.addEventListener(evt, function () { setTimeout(siap, 0); });
    });
    document.querySelectorAll('input[name="jenis_tunjangan"], input[name="jenis_pegawai"]').forEach(function (r) {
        r.addEventListener('change', function () { aturAkta(); siap(); });
    });

    aturAkta();
    siap();
});
</script>

@endsection
