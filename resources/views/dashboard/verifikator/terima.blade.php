@extends('layout.dashboard-verifikator')

@section('title', 'Form Penerimaan KGB | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Form Penerimaan KGB')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" style="color:inherit;text-decoration:none;">{{ $pengajuan->sekolah->nama_sekolah }}</a> / <span>Isi Form Penerimaan</span>
@endsection

@push('styles')
<style>
    .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
    @media (max-width:720px){.form-grid{grid-template-columns:1fr;}}
    .form-field{display:flex;flex-direction:column;gap:6px;}
    .form-field.full{grid-column:1 / -1;}
    .form-field label{font-size:12.5px;font-weight:700;color:var(--ink);}
    .form-field .req{color:var(--red-600);}
    .form-field input,.form-field select{
        height:42px;padding:0 13px;border-radius:10px;border:1.5px solid var(--line);
        font-family:var(--font-body);font-size:13.5px;color:var(--ink);background:#fff;
    }
    .form-field input:focus,.form-field select:focus{outline:none;border-color:var(--blue-500);}
    .form-field input[readonly]{background:var(--blue-50);color:var(--blue-800);font-weight:600;}
    .form-hint{font-size:11.5px;color:var(--muted);}
</style>
@endpush

@section('content')

<div class="mock-note">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
    <span>Daftar pegawai & tabel gaji pokok masih data contoh — ganti begitu database pegawai dan tabel gaji resmi sudah diunggah.</span>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19C3.5 15.7 6 13 9 13C12 13 14.5 15.7 14.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </div>
        <h2>Data Pegawai &amp; Surat Keputusan</h2>
    </div>

    <form method="POST" action="{{ route('verifikator.berkas.terima.store', $pengajuan) }}" id="formTerima">
        @csrf

        <div class="form-grid">
            <div class="form-field">
                <label>NIP Pegawai <span class="req">*</span></label>
                <input type="text" name="nip_pegawai" id="nipPegawai" list="daftarNip" maxlength="18"
                       value="{{ old('nip_pegawai') }}" placeholder="Cari / ketik NIP..." required>
                <datalist id="daftarNip">
                    @foreach ($daftarPegawai as $p)
                        <option value="{{ $p->nip }}">{{ $p->nama }}</option>
                    @endforeach
                </datalist>
                @error('nip_pegawai')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Hanya menampilkan pegawai di kecamatan {{ $verifikatorKecamatan }}.</span>
            </div>

            <div class="form-field">
                <label>Nama Pegawai</label>
                <input type="text" id="namaPegawai" readonly placeholder="Terisi otomatis setelah NIP dipilih">
            </div>

            <div class="form-field">
                <label>Pangkat / Jabatan <span class="req">*</span></label>
                <input type="text" name="pangkat_jabatan" id="pangkatJabatan" list="daftarPangkat"
                       value="{{ old('pangkat_jabatan') }}" placeholder="Terisi otomatis, bisa disesuaikan" required>
                <datalist id="daftarPangkat">
                    @foreach ($daftarPegawai->pluck('pangkat_jabatan')->unique()->filter() as $pj)
                        <option value="{{ $pj }}"></option>
                    @endforeach
                </datalist>
                @error('pangkat_jabatan')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Jenis Surat Keputusan <span class="req">*</span></label>
                <select name="jenis_sk" required>
                    <option value="">— Pilih —</option>
                    @foreach ($daftarJenisSk as $jsk)
                        <option value="{{ $jsk }}" {{ old('jenis_sk') === $jsk ? 'selected' : '' }}>{{ $jsk }}</option>
                    @endforeach
                </select>
                @error('jenis_sk')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Nomor SK <span class="req">*</span></label>
                <input type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" placeholder="Contoh: 821.2/123/SK/IX/2026" required>
                @error('nomor_sk')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-field">
                <label>Tanggal SK <span class="req">*</span></label>
                <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk') }}" required>
                @error('tanggal_sk')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h2>Golongan &amp; Perhitungan Gaji</h2>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label>Golongan <span class="req">*</span></label>
            <select name="golongan" form="formTerima" id="golongan" required>
                <option value="">— Pilih —</option>
                @foreach ($daftarGolongan as $g)
                    <option value="{{ $g }}" {{ old('golongan') === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
            @error('golongan')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-field">
            <label>Masa Kerja (tahun) <span class="req">*</span></label>
            <input type="number" name="masa_kerja_tahun" form="formTerima" id="masaKerja" min="2" max="40" step="2"
                   value="{{ old('masa_kerja_tahun') }}" placeholder="Contoh: 4" required>
            @error('masa_kerja_tahun')<p class="field-error">{{ $message }}</p>@enderror
            <span class="form-hint">Gaji pokok lama otomatis diambil dari masa kerja 2 tahun sebelumnya, pada golongan yang sama.</span>
        </div>

        <div class="form-field">
            <label>Gaji Pokok Lama</label>
            <input type="text" id="gajiPokokLama" readonly placeholder="Terisi otomatis">
        </div>

        <div class="form-field">
            <label>TMT Baru <span class="req">*</span></label>
            <input type="date" name="tmt_baru" form="formTerima" value="{{ old('tmt_baru') }}" required>
            @error('tmt_baru')<p class="field-error">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('verifikator.berkas.show', $pengajuan) }}" class="btn-secondary">Batal</a>
        <button type="submit" form="formTerima" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
            Simpan &amp; Lanjut Cetak
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var daftarPegawai = @json($daftarPegawai->map(fn($p) => [
        'nip' => $p->nip, 'nama' => $p->nama, 'pangkat_jabatan' => $p->pangkat_jabatan, 'golongan' => $p->golongan,
    ]));
    var referensiGaji = @json($referensiGaji->map(fn($r) => [
        'golongan' => $r->golongan, 'masa_kerja_tahun' => $r->masa_kerja_tahun, 'gaji_pokok' => $r->gaji_pokok,
    ]));

    var nipInput = document.getElementById('nipPegawai');
    var namaOut = document.getElementById('namaPegawai');
    var pangkatInput = document.getElementById('pangkatJabatan');
    var golonganSelect = document.getElementById('golongan');

    function isiDariPegawai() {
        var found = daftarPegawai.find(function (p) { return p.nip === nipInput.value.trim(); });
        if (found) {
            namaOut.value = found.nama;
            if (!pangkatInput.value) pangkatInput.value = found.pangkat_jabatan || '';
            if (found.golongan && [...golonganSelect.options].some(function (o) { return o.value === found.golongan; })) {
                golonganSelect.value = found.golongan;
            }
            hitungGajiPokokLama();
        } else {
            namaOut.value = '';
        }
    }
    nipInput.addEventListener('input', isiDariPegawai);
    nipInput.addEventListener('change', isiDariPegawai);

    var masaKerjaInput = document.getElementById('masaKerja');
    var gajiOut = document.getElementById('gajiPokokLama');

    function hitungGajiPokokLama() {
        var golongan = golonganSelect.value;
        var masaKerja = parseInt(masaKerjaInput.value, 10);
        if (!golongan || isNaN(masaKerja)) { gajiOut.value = ''; return; }

        var target = masaKerja - 2;
        var row = referensiGaji.find(function (r) { return r.golongan === golongan && r.masa_kerja_tahun === target; });

        gajiOut.value = row
            ? 'Rp ' + row.gaji_pokok.toLocaleString('id-ID') + ' (masa kerja ' + target + ' th)'
            : 'Tidak ditemukan referensi untuk golongan/masa kerja ini';
    }
    golonganSelect.addEventListener('change', hitungGajiPokokLama);
    masaKerjaInput.addEventListener('input', hitungGajiPokokLama);

    if (nipInput.value) isiDariPegawai();
});
</script>

@endsection
