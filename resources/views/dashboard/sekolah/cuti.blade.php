@extends('layout.dashboard')

@section('title', 'Cuti | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Cuti')
@section('active-menu', 'cuti')
@section('breadcrumb')
    <a href="{{ route('sekolah.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Cuti</span>
@endsection

@push('styles')
<style>
    .pilih-grup{display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;}
    .pilih-grup label{display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--line);border-radius:12px;cursor:pointer;font-size:13.5px;font-weight:600;background:#fff;}
    .pilih-grup label:has(input:checked){border-color:var(--blue-700);background:var(--blue-50);color:var(--blue-800);}
    .pilih-grup label.is-disabled{opacity:.5;cursor:not-allowed;}
    .pilih-judul{font-size:12.5px;font-weight:700;color:var(--muted);margin:16px 0 0;}
    .pilih-judul:first-of-type{margin-top:0;}
    .ibadah-box{display:none;margin-top:12px;padding:12px 14px;border:1px dashed var(--line-strong);border-radius:12px;background:var(--blue-50);}
    .ibadah-box label{display:flex;gap:10px;align-items:flex-start;font-size:13px;font-weight:600;cursor:pointer;}
    .ibadah-box input{margin-top:3px;accent-color:var(--blue-700);width:17px;height:17px;}
    .ibadah-box small{display:block;font-weight:400;color:var(--muted);margin-top:2px;}
    .selama-box{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:14px;padding:12px 14px;border-radius:12px;background:var(--blue-50);border:1px solid var(--line);font-size:13px;}
    .selama-box strong{font-size:15px;color:var(--blue-800);}
    .selama-box.is-bad{background:var(--danger-bg);border-color:var(--danger-line);color:var(--red-600);}
    .selama-box.is-bad strong{color:var(--red-600);}
    .form-field input[readonly]{background:var(--bg);color:var(--blue-800);font-weight:600;}
</style>
@endpush

@section('content')

@php
    $perluTambahan = [
        'ibadah'         => \App\Support\LamaCuti::berkasTambahan('tahunan', true),
        'melahirkan'     => \App\Support\LamaCuti::berkasTambahan('melahirkan'),
        'sakit'          => \App\Support\LamaCuti::berkasTambahan('sakit'),
        'alasan_penting' => \App\Support\LamaCuti::berkasTambahan('alasan_penting'),
    ];

    $uploadUtama = [
        ['name' => 'formulir_cuti',       'label' => 'Formulir Permohonan Cuti'],
        ['name' => 'sk_pangkat_terakhir', 'label' => 'SK Pangkat Terakhir'],
        ['name' => 'rekap_absen',         'label' => 'Rekap Absen 2 Tahun Terakhir'],
    ];
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        </div>
        <h2>Persyaratan Umum</h2>
    </div>
    <ul class="req-list">
        <li><span class="num">1</span> Pilih pegawai, centang jenis cuti, lalu isi alasan, tanggal, dan alamat selama cuti. Data pegawai (jabatan, pangkat, masa kerja) diambil otomatis dari database.</li>
        <li><span class="num">2</span> Berkas wajib untuk semua jenis cuti: <strong>Formulir Permohonan Cuti</strong>, <strong>SK Pangkat Terakhir</strong>, dan <strong>Rekap Absen 2 Tahun Terakhir</strong>.</li>
        <li><span class="num">3</span> Berkas tambahan menyesuaikan jenis cuti: Cuti Umroh/Haji (Surat Bukti Umroh/Haji), Cuti Melahirkan (Surat Keterangan RS Bersalin), Cuti Sakit (Surat Keterangan Dokter), Cuti Alasan Penting (surat keterangan pendukung).</li>
        <li><span class="num">4</span> Cuti Tahunan paling lama <strong>{{ config('cuti.maks_tahunan') }} hari kerja</strong> (Sabtu, Minggu, dan hari libur tidak dihitung). Khusus cuti karena ibadah (umroh/haji) boleh lebih. Cuti Melahirkan <strong>{{ config('cuti.lama_melahirkan_bulan') }} bulan</strong>.</li>
    </ul>
</div>

