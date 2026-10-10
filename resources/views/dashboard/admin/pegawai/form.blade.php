@extends('layout.dashboard-admin')

@section('title', ($mode === 'create' ? 'Tambah' : 'Ubah') . ' Pegawai | Dinas Pendidikan Kota Makassar')
@section('page-title', ($mode === 'create' ? 'Tambah Pegawai' : 'Ubah Pegawai'))
@section('active-menu', 'data-master-pegawai')
@section('breadcrumb')
    <a href="{{ route('admin.data-master.pegawai.index') }}" style="color:inherit;text-decoration:none;">Kelola Pegawai</a> / <span>{{ $mode === 'create' ? 'Tambah' : 'Ubah' }}</span>
@endsection

@section('content')

@php
    $v = fn ($n) => old($n, $pegawai->{$n} ?? '');
    $d = fn ($n) => old($n, optional($pegawai->{$n} ?? null)->toDateString());

    // Tiap baris: name, label, type, required, datalist (opsional)
    $dataPribadi = [
        ['name' => 'nama',                 'label' => 'Nama',                 'type' => 'text',   'required' => true,  'list' => null],
        ['name' => 'tempat_tanggal_lahir', 'label' => 'Tempat, Tanggal Lahir', 'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'jenis_kelamin',        'label' => 'Jenis Kelamin',        'type' => 'text',   'required' => false, 'list' => 'listJk'],
        ['name' => 'agama',                'label' => 'Agama',                'type' => 'text',   'required' => false, 'list' => 'listAgama'],
        ['name' => 'status_perkawinan',    'label' => 'Status Perkawinan',    'type' => 'text',   'required' => false, 'list' => 'listKawin'],
        ['name' => 'pendidikan_awal',      'label' => 'Pendidikan Awal',      'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'pendidikan_akhir',     'label' => 'Pendidikan Akhir',     'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'no_ktp',               'label' => 'No. KTP',              'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'no_npwp',              'label' => 'No. NPWP',             'type' => 'text',   'required' => false, 'list' => null],
    ];

    $dataKepegawaian = [
        ['name' => 'unit_kerja',       'label' => 'Unit Kerja',        'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'status_pegawai',   'label' => 'Status Pegawai',    'type' => 'text',   'required' => false, 'list' => 'listStatus'],
        ['name' => 'golongan_pangkat', 'label' => 'Golongan Pangkat',  'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'tmt_golongan',     'label' => 'TMT Golongan',      'type' => 'date',   'required' => false, 'list' => null],
        ['name' => 'nama_jabatan',     'label' => 'Nama Jabatan',      'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'tmt_jabatan',      'label' => 'TMT Jabatan',       'type' => 'date',   'required' => false, 'list' => null],
        ['name' => 'eselon',           'label' => 'Eselon',            'type' => 'text',   'required' => false, 'list' => null],
        ['name' => 'tmt_pegawai',      'label' => 'TMT Pegawai',       'type' => 'date',   'required' => false, 'list' => null],
        ['name' => 'masa_kerja_tahun', 'label' => 'Masa Kerja (Tahun)', 'type' => 'number', 'required' => false, 'list' => null],
        ['name' => 'masa_kerja_bulan', 'label' => 'Masa Kerja (Bulan)', 'type' => 'number', 'required' => false, 'list' => null],
    ];

    $dataKontak = [
        ['name' => 'telp',  'label' => 'Telp',   'type' => 'text',  'required' => false, 'list' => null],
        ['name' => 'hp',    'label' => 'HP',     'type' => 'text',  'required' => false, 'list' => null],
        ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => false, 'list' => null],
    ];
@endphp

<datalist id="listJk"><option value="Laki-laki"><option value="Perempuan"></datalist>
<datalist id="listAgama"><option value="Islam"><option value="Kristen"><option value="Katolik"><option value="Hindu"><option value="Buddha"><option value="Konghucu"></datalist>
<datalist id="listKawin"><option value="Kawin"><option value="Belum Kawin"><option value="Cerai Hidup"><option value="Cerai Mati"></datalist>
<datalist id="listStatus"><option value="PNS"><option value="CPNS"><option value="PPPK"></datalist>

<form method="POST" action="{{ $mode === 'create' ? route('admin.data-master.pegawai.store') : route('admin.data-master.pegawai.update', $pegawai) }}">
    @csrf
    @if ($mode === 'edit') @method('PUT') @endif

    {{-- ===== Data Pribadi ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.6"/><path d="M5 20C5 16.4 8.1 13.6 12 13.6C15.9 13.6 19 16.4 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>Data Pribadi</h2>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>NIP <span class="req">*</span></label>
                <input type="text" name="nip" maxlength="18"
                       value="{{ old('nip', $pegawai->nip ?? '') }}"
                       {{ $mode === 'edit' ? 'readonly style=background:var(--blue-50)' : '' }} required>
                @error('nip')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">18 digit angka, tanpa spasi.</span>
            </div>

            @foreach ($dataPribadi as $f)
                <div class="form-field">
                    <label>{{ $f['label'] }} @if ($f['required'])<span class="req">*</span>@endif</label>
                    <input type="{{ $f['type'] }}" name="{{ $f['name'] }}"
                           value="{{ $f['type'] === 'date' ? $d($f['name']) : $v($f['name']) }}"
                           @if ($f['list']) list="{{ $f['list'] }}" @endif
                           @if ($f['required']) required @endif>
                    @error($f['name'])<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div class="form-field full">
                <label>Alamat Rumah</label>
                <input type="text" name="alamat_rumah" value="{{ $v('alamat_rumah') }}">
                @error('alamat_rumah')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== Kepegawaian ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2>Data Kepegawaian</h2>
        </div>

        <div class="form-grid">
            <div class="form-field full">
                <label>Sekolah (terhubung ke NPSN)</label>
                <select name="npsn">
                    <option value="">— Belum terhubung ke sekolah —</option>
                    @foreach ($daftarSekolah as $s)
                        <option value="{{ $s->npsn }}" {{ old('npsn', $pegawai->npsn ?? '') === $s->npsn ? 'selected' : '' }}>{{ $s->nama_sekolah }} ({{ $s->npsn }})</option>
                    @endforeach
                </select>
                @error('npsn')<p class="field-error">{{ $message }}</p>@enderror
                <span class="form-hint">Wajib dipilih agar pegawai ini muncul di pencarian NIP verifikator yang menangani sekolahnya. Kalau Unit Kerja di bawah dikosongkan, akan diisi otomatis dengan nama sekolah ini.</span>
            </div>

            @foreach ($dataKepegawaian as $f)
                <div class="form-field">
                    <label>{{ $f['label'] }}</label>
                    <input type="{{ $f['type'] }}" name="{{ $f['name'] }}"
                           value="{{ $f['type'] === 'date' ? $d($f['name']) : $v($f['name']) }}"
                           @if ($f['type'] === 'number') min="0" @endif
                           @if ($f['list']) list="{{ $f['list'] }}" @endif>
                    @error($f['name'])<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </div>

    {{-- ===== Kontak ===== --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3.5" y="6" width="17" height="13" rx="2.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 7L12 13.5L20.5 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2>Kontak</h2>
        </div>

        <div class="form-grid">
            @foreach ($dataKontak as $f)
                <div class="form-field">
                    <label>{{ $f['label'] }}</label>
                    <input type="{{ $f['type'] }}" name="{{ $f['name'] }}" value="{{ $v($f['name']) }}">
                    @error($f['name'])<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.data-master.pegawai.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </div>
</form>

@endsection
