@extends('layout.dashboard-verifikator')

@section('title', 'Riwayat Diproses | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Riwayat Diproses')
@section('active-menu', 'riwayat')
@section('breadcrumb', 'Berkas yang sudah Anda tangani')

@section('content')

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Riwayat — Kecamatan {{ $verifikatorKecamatan ?? '—' }}</h2>
        <span class="count-pill">{{ $daftar->count() }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sekolah</th>
                        <th>Layanan</th>
                        <th>Tanggal Diproses</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar as $row)
                        <tr>
                            <td>
                                <span class="cell-strong">{{ $row->sekolah->nama_sekolah }}</span>
                                <span class="cell-sub">NPSN {{ $row->npsn }}</span>
                            </td>
                            <td>{{ $row->labelLayanan() }}</td>
                            <td>{{ optional($row->diproses_pada)->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                @if ($row->tahapVerifikator() === 'terkirim')
                                    <span class="badge badge-processed">Terkirim ke sekolah</span>
                                @elseif ($row->tahapVerifikator() === 'menunggu_ttd')
                                    <span class="badge badge-pending">Menunggu TTD Pimpinan</span>
                                @else
                                    <span class="badge badge-rejected">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if ($row->tahapVerifikator() === 'menunggu_ttd')
                                    <a href="{{ route('verifikator.berkas.cetak', $row) }}" class="btn-save-row" style="text-decoration:none;display:inline-flex;">Lanjutkan</a>
                                @elseif ($row->tahapVerifikator() === 'rejected')
                                    <span class="cell-sub">{{ $row->alasan_ditolak }}</span>
                                @else
                                    <span class="cell-sub">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="table-empty">Belum ada riwayat berkas yang diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
