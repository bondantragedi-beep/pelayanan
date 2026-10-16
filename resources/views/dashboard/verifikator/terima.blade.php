@extends('layout.dashboard-verifikator')

@section('title', 'Form KGB | Dinas Pendidikan Kota Makassar')
@section('page-title', $verifikasi ? 'Ubah Data Surat KGB' : 'Form Surat KGB')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" style="color:inherit;text-decoration:none;">{{ $pengajuan->sekolah->nama_sekolah }}</a> / <span>Isi Form Surat KGB</span>
@endsection

@push('styles')
<style>
    .form-field input[readonly]{background:var(--blue-50);color:var(--blue-800);font-weight:600;}
    .pilih-jenis-note{
        display:none;margin-top:6px;font-size:12.5px;color:#8A5A0A;background:#FFFBF3;
        border:1px solid #F0DDA8;border-radius:10px;padding:10px 12px;
    }
</style>
@endpush

@section('content')

@php
    $v     = fn ($n, $default = '') => old($n, $verifikasi->{$n} ?? $default);
    $d     = fn ($n) => old($n, optional($verifikasi->{$n} ?? null)->toDateString());
    $uang  = fn ($n) => old($n, isset($verifikasi->{$n}) ? number_format($verifikasi->{$n}, 0, ',', '.') : '');
    $jenisAwal = old('jenis_pegawai', $verifikasi->jenis_pegawai ?? '');

    // Data untuk public/js/kgb-form.js
    $kgbForm = [
        'edit'         => (bool) $verifikasi,
        'golonganAwal' => old('golongan', $verifikasi->golongan ?? ''),
        'golongan'     => ['PNS' => $golonganPns, 'PPPK' => $golonganPppk],
        'pegawai'      => $daftarPegawai->map(function ($p) {
            return [
                'nip'             => $p->nip,
                'nama'            => $p->nama,
                'pangkat_jabatan' => $p->pangkat_jabatan,
                'golongan'        => $p->golongan,
                'unit_kerja'      => $p->unit_kerja ?: ($p->sekolah->nama_sekolah ?? ''),
                'status_pegawai'  => $p->status_pegawai,
            ];
        })->values()->all(),
        'gaji'         => $referensiGaji->map(function ($r) {
            return [
                'golongan'         => $r->golongan,
                'masa_kerja_tahun' => $r->masa_kerja_tahun,
                'gaji_pokok'       => $r->gaji_pokok,
            ];
        })->values()->all(),
    ];
@endphp

