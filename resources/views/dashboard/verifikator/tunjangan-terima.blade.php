@extends('layout.dashboard-verifikator')

@section('title', 'Form Tunjangan | Dinas Pendidikan Kota Makassar')
@section('page-title', $verifikasi ? 'Ubah Data Surat Tunjangan' : 'Form Surat Tunjangan')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" style="color:inherit;text-decoration:none;">{{ $pengajuan->sekolah->nama_sekolah }}</a> / <span>Isi Form Surat Tunjangan</span>
@endsection

@push('styles')
<style>
    .form-field input[readonly]{background:var(--bg);color:var(--blue-800);font-weight:600;}
</style>
@endpush

@section('content')

@php
    $v = fn ($n, $default = '') => old($n, $verifikasi->{$n} ?? $default);

    // Nilai awal dari pilihan sekolah (PNS/PPPK, anak atau istri/suami) bila belum pernah diisi.
    $jenisPegawaiAwal = old('jenis_pegawai', $verifikasi->jenis_pegawai ?? ($pengajuan->detail['jenis_pegawai'] ?? ''));
    $pilihanSekolah   = $pengajuan->detail['jenis_tunjangan'] ?? '';
    $jenisTunjAwal    = old('jenis_tunjangan', $verifikasi->jenis_tunjangan
        ?? ($pilihanSekolah === 'anak' ? 'anak' : ''));
    $anakKeAwal       = old('anak_ke', $verifikasi->anak_ke ?? 1);

    $dataPegawai = $daftarPegawai->map(fn ($p) => [
        'nip'              => $p->nip,
        'nama'             => $p->nama,
        'pangkat_golongan' => $p->golongan_pangkat,
        'jabatan'          => $p->nama_jabatan,
        'unit_kerja'       => $p->unit_kerja ?: ($p->sekolah->nama_sekolah ?? ''),
        'status_pegawai'   => $p->status_pegawai,
    ])->values()->all();
@endphp

