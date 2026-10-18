@extends('layout.dashboard-admin')

@section('title', 'Penugasan Sekolah | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Penugasan Sekolah')
@section('active-menu', 'penugasan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a> / <span>Penugasan Sekolah</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/penugasan.css') }}">
@endpush

@section('content')

@if ($errors->any())
    <div class="dash-alert error">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

{{-- ===== Ringkasan ===== --}}
<div class="pn-ringkas">
    <div class="pn-kartu">
        <p class="pn-angka">{{ $ringkasan['total'] }}</p>
        <p class="pn-label">Total sekolah
            @foreach ($daftarJenjang as $jenjang => $jumlah)
                <span class="pn-kecil">{{ $jenjang ?: 'Tanpa jenjang' }} {{ $jumlah }}</span>
            @endforeach
        </p>
    </div>
    <div class="pn-kartu">
        <p class="pn-angka pn-ok">{{ $ringkasan['sudah'] }}</p>
        <p class="pn-label">Sudah ditugaskan</p>
    </div>
    <div class="pn-kartu">
        <p class="pn-angka pn-warn">{{ $ringkasan['belum'] }}</p>
        <p class="pn-label">Belum ditugaskan</p>
        <div class="pn-bar"><span style="width: {{ $ringkasan['total'] ? round($ringkasan['sudah'] / $ringkasan['total'] * 100) : 0 }}%;"></span></div>
    </div>
</div>

{{-- ===== Beban kerja verifikator ===== --}}
<div class="panel">
    <div class="section-heading" style="margin-bottom:10px;">
        <h2>Beban kerja verifikator</h2>
        <span class="count-pill">{{ count($daftarVerifikator) }} verifikator</span>
    </div>
    @if (count($daftarVerifikator) === 0)
        <p class="form-hint">Belum ada akun verifikator. Tambahkan dulu lewat menu Data Master &rarr; Verifikator.</p>
    @else
        <div class="pn-beban">
            @foreach ($daftarVerifikator as $v)
                <button type="button" class="pn-beban-item" data-lihat-tugas="{{ $v['nip'] }}" title="Tampilkan sekolah milik {{ $v['nama'] }}">
                    <span class="pn-beban-nama">{{ $v['nama'] }}</span>
                    <span class="pn-beban-rinci">
                        @foreach ($v['per_jenjang'] as $jenjang => $jumlah)
                            {{ $jenjang ?: '-' }} {{ $jumlah }}@if (! $loop->last) &middot; @endif
                        @endforeach
                        @if ($v['total'] === 0) belum ada sekolah @endif
                    </span>
                    <span class="pn-beban-total">{{ $v['total'] }}</span>
                </button>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== Filter ===== --}}
