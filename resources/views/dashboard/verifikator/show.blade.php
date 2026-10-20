@extends('layout.dashboard-verifikator')

@section('title', 'Periksa Berkas | Dinas Pendidikan Kota Makassar')
@section('page-title', 'Periksa Berkas')
@section('active-menu', 'berkas-masuk')
@section('breadcrumb')
    <a href="{{ route('verifikator.dashboard') }}" style="color:inherit;text-decoration:none;">Berkas Masuk</a> / <span>{{ $pengajuan->sekolah->nama_sekolah }}</span>
@endsection

@push('styles')
<style>
    .doc-list{display:flex;flex-direction:column;gap:10px;}
    .doc-item{
        display:flex;align-items:center;gap:12px;
        padding:12px 14px;border:1px solid var(--line);border-radius:12px;background:var(--surface);
        transition:border-color .15s ease;
    }
    .doc-item:hover{border-color:var(--line-strong);}
    .doc-item .doc-icon{
        width:36px;height:36px;border-radius:10px;background:var(--blue-100);color:var(--blue-700);
        display:flex;align-items:center;justify-content:center;flex:0 0 auto;
    }
    .doc-item .doc-icon svg{width:17px;height:17px;}
    .doc-item .doc-name{font-size:13.5px;font-weight:600;color:var(--ink);flex:1 1 auto;}
    .doc-item .doc-view-btn{
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 14px;border-radius:var(--radius-sm);border:1px solid var(--line-strong);
        color:var(--blue-800);background:var(--surface);font-family:var(--font-head);font-size:12.5px;font-weight:700;
        text-decoration:none;transition:border-color .15s ease,background .15s ease;
    }
    .doc-item .doc-view-btn:hover{border-color:var(--blue-600);background:var(--blue-50);}
    .gate-note{
        display:flex;gap:10px;align-items:flex-start;
        background:var(--warn-bg);border:1px solid var(--warn-line);color:var(--warn);
        border-radius:12px;padding:11px 14px;font-size:12.5px;line-height:1.55;margin-top:16px;
    }
    .gate-note.ok{background:var(--ok-bg);border-color:var(--ok-line);color:var(--ok);}
    .gate-note svg{width:15px;height:15px;flex:0 0 auto;margin-top:1px;}
    .action-row{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;}
    .btn-accept,.btn-reject{
        height:44px;padding:0 22px;border-radius:var(--radius-field);border:none;
        color:#fff;font-family:var(--font-head);font-size:13.5px;font-weight:700;cursor:pointer;
        transition:background .15s ease,transform .1s ease;
    }
    .btn-accept{background:#1C8A59;}
    .btn-accept:hover{background:#157047;}
    .btn-reject{background:var(--red-600);}
    .btn-reject:hover{background:#A01D20;}
    .btn-accept:active,.btn-reject:active{transform:translateY(1px);}
    .btn-accept:disabled,.btn-reject:disabled{opacity:.4;cursor:not-allowed;}
    .reject-box{
        display:none;margin-top:14px;padding:16px;border:1px solid var(--danger-line);background:var(--danger-bg);border-radius:12px;
    }
    .reject-box.show{display:block;}
    .reject-box textarea{
        width:100%;min-height:92px;border-radius:var(--radius-field);border:1px solid var(--line-strong);background:#fff;
        padding:10px 12px;font-family:var(--font-body);font-size:13.5px;resize:vertical;
    }
    .reject-box textarea:focus{outline:none;border-color:var(--red-600);box-shadow:0 0 0 3px rgba(190,35,38,.16);}
</style>
@endpush

@section('content')

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 15L9 10L13 14L20 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h2>{{ $pengajuan->sekolah->nama_sekolah }} — {{ $pengajuan->labelLayanan() }}</h2>
    </div>
    <p style="font-size:12.5px;color:var(--muted);margin:0 0 4px;">NPSN {{ $pengajuan->npsn }} · Diunggah {{ $pengajuan->created_at->translatedFormat('d M Y, H:i') }}</p>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
        </div>
        <h2>Dokumen yang Diunggah</h2>
    </div>

    <div class="doc-list">
        @foreach ($pengajuan->berkas as $key => $path)
            <div class="doc-item">
                <span class="doc-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </span>
                <span class="doc-name">{{ $key === 'sk_pangkat_terakhir' ? 'SK Pangkat Terakhir' : ($key === 'sk_kgb_terakhir' ? 'SK KGB Terakhir' : $key) }}</span>
                <a href="{{ route('verifikator.berkas.dokumen', [$pengajuan, $key]) }}" target="_blank" class="doc-view-btn" data-doc-view>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.6"/></svg>
                    Lihat Dokumen
                </a>
            </div>
        @endforeach
    </div>

    @if ($pengajuan->dilihat_pada)
        <div class="gate-note ok" id="gateNote">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12.5L11 15.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
            <span>Dokumen sudah diperiksa pada {{ $pengajuan->dilihat_pada->translatedFormat('d M Y, H:i') }}. Tombol Terima/Tolak sudah bisa dipakai.</span>
        </div>
    @else
        <div class="gate-note" id="gateNote">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.2" r="0.9" fill="currentColor"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
            <span>Buka minimal satu dokumen di atas terlebih dahulu sebelum bisa Terima/Tolak.</span>
        </div>
    @endif

    <div class="action-row">
        <button type="button" class="btn-accept" id="btnAccept" {{ $pengajuan->dilihat_pada ? '' : 'disabled' }} onclick="window.location.href='{{ route('verifikator.berkas.terima', $pengajuan) }}'">
            Terima &amp; Lanjut Isi Form
        </button>
        <button type="button" class="btn-reject" id="btnReject" {{ $pengajuan->dilihat_pada ? '' : 'disabled' }} onclick="document.getElementById('rejectBox').classList.toggle('show')">
            Tolak Berkas
        </button>
    </div>

    <div class="reject-box" id="rejectBox">
        <form method="POST" action="{{ route('verifikator.berkas.tolak', $pengajuan) }}">
            @csrf
            <label style="font-size:12.5px;font-weight:700;color:var(--red-600);display:block;margin-bottom:8px;">Alasan Penolakan</label>
            <textarea name="alasan_ditolak" placeholder="Jelaskan kekurangan berkas, contoh: scan SK KGB Terakhir buram / data tidak sesuai...">{{ old('alasan_ditolak') }}</textarea>
            @error('alasan_ditolak')
                <p class="field-error">{{ $message }}</p>
            @enderror
            @error('dokumen')
                <p class="field-error">{{ $message }}</p>
            @enderror
            <div style="margin-top:12px;">
                <button type="submit" class="btn-reject">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilan optimis: begitu tautan "Lihat Dokumen" diklik, aktifkan tombol
    // Terima/Tolak di halaman ini juga (server tetap jadi penentu utama —
    // kalau ternyata belum tercatat, aksi Terima/Tolak akan ditolak balik).
    document.querySelectorAll('[data-doc-view]').forEach(function (link) {
        link.addEventListener('click', function () {
            document.getElementById('btnAccept').disabled = false;
            document.getElementById('btnReject').disabled = false;
        });
    });
});
</script>

@endsection
