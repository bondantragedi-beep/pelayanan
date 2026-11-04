@extends('layout.dashboard-verifikator')

@section('title', 'Form Cuti | Dinas Pendidikan Kota Makassar')
@section('page-title', $verifikasi ? 'Ubah Data Formulir Cuti' : 'Form Formulir Cuti')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" style="color:inherit;text-decoration:none;">{{ $pengajuan->sekolah->nama_sekolah }}</a> / <span>Isi Formulir Cuti</span>
@endsection

@push('styles')
<style>
    .form-field input[readonly]{background:var(--bg);color:var(--blue-800);font-weight:600;}
    .pilih-grup{display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;}
    .pilih-grup label{display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--line);border-radius:12px;cursor:pointer;font-size:13.5px;font-weight:600;background:#fff;}
    .pilih-grup label:has(input:checked){border-color:var(--blue-700);background:var(--blue-50);color:var(--blue-800);}
    .catatan-tbl{width:100%;border-collapse:collapse;font-size:13px;}
    .catatan-tbl th{text-align:left;font-size:12px;color:var(--muted);font-weight:700;padding:6px 8px;}
    .catatan-tbl td{padding:5px 8px 5px 0;vertical-align:middle;}
    .catatan-tbl input{width:100%;height:40px;border:1px solid var(--line-strong);border-radius:10px;padding:0 10px;font-size:13px;font-family:inherit;}
    .catatan-tbl input[type=number]{max-width:90px;}
    .catatan-tbl .thn{font-weight:700;color:var(--blue-800);white-space:nowrap;}
    .selama-box{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:14px;padding:12px 14px;border-radius:12px;background:var(--blue-50);border:1px solid var(--line);font-size:13px;}
    .selama-box strong{font-size:15px;color:var(--blue-800);}
    .selama-box.is-bad{background:var(--danger-bg);border-color:var(--danger-line);color:var(--red-600);}
    .selama-box.is-bad strong{color:var(--red-600);}
    .ibadah-row{display:none;align-items:center;gap:9px;margin-top:12px;font-size:13px;font-weight:600;}
    .ibadah-row input{width:17px;height:17px;accent-color:var(--blue-700);}
    .hint-box{font-size:12.5px;color:var(--muted);line-height:1.6;margin:0 0 14px;padding:10px 12px;background:var(--blue-50);border-radius:10px;border:1px solid var(--line);}
</style>
@endpush

@section('content')