<form id="formCuti" action="{{ route('sekolah.cuti.store') }}" method="POST" enctype="multipart/form-data" data-upload-form>
    @csrf

    {{-- ===================== DATA PEGAWAI & CUTI ===================== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h2>Data Cuti</h2>
        </div>

        <div class="form-grid">
            <div class="form-field full">
                <label>Pegawai yang Mengajukan Cuti <span class="req">*</span></label>
                <input type="text" id="cariPegawai" list="daftarPegawai" placeholder="Ketik nama atau NIP pegawai..." autocomplete="off">
                <datalist id="daftarPegawai">
                    @foreach ($daftarPegawai as $p)
                        <option value="{{ $p['nip'] }} — {{ $p['nama'] }}"></option>
                    @endforeach
                </datalist>
                <input type="hidden" name="nip_pegawai" id="nipPegawai" value="{{ old('nip_pegawai') }}">
                <span class="form-hint" id="infoPegawai">
                    @if ($daftarPegawai->isEmpty())
                        Belum ada data pegawai untuk sekolah ini. Hubungi admin Dinas untuk mengisi data pegawai.
                    @else
                        Hanya pegawai dari sekolah Anda. Pilih dari daftar yang muncul.
                    @endif
                </span>
                @error('nip_pegawai')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <p class="pilih-judul" style="margin-top:20px;">Jenis cuti yang diambil <span class="required-mark">*</span> <span style="font-weight:500;">(centang salah satu)</span></p>
        <div class="pilih-grup">
            @foreach (\App\Support\LamaCuti::JENIS as $kode => $label)
                @php $bisa = in_array($kode, \App\Support\LamaCuti::JENIS_SEKOLAH, true); @endphp
                <label class="{{ $bisa ? '' : 'is-disabled' }}" @unless($bisa) title="Cuti di luar tanggungan negara membutuhkan persetujuan Kepala BKN dan diproses terpisah oleh Dinas." @endunless>
                    <input type="radio" name="jenis_cuti" value="{{ $kode }}" {{ old('jenis_cuti') === $kode ? 'checked' : '' }} {{ $bisa ? '' : 'disabled' }}>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @error('jenis_cuti')<p class="field-error">{{ $message }}</p>@enderror

        <div class="ibadah-box" id="boxIbadah">
            <label>
                <input type="checkbox" name="cuti_ibadah" value="1" id="cutiIbadah" {{ old('cuti_ibadah') ? 'checked' : '' }}>
                <span>Cuti karena ibadah (umroh / haji)
                    <small>Centang bila cuti tahunan ini untuk ibadah. Lama cuti boleh lebih dari {{ config('cuti.maks_tahunan') }} hari kerja dan wajib melampirkan bukti keberangkatan.</small>
                </span>
            </label>
        </div>

        <div class="form-grid" style="margin-top:20px;">
            <div class="form-field full">
                <label>Alasan Cuti <span class="req">*</span></label>
                <textarea name="alasan" maxlength="500" placeholder="Contoh: Alasan keluarga / Menunaikan ibadah umroh / Persalinan anak ke-2" required>{{ old('alasan') }}</textarea>
                @error('alasan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Mulai Tanggal <span class="req">*</span></label>
                <input type="date" name="tanggal_mulai" id="tglMulai" value="{{ old('tanggal_mulai') }}" required>
                @error('tanggal_mulai')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Sampai Tanggal <span class="req">*</span></label>
                <input type="date" name="tanggal_selesai" id="tglSelesai" value="{{ old('tanggal_selesai') }}" required>
                @error('tanggal_selesai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint" id="hintSelesai"></span>
            </div>
        </div>

        <div class="selama-box" id="selamaBox">
            <span>Lamanya cuti (selama):</span>
            <strong id="selamaTeks">—</strong>
            <span id="selamaKet" style="color:var(--muted);"></span>
        </div>

        <div class="form-grid" style="margin-top:20px;">
            <div class="form-field full">
                <label>Alamat Selama Menjalankan Cuti <span class="req">*</span></label>
                <textarea name="alamat_cuti" maxlength="500" placeholder="Contoh: Jl. Hati Rela No. 50 Makassar" required>{{ old('alamat_cuti') }}</textarea>
                @error('alamat_cuti')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>No. Telepon Pegawai</label>
                <input type="text" name="telp" id="telp" maxlength="30" value="{{ old('telp') }}" placeholder="Terisi otomatis dari data pegawai bila ada">
                @error('telp')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Nomor yang bisa dihubungi selama pegawai menjalankan cuti.</span>
            </div>
        </div>
    </div>

    {{-- ===================== BERKAS ===================== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3V8H19" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
            </div>
            <h2>Persyaratan Berkas</h2>
        </div>

        <div class="upload-grid">

            @foreach ($uploadUtama as $f)
                <div class="upload-field">
                    <label class="upload-label">{{ $f['label'] }} <span class="required-mark">*</span></label>
                    <div class="dropzone @error($f['name']) is-dragover @enderror" data-dropzone>
                        <input type="file" name="{{ $f['name'] }}" accept=".pdf,.jpg,.jpeg,.png" required>
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
                    @error($f['name'])<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach

            {{-- Berkas tambahan: hanya muncul bila jenis cuti memerlukannya --}}
            <div class="upload-field" id="tambahanField" style="display:none;">
                <label class="upload-label"><span id="tambahanLabel">Berkas Tambahan</span> <span class="required-mark">*</span></label>
                <div class="dropzone @error('berkas_tambahan') is-dragover @enderror" data-dropzone>
                    <input type="file" name="berkas_tambahan" id="fTambahan" accept=".pdf,.jpg,.jpeg,.png">
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
                @error('berkas_tambahan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

        </div>

        <p class="req-note">
            Pastikan seluruh berkas terbaca jelas (tidak buram/terpotong) sebelum diunggah. Formulir Permohonan Cuti sebaiknya sudah ditandatangani pegawai dan disetujui Kepala Sekolah — dokumen ini yang diperiksa verifikator sebagai pertimbangan atasan langsung.
        </p>

        <div class="form-actions">
            <a href="{{ route('sekolah.dashboard') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" data-submit-btn disabled>
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 8.5L12 4L16.5 8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1 4.9 20 6 20H18C19.1 20 20 19.1 20 18V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Ajukan Berkas Cuti
            </button>
        </div>
    </div>
</form>

<script src="{{ asset('js/cuti-lama.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var pegawai   = @json($daftarPegawai);
    var libur     = @json((object) config('cuti.hari_libur'));
    var maksBulan = @json((object) config('cuti.maks_bulan'));
    var MAKS_THN  = {{ (int) config('cuti.maks_tahunan') }};
    var LAMA_LHR  = {{ (int) config('cuti.lama_melahirkan_bulan') }};
    var tambahan  = @json($perluTambahan);

    var form    = document.getElementById('formCuti');
    var submit  = form.querySelector('[data-submit-btn]');
    var el      = function (id) { return document.getElementById(id); };
    var terpilih = function (n) { return form.querySelector('input[name="' + n + '"]:checked'); };

    /* ---------- tanggal (perhitungan bersama: public/js/cuti-lama.js) ---------- */
    var parse = CutiLama.parse, fmt = CutiLama.fmt, addDays = CutiLama.addDays, addMonths = CutiLama.addMonths;

    /* ---------- pegawai ---------- */
    function labelPegawai(p) { return p.nip + ' — ' + p.nama; }
    function pilihPegawai() {
        var nilai = el('cariPegawai').value.trim();
        var p = pegawai.find(function (x) { return labelPegawai(x) === nilai || x.nip === nilai; });
        el('nipPegawai').value = p ? p.nip : '';
        el('infoPegawai').textContent = p
            ? (p.jabatan ? 'Jabatan: ' + p.jabatan : 'Pegawai terpilih.')
            : 'Hanya pegawai dari sekolah Anda. Pilih dari daftar yang muncul.';
        if (p && p.telp && !el('telp').dataset.manual && !el('telp').value) el('telp').value = p.telp;
        siap();
    }
    el('telp').addEventListener('input', function () { this.dataset.manual = '1'; });
    el('cariPegawai').addEventListener('input', pilihPegawai);
    (function () {   // pulihkan pilihan setelah validasi gagal
        var nip = el('nipPegawai').value;
        var p = pegawai.find(function (x) { return x.nip === nip; });
        if (p) el('cariPegawai').value = labelPegawai(p);
    })();

    /* ---------- jenis cuti, ibadah, berkas tambahan ---------- */
    function aturJenis() {
        var j = terpilih('jenis_cuti');
        var jenis = j ? j.value : '';
        var tahunan = jenis === 'tahunan';

        el('boxIbadah').style.display = tahunan ? 'block' : 'none';
        if (!tahunan) el('cutiIbadah').checked = false;

        var ibadah = tahunan && el('cutiIbadah').checked;
        var label = ibadah ? tambahan.ibadah : (tambahan[jenis] || null);

        var field = el('tambahanField'), file = el('fTambahan');
        field.style.display = label ? '' : 'none';
        file.required = !!label;
        if (label) {
            el('tambahanLabel').textContent = label;
        } else if (file.files && file.files.length) {
            file.value = '';
            field.querySelector('[data-file-chip]').classList.remove('show');
            field.querySelector('[data-dropzone]').classList.remove('has-file');
        }

        var hint = '';
        if (jenis === 'melahirkan') hint = 'Cuti melahirkan ' + LAMA_LHR + ' bulan — tanggal selesai terisi otomatis dari tanggal mulai.';
        else if (jenis === 'besar') hint = 'Cuti besar paling lama ' + maksBulan.besar + ' bulan.';
        else if (jenis === 'alasan_penting') hint = 'Cuti karena alasan penting paling lama ' + maksBulan.alasan_penting + ' bulan.';
        else if (tahunan && !ibadah) hint = 'Cuti tahunan paling lama ' + MAKS_THN + ' hari kerja.';
        else if (tahunan && ibadah) hint = 'Cuti ibadah: tidak dibatasi ' + MAKS_THN + ' hari kerja.';
        el('hintSelesai').textContent = hint;

        otomatisSelesai();
        hitung();
    }

    /* Melahirkan: selesai = mulai + 3 bulan - 1 hari (masih boleh diubah bila lebih singkat). */
    function otomatisSelesai() {
        var j = terpilih('jenis_cuti');
        var a = parse(el('tglMulai').value);
        if (!j || !a || el('tglSelesai').dataset.manual === '1') return;
        if (j.value === 'melahirkan') el('tglSelesai').value = fmt(addDays(addMonths(a, LAMA_LHR), -1));
    }
    el('tglSelesai').addEventListener('input', function () { this.dataset.manual = '1'; });
    if (el('tglSelesai').value) el('tglSelesai').dataset.manual = '1';

    /* ---------- hitung "selama" ---------- */
    var salah = true;
    function hitung() {
        var j = terpilih('jenis_cuti');
        var r = CutiLama.hitung({
            jenis: j ? j.value : '', mulai: el('tglMulai').value, selesai: el('tglSelesai').value,
            ibadah: el('cutiIbadah').checked, libur: libur, maksTahunan: MAKS_THN, maksBulan: maksBulan
        });
        el('selamaTeks').textContent = r.teks;
        el('selamaKet').textContent = r.ket;
        el('selamaBox').classList.toggle('is-bad', !!r.salah && !r.kosong);
        salah = !!r.salah;
    }

    /* ---------- tombol kirim ---------- */
    function siap() {
        var ok = !!el('nipPegawai').value && !!terpilih('jenis_cuti') && !salah
              && !!form.querySelector('textarea[name="alasan"]').value.trim()
              && !!form.querySelector('textarea[name="alamat_cuti"]').value.trim();
        form.querySelectorAll('input[type=file][required]').forEach(function (f) {
            if (!f.files || !f.files[0]) ok = false;
        });
        submit.disabled = !ok;
    }
    ['change', 'input', 'drop', 'click'].forEach(function (evt) {
        form.addEventListener(evt, function () { setTimeout(siap, 0); });
    });

    form.querySelectorAll('input[name="jenis_cuti"]').forEach(function (r) { r.addEventListener('change', aturJenis); });
    el('cutiIbadah').addEventListener('change', aturJenis);
    el('tglMulai').addEventListener('input', function () { otomatisSelesai(); hitung(); });
    el('tglSelesai').addEventListener('input', hitung);

    aturJenis();
    pilihPegawai();
    siap();
});
</script>

@endsection