<div class="panel pn-filter">
    <div class="section-heading" style="margin-bottom:12px;">
        <h2>1. Saring sekolah</h2>
        <button type="button" id="fReset" class="btn-reset">Atur ulang filter</button>
    </div>

    <div class="pn-baris">
        <div class="filter-field pn-cari">
            <label for="fCari">Cari nama atau NPSN</label>
            <input type="search" id="fCari" placeholder="Contoh: SD Negeri 5 atau 4031..." autocomplete="off">
        </div>

        <div class="filter-field">
            <label>Jenjang</label>
            <div class="pn-seg" role="group" aria-label="Jenjang">
                <button type="button" class="aktif" data-jenjang-btn="">Semua <span>{{ $ringkasan['total'] }}</span></button>
                @foreach ($daftarJenjang as $jenjang => $jumlah)
                    <button type="button" data-jenjang-btn="{{ $jenjang }}">{{ $jenjang ?: 'Tanpa jenjang' }} <span>{{ $jumlah }}</span></button>
                @endforeach
            </div>
        </div>

        <div class="filter-field">
            <label for="fStatus">Status sekolah</label>
            <select id="fStatus">
                <option value="">Negeri &amp; Swasta</option>
                <option value="Negeri">Negeri</option>
                <option value="Swasta">Swasta</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="fTugas">Penugasan</label>
            <select id="fTugas">
                <option value="">Semua</option>
                <option value="belum">Belum ditugaskan</option>
                <option value="sudah">Sudah ditugaskan</option>
                @foreach ($daftarVerifikator as $v)
                    <option value="{{ $v['nip'] }}">Milik {{ $v['nama'] }} ({{ $v['total'] }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="pn-kec">
        <span class="pn-kec-judul">Kecamatan <small>(boleh pilih lebih dari satu)</small></span>
        <div class="pn-chips">
            <button type="button" class="pn-chip aktif" id="kecSemua">Semua kecamatan</button>
            @foreach ($daftarKecamatan as $nama => $info)
                <button type="button" class="pn-chip" data-kec="{{ $nama }}">
                    {{ $nama !== '' ? $nama : 'Tanpa kecamatan' }}
                    <span class="pn-n">{{ $info['total'] }}</span>
                    @if ($info['belum'] > 0)<span class="pn-belum" title="{{ $info['belum'] }} belum ditugaskan">{{ $info['belum'] }}</span>@endif
                </button>
            @endforeach
        </div>
        <p class="form-hint">Angka abu-abu = jumlah sekolah. Angka oranye = yang belum ditugaskan.</p>
    </div>
</div>

{{-- ===== Daftar sekolah + aksi ===== --}}
<form method="POST" action="{{ route('admin.penugasan.massal') }}" id="formPenugasan">
    @csrf

    <div class="panel" style="padding:0;">
        <div class="section-heading pn-judul-tabel">
            <h2>2. Centang sekolah</h2>
            <div class="pn-info">
                <span id="infoHasil">Menampilkan {{ $ringkasan['total'] }} dari {{ $ringkasan['total'] }} sekolah</span>
                <button type="button" id="pilihTampil" class="pn-link">Centang semua yang tampil</button>
                <button type="button" id="pilihBelum" class="pn-link">Hanya yang belum ditugaskan</button>
            </div>
        </div>

        <div class="pn-tabel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="kol-cek"><input type="checkbox" id="pilihSemua" aria-label="Centang semua yang tampil"></th>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Jenjang</th>
                        <th>Kecamatan</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarSekolah as $s)
                        <tr data-row
                            data-npsn="{{ $s->npsn }}"
                            data-nama="{{ mb_strtolower($s->nama_sekolah) }}"
                            data-jenjang="{{ $s->jenjang }}"
                            data-status="{{ $s->status }}"
                            data-kec="{{ $s->kecamatan }}"
                            data-tugas="{{ $s->verifikator_nip }}">
                            <td class="kol-cek"><input type="checkbox" class="cek" name="npsn[]" value="{{ $s->npsn }}" aria-label="Pilih {{ $s->nama_sekolah }}"></td>
                            <td>{{ $s->npsn }}</td>
                            <td>
                                <span class="cell-strong">{{ $s->nama_sekolah }}</span>
                                <span class="cell-sub">{{ $s->status }}</span>
                            </td>
                            <td>{{ $s->jenjang }}</td>
                            <td>{{ $s->kecamatan ?: '—' }}</td>
                            <td>
                                @if ($s->verifikator)
                                    {{ $s->verifikator->nama }}
                                @else
                                    <span class="badge badge-unassigned">Belum ditugaskan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="table-empty">Belum ada data sekolah. Tambahkan dulu lewat menu Data Master &rarr; Sekolah.</td></tr>
                    @endforelse
                    <tr id="barisKosong" style="display:none;"><td colspan="6" class="table-empty">Tidak ada sekolah yang cocok dengan filter ini.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Bar aksi (muncul setelah ada yang dicentang) ===== --}}
    <div class="pn-aksi" id="barAksi" hidden>
        <div class="pn-aksi-kiri">
            <strong><span id="jumlahDipilih">0</span> sekolah dicentang</strong>
            <button type="button" id="hapusPilihan" class="pn-link">Batalkan centang</button>
        </div>
        <div class="pn-aksi-kanan">
            <label for="fVerifikator" class="pn-aksi-label">3. Tugaskan ke</label>
            <select name="verifikator_id" id="fVerifikator">
                <option value="">— Pilih verifikator —</option>
                @foreach ($daftarVerifikator as $v)
                    <option value="{{ $v['nip'] }}">{{ $v['nama'] }} ({{ $v['total'] }} sekolah)</option>
                @endforeach
            </select>
            <button type="submit" name="aksi" value="tugaskan" id="btnTugaskan" class="btn-primary">Tugaskan</button>
            <button type="submit" name="aksi" value="lepas" id="btnLepas" class="btn-secondary">Lepas penugasan</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
    <script src="{{ asset('js/penugasan.js') }}"></script>
@endpush
