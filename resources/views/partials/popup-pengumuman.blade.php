{{-- Popup pengumuman: hanya untuk sekolah yang login (guard 'sekolah'), muncul sekali per login --}}
@auth('sekolah')
@php
    $popup = \App\Models\Popup::yangAktif();
    $kunci = $popup ? 'popup_seen_' . $popup->id . '_' . $popup->updated_at->timestamp : null;
@endphp

@if($popup && !session($kunci))
    @php session([$kunci => true]); @endphp

    <div id="popupPengumuman" style="position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px">
        <div style="position:relative;max-width:560px;width:100%">
            <button type="button" onclick="document.getElementById('popupPengumuman').remove()"
                    aria-label="Tutup"
                    style="position:absolute;top:-14px;right:-14px;width:36px;height:36px;border-radius:50%;border:none;background:#fff;font-size:20px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.4)">&times;</button>
            <img src="{{ $popup->gambar_url }}" alt="{{ $popup->judul }}"
                 style="width:100%;max-height:85vh;object-fit:contain;border-radius:10px;background:#fff">
        </div>
    </div>
@endif
@endauth