<form method="POST" action="{{ route('verifikator.berkas.terima.store', $pengajuan) }}">
    @csrf

    {{-- ===== 1. Jenis surat & data pegawai ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>Jenis Pegawai &amp; Data Pegawai</h2>
        </div>

        <div class="form-grid">
            <div class="form-field full">
                <label>Jenis Pegawai <span class="req">*</span></label>
                <select name="jenis_pegawai" id="jenisPegawai" required>
                    <option value="">— Pilih PNS atau PPPK —</option>
                    <option value="PNS"  {{ $jenisAwal === 'PNS'  ? 'selected' : '' }}>PNS</option>
                    <option value="PPPK" {{ $jenisAwal === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                </select>
                @error('jenis_pegawai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Format surat PNS dan PPPK berbeda. Pilihan terisi otomatis dari status pegawai saat NIP dipilih, tapi tetap bisa diganti.</span>
            </div>

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
                <label><span id="labelPangkat">Pangkat / Jabatan</span> <span class="req">*</span></label>
                <input type="text" name="pangkat_jabatan" id="pangkatJabatan" value="{{ $v('pangkat_jabatan') }}" required>
                @error('pangkat_jabatan')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Terisi otomatis dari data pegawai, boleh disesuaikan.</span>
            </div>

            <div class="form-field">
                <label>Unit Kerja <span class="req">*</span></label>
                <input type="text" name="unit_kerja" id="unitKerja" value="{{ $v('unit_kerja') }}" required>
                @error('unit_kerja')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <p class="pilih-jenis-note" id="catatanPilihJenis">Pilih jenis pegawai (PNS atau PPPK) dulu untuk menampilkan isian yang sesuai.</p>
    </div>

    {{-- ===== 2. Surat keputusan terakhir ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
            </div>
            <h2>Surat Keputusan Terakhir</h2>
        </div>

        <div class="form-grid">
            <div class="form-field full">
                <label>Pejabat Penetap SK <span class="req">*</span></label>
                <input type="text" name="sk_pejabat" list="daftarSkPejabat" value="{{ $v('sk_pejabat') }}" placeholder="Pilih dari daftar atau ketik sendiri" required>
                <datalist id="daftarSkPejabat">
                    @foreach ($daftarSkPejabat as $pejabat)
                        <option value="{{ $pejabat }}"></option>
                    @endforeach
                </datalist>
                @error('sk_pejabat')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Dicetak apa adanya di surat. Contoh: PRESIDEN REPUBLIK INDONESIA, Walikota Makassar.</span>
            </div>

            <div class="form-field">
                <label>Tanggal SK <span class="req">*</span></label>
                <input type="date" name="tanggal_sk" value="{{ $d('tanggal_sk') }}" required>
                @error('tanggal_sk')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Nomor SK <span class="req">*</span></label>
                <input type="text" name="nomor_sk" value="{{ $v('nomor_sk') }}" placeholder="Contoh: PN.822.4/898/DP/IX/2024" required>
                @error('nomor_sk')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field" data-only="PPPK">
                <label>Tanggal Berlaku Gaji Tersebut <span class="req">*</span></label>
                <input type="date" name="sk_berlaku_gaji" value="{{ $d('sk_berlaku_gaji') }}">
                @error('sk_berlaku_gaji')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== 3. Golongan, masa kerja & gaji ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2>Golongan, Masa Kerja &amp; Gaji</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Dalam Golongan <span class="req">*</span></label>
                <select name="golongan" id="golongan" required>
                    <option value="">— Pilih jenis pegawai dulu —</option>
                </select>
                @error('golongan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Masa Kerja Golongan (MKG) — Tahun <span class="req">*</span></label>
                <input type="number" name="masa_kerja_tahun" id="masaKerjaTahun" min="2" max="50"
                       value="{{ $v('masa_kerja_tahun') }}" placeholder="Contoh: 32" required>
                @error('masa_kerja_tahun')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">MKG BARU (setelah KGB ini). Gaji pokok lama otomatis diambil dari MKG 2 tahun sebelumnya.</span>
            </div>

            <div class="form-field" data-only="PPPK">
                <label>Masa Kerja Golongan (MKG) — Bulan</label>
                <input type="number" name="masa_kerja_bulan" id="masaKerjaBulan" min="0" max="11" value="{{ $v('masa_kerja_bulan', 0) }}">
                @error('masa_kerja_bulan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field" data-only="PPPK">
                <label>Masa Kerja Golongan pada Tanggal SK (otomatis)</label>
                <input type="text" id="masaKerjaLama" readonly placeholder="Terisi otomatis">
            </div>

            <div class="form-field">
                <label>Gaji Pokok Lama <span class="req">*</span></label>
                <input type="text" name="gaji_pokok_lama" id="gajiLama" inputmode="numeric" value="{{ $uang('gaji_pokok_lama') }}" placeholder="Contoh: 5.687.200" required>
                @error('gaji_pokok_lama')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Gaji Pokok Baru <span class="req">*</span></label>
                <input type="text" name="gaji_pokok_baru" id="gajiBaru" inputmode="numeric" value="{{ $uang('gaji_pokok_baru') }}" placeholder="Contoh: 5.866.400" required>
                @error('gaji_pokok_baru')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field full">
                <span class="form-hint" id="gajiHint"></span>
                <button type="button" id="gajiToggle" class="btn-secondary" style="display:none;align-self:flex-start;margin-top:8px;padding:8px 14px;font-size:12.5px;">Ubah manual</button>
            </div>

            <div class="form-field">
                <label>Mulai Tanggal <span class="req">*</span></label>
                <input type="date" name="tmt_baru" id="tmtBaru" value="{{ $d('tmt_baru') }}" required>
                @error('tmt_baru')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label id="labelKgbBerikutnya">KGB y.a.d (otomatis, +2 tahun)</label>
                <input type="text" id="kgbBerikutnya" readonly placeholder="Terisi otomatis">
            </div>

            <div class="form-field full" data-only="PNS">
                <label>Keterangan</label>
                <input type="text" name="keterangan" value="{{ $v('keterangan', 'ybs adalah Pegawai Negeri Sipil Kota Makassar') }}">
                @error('keterangan')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== 4. Khusus PPPK: perjanjian kerja ===== --}}
    <div class="panel" data-only="PPPK">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 9.5H20.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 3V6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>Perjanjian Kerja (khusus PPPK)</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Perpanjangan Perjanjian Kerja — Mulai <span class="req">*</span></label>
                <input type="date" name="perjanjian_mulai" id="perjanjianMulai" value="{{ $d('perjanjian_mulai') }}">
                @error('perjanjian_mulai')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Perpanjangan Perjanjian Kerja — Sampai <span class="req">*</span></label>
                <input type="date" name="perjanjian_selesai" id="perjanjianSelesai" value="{{ $d('perjanjian_selesai') }}">
                @error('perjanjian_selesai')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Masa Perjanjian Kerja (tahun) <span class="req">*</span></label>
                <input type="number" name="masa_perjanjian_tahun" id="masaPerjanjian" min="0" max="50" value="{{ $v('masa_perjanjian_tahun') }}">
                @error('masa_perjanjian_tahun')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Terisi otomatis dari selisih tanggal di atas, boleh disesuaikan.</span>
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
    window.KGB_FORM = @json($kgbForm);
</script>
<script src="{{ asset('js/kgb-form.js') }}"></script>

@endsection