@php
    $cat  = $verifikasi?->catatan ?? [];
    $thn  = $cat['tahunan'] ?? [];
    $tgl  = fn ($c) => $c ? $c->format('Y-m-d') : null;

    $f = [
        'nip_pegawai'      => old('nip_pegawai', $verifikasi->nip_pegawai ?? $isian['nip_pegawai']),
        'jabatan'          => old('jabatan', $verifikasi->jabatan ?? $isian['jabatan']),
        'unit_kerja'       => old('unit_kerja', $verifikasi->unit_kerja ?? $isian['unit_kerja']),
        'masa_kerja'       => old('masa_kerja', $verifikasi->masa_kerja ?? $isian['masa_kerja']),
        'pangkat_golongan' => old('pangkat_golongan', $verifikasi->pangkat_golongan ?? $isian['pangkat_golongan']),

        'jenis_cuti'       => old('jenis_cuti', $verifikasi->jenis_cuti ?? $isian['jenis_cuti']),
        'cuti_ibadah'      => (bool) old('cuti_ibadah', $verifikasi->cuti_ibadah ?? $isian['cuti_ibadah']),
        'alasan'           => old('alasan', $verifikasi->alasan ?? $isian['alasan']),
        'tanggal_mulai'    => old('tanggal_mulai', $tgl($verifikasi->tanggal_mulai ?? null) ?? $isian['tanggal_mulai']),
        'tanggal_selesai'  => old('tanggal_selesai', $tgl($verifikasi->tanggal_selesai ?? null) ?? $isian['tanggal_selesai']),

        'sisa_n2' => old('sisa_n2', $thn[0]['sisa'] ?? $isian['sisa_n2']),
        'sisa_n1' => old('sisa_n1', $thn[1]['sisa'] ?? $isian['sisa_n1']),
        'sisa_n'  => old('sisa_n',  $thn[2]['sisa'] ?? $isian['sisa_n']),
        'ket_n2'  => old('ket_n2', $thn[0]['ket'] ?? ''),
        'ket_n1'  => old('ket_n1', $thn[1]['ket'] ?? ''),
        'ket_n'   => old('ket_n',  $thn[2]['ket'] ?? ''),

        'alamat_cuti' => old('alamat_cuti', $verifikasi->alamat_cuti ?? $isian['alamat_cuti']),
        'telp'        => old('telp', $verifikasi->telp ?? $isian['telp']),
        'nama_kepsek' => old('nama_kepsek', $verifikasi->nama_kepsek ?? $isian['nama_kepsek']),
        'pertimbangan' => old('pertimbangan', $verifikasi->pertimbangan ?? 'disetujui'),
        'keputusan'    => old('keputusan', $verifikasi->keputusan ?? 'disetujui'),
    ];

    $tahunAwal = $f['tanggal_mulai'] ? (int) substr($f['tanggal_mulai'], 0, 4) : $isian['tahun_n'];

    $lainnya = [
        'besar'           => '2. Cuti Besar',
        'sakit'           => '3. Cuti Sakit',
        'melahirkan'      => '4. Cuti Melahirkan',
        'alasan_penting'  => '5. Cuti Karena Alasan Penting',
        'luar_tanggungan' => '6. Cuti di Luar Tanggungan Negara',
    ];

    $dataPegawai = $daftarPegawai->map(fn ($p) => [
        'nip'              => $p->nip,
        'nama'             => $p->nama,
        'pangkat_golongan' => $p->golongan_pangkat,
        'jabatan'          => $p->nama_jabatan,
        'unit_kerja'       => $p->unit_kerja ?: ($p->sekolah->nama_sekolah ?? ''),
        'masa_kerja'       => $p->masa_kerja_tahun !== null ? $p->masa_kerja_tahun . ' Tahun ' . (int) $p->masa_kerja_bulan . ' Bulan' : '',
        'telp'             => $p->hp ?: $p->telp,
    ])->values()->all();
@endphp