<form method="POST" action="{{ route('verifikator.tunjangan.terima.store', $pengajuan) }}">
    @csrf

    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="12" rx="2.2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10.5H21" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h2>Jenis Tunjangan</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Jenis Pegawai <span class="req">*</span></label>
                <select name="jenis_pegawai" id="jenisPegawai" required>
                    <option value="">— Pilih PNS atau PPPK —</option>
                    <option value="PNS"  {{ $jenisPegawaiAwal === 'PNS'  ? 'selected' : '' }}>PNS</option>
                    <option value="PPPK" {{ $jenisPegawaiAwal === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                </select>
                @error('jenis_pegawai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Terisi dari pilihan sekolah saat mengunggah berkas.</span>
            </div>

            <div class="form-field">
                <label>Tunjangan yang Diajukan <span class="req">*</span></label>
                <select name="jenis_tunjangan" id="jenisTunjangan" required>
                    <option value="">— Pilih —</option>
                    <option value="istri" {{ $jenisTunjAwal === 'istri' ? 'selected' : '' }}>Istri</option>
                    <option value="suami" {{ $jenisTunjAwal === 'suami' ? 'selected' : '' }}>Suami</option>
                    <option value="anak"  {{ $jenisTunjAwal === 'anak'  ? 'selected' : '' }}>Anak</option>
                    <option value="istri_anak" {{ $jenisTunjAwal === 'istri_anak' ? 'selected' : '' }}>Istri dan Anak</option>
                    <option value="suami_anak" {{ $jenisTunjAwal === 'suami_anak' ? 'selected' : '' }}>Suami dan Anak</option>
                </select>
                @error('jenis_tunjangan')<p class="field-error">{{ $message }}</p>@enderror
                @if ($pilihanSekolah === 'istri_suami')
                    <span class="form-hint">Sekolah memilih "Istri / Suami" — tentukan sesuai dokumen (Buku Nikah / KP4).</span>
                @elseif ($pilihanSekolah === 'istri_suami_anak')
                    <span class="form-hint">Sekolah memilih "Istri / Suami dan Anak" — pilih "Istri dan Anak" atau "Suami dan Anak" sesuai dokumen.</span>
                @endif
            </div>

            <div class="form-field" id="boxAnakKe" style="display:none;">
                <label>Anak Ke- <span class="req">*</span></label>
                <input type="number" name="anak_ke" id="anakKe" min="1" max="3" value="{{ $anakKeAwal }}">
                @error('anak_ke')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Dicetak di surat, contoh: "Tunjangan Anak Ke-1".</span>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>Data Pegawai</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>NIP Pegawai <span class="req">*</span></label>
                <input type="text" name="nip_pegawai" id="nipPegawai" list="daftarNip" maxlength="18"
                       value="{{ $v('nip_pegawai') }}" placeholder="Cari / ketik NIP..." required>
                <datalist id="daftarNip">
                    @foreach ($daftarPegawai as $p)
                        <option value="{{ $p->nip }}">{{ $p->nama }}</option>
                    @endforeach
                </datalist>
                @error('nip_pegawai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Hanya pegawai dari sekolah yang ditugaskan kepada Anda.</span>
            </div>

            <div class="form-field">
                <label>Nama Pegawai</label>
                <input type="text" id="namaPegawai" value="{{ $verifikasi->nama_pegawai ?? '' }}" readonly placeholder="Terisi otomatis setelah NIP dipilih">
            </div>

            <div class="form-field">
                <label>Pangkat / Gol <span class="req">*</span></label>
                <input type="text" name="pangkat_golongan" id="pangkatGolongan" value="{{ $v('pangkat_golongan') }}" required>
                @error('pangkat_golongan')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Terisi otomatis dari data pegawai, boleh disesuaikan. PPPK contoh: IX. PNS contoh: Penata Muda Tk. I (III/b).</span>
            </div>

            <div class="form-field">
                <label>Jabatan <span class="req">*</span></label>
                <input type="text" name="jabatan" id="jabatan" value="{{ $v('jabatan') }}" required>
                @error('jabatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field full">
                <label>Unit Kerja <span class="req">*</span></label>
                <input type="text" name="unit_kerja" id="unitKerja" value="{{ $v('unit_kerja') }}" required>
                @error('unit_kerja')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="panel">
        <p style="font-size:12.5px;color:var(--muted);margin:0 0 4px;line-height:1.6;">
            Nomor surat, tanggal surat, dan tanda tangan <strong>tidak diisi di sini</strong>. Di PDF, kode
            <code>${nomor_naskah}</code>, <code>${tanggal_naskah}</code>, <code>${ttd_pengirim}</code>,
            <code>${nama_pengirim}</code>, dan <code>${nip_pengirim}</code> dibiarkan apa adanya untuk diisi sistem persuratan.
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

<script>
(function () {
    var pegawai = @json($dataPegawai);
    var el = function (id) { return document.getElementById(id); };

    // "Anak ke-" hanya muncul bila tunjangan anak
    function aturAnak() {
        var anak = el('jenisTunjangan').value.indexOf('anak') !== -1;
        el('boxAnakKe').style.display = anak ? '' : 'none';
        el('anakKe').required = anak;
    }
    el('jenisTunjangan').addEventListener('change', aturAnak);
    aturAnak();

    // Isi otomatis nama / pangkat / jabatan / unit kerja dari NIP.
    // Kolom yang sudah diketik manual tidak ditimpa (data-manual).
    ['pangkatGolongan', 'jabatan', 'unitKerja'].forEach(function (id) {
        el(id).addEventListener('input', function () { this.dataset.manual = '1'; });
        if (el(id).value) { el(id).dataset.manual = '1'; }
    });

    function isi(id, val) {
        var f = el(id);
        if (f.dataset.manual === '1' && f.value) { return; }
        f.value = val || '';
    }

    el('nipPegawai').addEventListener('input', function () {
        var nip = this.value.replace(/\D/g, '');
        var p = pegawai.find(function (x) { return x.nip === nip; });
        el('namaPegawai').value = p ? p.nama : '';
        if (!p) { return; }
        isi('pangkatGolongan', p.pangkat_golongan);
        isi('jabatan', p.jabatan);
        isi('unitKerja', p.unit_kerja);
        if (!el('jenisPegawai').value) {
            el('jenisPegawai').value = /PPPK|P3K/i.test(p.status_pegawai || '') ? 'PPPK' : 'PNS';
        }
    });
    // Jalankan sekali untuk NIP yang sudah terisi (mode ubah / isian lama setelah error)
    el('nipPegawai').dispatchEvent(new Event('input'));
})();
</script>

@endsection
