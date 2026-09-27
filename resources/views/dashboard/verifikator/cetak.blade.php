@extends('layout.dashboard-verifikator')

@section('title', 'Cetak & Kirim | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Cetak & Kirim ke Sekolah')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" style="color:inherit;text-decoration:none;">{{ $pengajuan->sekolah->nama_sekolah }}</a> / <span>Cetak &amp; Kirim</span>
@endsection

@section('content')

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9V3H18V9" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6 18H4C3.4 18 3 17.6 3 17V11C3 10.4 3.4 10 4 10H20C20.6 10 21 10.4 21 11V17C21 17.6 20.6 18 20 18H18" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><rect x="6" y="14" width="12" height="7" stroke="currentColor" stroke-width="1.6"/></svg>
        </div>
        <h2>Langkah 1 — Cetak Dokumen</h2>
    </div>
    <p style="font-size:13px;color:var(--muted);margin:0 0 16px;line-height:1.6;">
        Unduh draf Surat Keputusan KGB atas nama <strong>{{ $pengajuan->verifikasiKgb->nama_pegawai }}</strong> di bawah ini, cetak, lalu mintakan tanda tangan pimpinan.
    </p>
    <a href="{{ route('verifikator.berkas.cetak.pdf', $pengajuan) }}" target="_blank" class="btn-primary" style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 11.5L12 16L16.5 11.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Unduh / Cetak PDF
    </a>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h2>Langkah 2 — Unggah Hasil Tanda Tangan</h2>
    </div>
    <p style="font-size:13px;color:var(--muted);margin:0 0 16px;line-height:1.6;">
        Setelah ditandatangani pimpinan, scan dokumennya (format PDF) lalu unggah di sini. Begitu terkirim, dokumen langsung muncul di menu <strong>Inbox</strong> sekolah dan berkas ini akan hilang dari daftar yang menunggu TTD.
    </p>

    <form method="POST" action="{{ route('verifikator.berkas.kirim', $pengajuan) }}" enctype="multipart/form-data" data-upload-form>
        @csrf
        <div class="upload-field" style="max-width:420px;">
            <label class="upload-label">Hasil Scan (sudah TTD) <span class="required-mark">*</span></label>
            <div class="dropzone" data-dropzone>
                <input type="file" name="hasil_ttd" accept=".pdf" required>
                <span class="dz-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <p class="dz-text">Klik atau seret file ke sini</p>
                <p class="dz-hint">PDF — maks. 4 MB</p>
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
            @error('hasil_ttd')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary" data-submit-btn disabled>
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 12H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M11 6L17 12L11 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kirim ke Sekolah
            </button>
        </div>
    </form>
</div>

@endsection