<form method="POST" action="{{ route('verifikator.cuti.terima.store', $pengajuan) }}">
    @csrf

    {{-- ============ I. DATA PEGAWAI ============ --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>I. Data Pegawai</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>NIP Pegawai <span class="req">*</span></label>
                <input type="text" name="nip_pegawai" id="nipPegawai" list="daftarNip" maxlength="18" value="{{ $f['nip_pegawai'] }}" placeholder="Cari / ketik NIP..." required>
                <datalist id="daftarNip">
                    @foreach ($daftarPegawai as $p)
                        <option value="{{ $p->nip }}">{{ $p->nama }}</option>
                    @endforeach
                </datalist>
                @error('nip_pegawai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Terisi dari pegawai yang dipilih sekolah. Hanya pegawai dari sekolah yang ditugaskan kepada Anda.</span>
            </div>

            <div class="form-field">
                <label>Nama Pegawai</label>
                <input type="text" id="namaPegawai" value="{{ $verifikasi->nama_pegawai ?? '' }}" readonly placeholder="Terisi otomatis setelah NIP dipilih">
            </div>

            <div class="form-field">
                <label>Jabatan</label>
                <input type="text" name="jabatan" id="jabatan" value="{{ $f['jabatan'] }}">
                @error('jabatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Pangkat / Golongan</label>
                <input type="text" name="pangkat_golongan" id="pangkatGolongan" value="{{ $f['pangkat_golongan'] }}">
                @error('pangkat_golongan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Masa Kerja</label>
                <input type="text" name="masa_kerja" id="masaKerja" value="{{ $f['masa_kerja'] }}" placeholder="Contoh: 12 Tahun 3 Bulan">
                @error('masa_kerja')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Unit Kerja</label>
                <input type="text" name="unit_kerja" id="unitKerja" value="{{ $f['unit_kerja'] }}">
                @error('unit_kerja')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ============ II. JENIS CUTI, III. ALASAN, IV. LAMANYA ============ --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h2>II–IV. Jenis, Alasan, dan Lamanya Cuti</h2>
        </div>

        <div class="form-field">
            <label>Jenis Cuti yang Diambil <span class="req">*</span></label>
            <div class="pilih-grup">
                @foreach (\App\Support\LamaCuti::JENIS as $kode => $label)
                    <label><input type="radio" name="jenis_cuti" value="{{ $kode }}" {{ $f['jenis_cuti'] === $kode ? 'checked' : '' }} required> {{ $label }}</label>
                @endforeach
            </div>
            @error('jenis_cuti')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <label class="ibadah-row" id="rowIbadah">
            <input type="checkbox" name="cuti_ibadah" value="1" id="cutiIbadah" {{ $f['cuti_ibadah'] ? 'checked' : '' }}>
            Cuti karena ibadah (umroh / haji) — tidak dibatasi {{ config('cuti.maks_tahunan') }} hari kerja
        </label>

        <div class="form-grid" style="margin-top:18px;">
            <div class="form-field full">
                <label>Alasan Cuti <span class="req">*</span></label>
                <textarea name="alasan" maxlength="500" required>{{ $f['alasan'] }}</textarea>
                @error('alasan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Mulai Tanggal <span class="req">*</span></label>
                <input type="date" name="tanggal_mulai" id="tglMulai" value="{{ $f['tanggal_mulai'] }}" required>
                @error('tanggal_mulai')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Sampai Tanggal <span class="req">*</span></label>
                <input type="date" name="tanggal_selesai" id="tglSelesai" value="{{ $f['tanggal_selesai'] }}" required>
                @error('tanggal_selesai')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="selama-box" id="selamaBox">
            <span>Selama:</span> <strong id="selamaTeks">—</strong> <span id="selamaKet" style="color:var(--muted);"></span>
        </div>
    </div>

    {{-- ============ V. CATATAN CUTI ============ --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.5 13H15.5M8.5 16.5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <h2>V. Catatan Cuti</h2>
        </div>

        <p class="hint-box">
            Isi <strong>sisa cuti tahunan sebelum pengajuan ini</strong> (hak per tahun {{ config('cuti.hak_tahunan') }} hari kerja).
            Nilai awal diambil dari catatan sistem; bila pegawai pernah cuti sebelum sistem ini dipakai, sesuaikan di sini.
            Catatan ini tersimpan dan otomatis muncul pada pengajuan berikutnya. Cuti tahunan pengajuan ini dipotong dari sisa tahun N
            otomatis setelah dokumen terkirim ke sekolah.
        </p>

        <table class="catatan-tbl">
            <thead><tr><th>1. Cuti Tahunan — Tahun</th><th>Sisa (hari)</th><th>Keterangan</th></tr></thead>
            <tbody>
                <tr>
                    <td class="thn"><span data-tahun="-2">{{ $tahunAwal - 2 }}</span> (N-2)</td>
                    <td><input type="number" name="sisa_n2" min="0" max="{{ config('cuti.hak_tahunan') }}" value="{{ $f['sisa_n2'] }}" required></td>
                    <td><input type="text" name="ket_n2" maxlength="100" value="{{ $f['ket_n2'] }}"></td>
                </tr>
                <tr>
                    <td class="thn"><span data-tahun="-1">{{ $tahunAwal - 1 }}</span> (N-1)</td>
                    <td><input type="number" name="sisa_n1" min="0" max="{{ config('cuti.hak_tahunan') }}" value="{{ $f['sisa_n1'] }}" required></td>
                    <td><input type="text" name="ket_n1" maxlength="100" value="{{ $f['ket_n1'] }}"></td>
                </tr>
                <tr>
                    <td class="thn"><span data-tahun="0">{{ $tahunAwal }}</span> (N)</td>
                    <td><input type="number" name="sisa_n" min="0" max="{{ config('cuti.hak_tahunan') }}" value="{{ $f['sisa_n'] }}" required></td>
                    <td><input type="text" name="ket_n" maxlength="100" value="{{ $f['ket_n'] }}"></td>
                </tr>
            </tbody>
        </table>
        @error('sisa_n2')<p class="field-error">{{ $message }}</p>@enderror
        @error('sisa_n1')<p class="field-error">{{ $message }}</p>@enderror
        @error('sisa_n')<p class="field-error">{{ $message }}</p>@enderror

        <div class="form-grid" style="margin-top:18px;">
            @foreach ($lainnya as $kode => $label)
                <div class="form-field">
                    <label>{{ $label }}</label>
                    <input type="text" name="catatan_{{ $kode }}" maxlength="100"
                           value="{{ old('catatan_' . $kode, $cat[$kode] ?? '') }}"
                           placeholder="Kosongkan bila belum pernah">
                    @error('catatan_' . $kode)<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============ VI. ALAMAT ============ --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21C12 21 19 15 19 9.5C19 5.9 15.9 3 12 3C8.1 3 5 5.9 5 9.5C5 15 12 21 12 21Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9.5" r="2.4" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h2>VI. Alamat Selama Menjalankan Cuti</h2>
        </div>

        <div class="form-grid">
            <div class="form-field full">
                <label>Alamat <span class="req">*</span></label>
                <textarea name="alamat_cuti" maxlength="500" required>{{ $f['alamat_cuti'] }}</textarea>
                @error('alamat_cuti')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-field">
                <label>No. Telepon Pegawai</label>
                <input type="text" name="telp" id="telp" maxlength="30" value="{{ $f['telp'] }}">
                @error('telp')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ============ VII & VIII ============ --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h2>VII–VIII. Pertimbangan dan Keputusan</h2>
        </div>

        <div class="form-field" style="margin-bottom:16px;">
            <label>Nama Kepala Sekolah (tercetak di Pertimbangan Atasan Langsung)</label>
            <input type="text" name="nama_kepsek" maxlength="255" value="{{ $f['nama_kepsek'] }}" placeholder="Terisi dari data pegawai berjabatan Kepala Sekolah, bila ada">
            <span class="form-hint">Dicetak bersama nama sekolah: <strong>{{ $pengajuan->sekolah->nama_sekolah }}</strong>.</span>
            @error('nama_kepsek')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-field" style="margin-bottom:16px;">
            <label>VII. Pertimbangan Atasan Langsung <span class="req">*</span></label>
            <div class="pilih-grup">
                @foreach ($pilihan as $kode => $label)
                    <label><input type="radio" name="pertimbangan" value="{{ $kode }}" {{ $f['pertimbangan'] === $kode ? 'checked' : '' }} required> {{ $label }}</label>
                @endforeach
            </div>
            <span class="form-hint">Centang <strong>Disetujui</strong> bila Formulir Permohonan Cuti yang diunggah sekolah sudah ada persetujuan sekolah.</span>
            @error('pertimbangan')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-field">
            <label>VIII. Keputusan Pejabat yang Memberikan Cuti <span class="req">*</span></label>
            <div class="pilih-grup">
                @foreach ($pilihan as $kode => $label)
                    <label><input type="radio" name="keputusan" value="{{ $kode }}" {{ $f['keputusan'] === $kode ? 'checked' : '' }} required> {{ $label }}</label>
                @endforeach
            </div>
            @error('keputusan')<p class="field-error">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="panel">
        <p style="font-size:12.5px;color:var(--muted);margin:0 0 4px;line-height:1.6;">
            Tanggal naskah, jabatan tujuan, dan tanda tangan pejabat <strong>tidak diisi di sini</strong>. Di PDF, kode
            <code>${tanggal_naskah}</code>, <code>${jabatan_tujuan}</code>, dan <code>${ttd_pengirim}</code>
            dibiarkan apa adanya untuk diisi sistem persuratan.
        </p>

        <div class="form-actions">
            <a href="{{ $verifikasi ? route('verifikator.berkas.cetak', $pengajuan) : route('verifikator.berkas.show', $pengajuan) }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
                Simpan &amp; Lanjut ke Unduh PDF
            </button>
        </div>
    </div>
</form>

<script src="{{ asset('js/cuti-lama.js') }}"></script>
<script>
(function () {
    var pegawai   = @json($dataPegawai);
    var libur     = @json((object) config('cuti.hari_libur'));
    var maksBulan = @json((object) config('cuti.maks_bulan'));
    var MAKS_THN  = {{ (int) config('cuti.maks_tahunan') }};
    var el = function (id) { return document.getElementById(id); };
    var form = document.querySelector('form');
    var jenis = function () { var r = form.querySelector('input[name="jenis_cuti"]:checked'); return r ? r.value : ''; };

    /* --- Isi otomatis data pegawai dari NIP; kolom yang diketik manual tidak ditimpa --- */
    var kolom = { pangkatGolongan: 'pangkat_golongan', jabatan: 'jabatan', unitKerja: 'unit_kerja', masaKerja: 'masa_kerja', telp: 'telp' };
    Object.keys(kolom).forEach(function (id) {
        el(id).addEventListener('input', function () { this.dataset.manual = '1'; });
        if (el(id).value) el(id).dataset.manual = '1';
    });
    el('nipPegawai').addEventListener('input', function () {
        var nip = this.value.replace(/\D/g, '');
        var p = pegawai.find(function (x) { return x.nip === nip; });
        el('namaPegawai').value = p ? p.nama : '';
        if (!p) return;
        Object.keys(kolom).forEach(function (id) {
            var f = el(id);
            if (f.dataset.manual === '1' && f.value) return;
            f.value = p[kolom[id]] || '';
        });
    });
    el('nipPegawai').dispatchEvent(new Event('input'));

    /* --- Lamanya cuti, ibadah, label tahun N-2 / N-1 / N --- */
    function segarkan() {
        var tahunan = jenis() === 'tahunan';
        el('rowIbadah').style.display = tahunan ? 'flex' : 'none';
        if (!tahunan) el('cutiIbadah').checked = false;

        var r = CutiLama.hitung({
            jenis: jenis(), mulai: el('tglMulai').value, selesai: el('tglSelesai').value,
            ibadah: el('cutiIbadah').checked, libur: libur, maksTahunan: MAKS_THN, maksBulan: maksBulan
        });
        el('selamaTeks').textContent = r.teks;
        el('selamaKet').textContent = r.ket;
        el('selamaBox').classList.toggle('is-bad', !!r.salah && !r.kosong);

        var th = parseInt((el('tglMulai').value || '').substr(0, 4), 10);
        if (th) {
            document.querySelectorAll('[data-tahun]').forEach(function (s) {
                s.textContent = th + parseInt(s.getAttribute('data-tahun'), 10);
            });
        }
    }
    form.querySelectorAll('input[name="jenis_cuti"]').forEach(function (r) { r.addEventListener('change', segarkan); });
    ['tglMulai', 'tglSelesai', 'cutiIbadah'].forEach(function (id) { el(id).addEventListener('input', segarkan); el(id).addEventListener('change', segarkan); });
    segarkan();
})();
</script>

@endsection
