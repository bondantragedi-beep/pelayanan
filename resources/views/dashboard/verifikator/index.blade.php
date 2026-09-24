@extends('layout.dashboard-verifikator')

@section('title', 'Berkas Masuk | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Berkas Masuk')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb', 'Berkas dari sekolah di wilayah Anda')

@section('content')

<div class="mock-note">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
    <span>Data di bawah masih contoh (dummy), belum tersambung ke berkas KGB yang sungguhan diunggah sekolah. Tombol Terima/Tolak juga belum berfungsi — ini langkah berikutnya yang akan dibuat.</span>
</div>

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Menunggu diproses — Kecamatan {{ $verifikatorKecamatan ?? '—' }}</h2>
        <span class="count-pill">{{ count($berkasMasuk ?? []) }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sekolah</th>
                        <th>Layanan</th>
                        <th>Berkas</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($berkasMasuk ?? []) as $row)
                        <tr>
                            <td>
                                <span class="cell-strong">{{ $row['sekolah'] }}</span>
                                <span class="cell-sub">NPSN {{ $row['npsn'] }}</span>
                            </td>
                            <td>{{ $row['layanan'] }}</td>
                            <td>
                                <span class="cell-sub" style="display:block;">SK Pangkat Terakhir</span>
                                <span class="cell-sub" style="display:block;">SK KGB Terakhir</span>
                            </td>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>
                                @if ($row['status'] === 'pending')
                                    <span class="badge badge-pending">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        Menunggu diproses
                                    </span>
                                @elseif ($row['status'] === 'processed')
                                    <span class="badge badge-processed">Sudah diproses</span>
                                @else
                                    <span class="badge badge-rejected">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="btn-save-row" disabled title="Langkah berikutnya — belum aktif" style="background:var(--blue-700);">Terima</button>
                                    <button type="button" class="btn-save-row" disabled title="Langkah berikutnya — belum aktif" style="background:var(--red-600);">Tolak</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="table-empty">Tidak ada berkas masuk dari sekolah di wilayah Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
