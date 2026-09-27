@extends('layout.dashboard')

@section('title', 'Inbox | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Inbox')
@section('active-menu', 'inbox')
@section('breadcrumb', 'Berkas yang sudah ditandatangani pimpinan')

@section('content')

<div class="panel" style="padding:0;">
    <div class="section-heading" style="padding:20px 22px 0;">
        <h2>Berkas Selesai Diproses</h2>
        <span class="count-pill">{{ $inbox->count() }} berkas</span>
    </div>

    <div class="table-wrap" style="border:none;border-radius:0;margin-top:14px;">
        <div class="table-wrap-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inbox as $item)
                        <tr>
                            <td class="cell-strong">{{ $item->labelLayanan() }}</td>
                            <td>{{ optional($item->diproses_pada)->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge badge-processed">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12.5L9.5 17L19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Siap diunduh
                                </span>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $item->hasil_path) }}" target="_blank" class="btn-save-row" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;"><path d="M12 4V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 11.5L12 16L16.5 11.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 18V19C4 19.6 4.4 20 5 20H19C19.6 20 20 19.6 20 19V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    Unduh PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="table-empty">Belum ada berkas yang selesai diproses dan dikirim ke sekolah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
