@extends('layout.dashboard')

@section('title', 'Daftar Berkas Saya | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Daftar Berkas Saya')
@section('active-menu', 'daftar-berkas')
@section('breadcrumb', 'Riwayat seluruh pengajuan yang pernah diunggah')

@section('content')

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Riwayat Pengajuan</h2>
        <span class="count-pill">{{ $daftarBerkas->count() }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Tanggal Diunggah</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarBerkas as $item)
                        <tr>
                            <td class="cell-strong">{{ $item->labelLayanan() }}</td>
                            <td>{{ $item->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                @if ($item->status === 'processed')
                                    <span class="badge badge-processed">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12.5L9.5 17L19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Diterima
                                    </span>
                                @elseif ($item->status === 'rejected')
                                    <span class="badge badge-rejected">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="badge badge-pending">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        Proses Pengajuan
                                    </span>
                                @endif
                            </td>
                            <td style="max-width:280px;">
                                @if ($item->status === 'rejected' && $item->alasan_ditolak)
                                    <span style="color:var(--red-600);font-size:12.5px;">{{ $item->alasan_ditolak }}</span>
                                @elseif ($item->status === 'processed')
                                    <a href="{{ route('sekolah.inbox') }}" style="font-size:12.5px;color:var(--blue-700);font-weight:600;text-decoration:none;">Lihat di Inbox →</a>
                                @else
                                    <span style="color:var(--muted);font-size:12.5px;">Menunggu diproses Verifikator</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="table-empty">Belum ada berkas yang diunggah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
