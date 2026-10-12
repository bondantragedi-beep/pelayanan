@php
    // Logo ditanam langsung (base64) supaya dompdf tidak perlu membaca file dari luar.
    $logoFile = public_path('imager/logo-kop-pemkot.jpg');
    $logoSrc  = is_file($logoFile)
        ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoFile))
        : null;

    $pppk = ($gaya ?? 'pns') === 'pppk';
@endphp

<div style="position:relative; height:{{ $pppk ? '98pt' : '78pt' }};">
    @if ($logoSrc)
        <img src="{{ $logoSrc }}" style="position:absolute; left:0; top:0; width:{{ $pppk ? '58pt' : '62pt' }};">
    @endif

    <div style="text-align:center; padding-top:{{ $pppk ? '8pt' : '4pt' }};">
        @if ($pppk)
            <div style="font-size:18pt; font-weight:bold; line-height:1.15;">PEMERINTAH KOTA MAKASSAR</div>
            <div style="font-size:16pt; font-weight:bold; line-height:1.2;">DINAS PENDIDIKAN</div>
            <div style="font-family:'Times New Roman', Times, serif; font-size:10.5pt; margin-top:5pt; line-height:1.25;">Jl. A.P. Pettarani No. 62 Kel. Tamamaung Kec. Panakkukang</div>
            <div style="font-family:'Times New Roman', Times, serif; font-size:10.5pt; line-height:1.25;">Kota Makassar 90232, Sulawesi Selatan</div>
            <div style="font-family:'Times New Roman', Times, serif; font-size:10.5pt; line-height:1.25;">Website: <u>https://disdik.makassar.go.id</u> - email : <u>disdikkotamks&#64;gmail.com</u></div>
        @else
            <div style="font-size:16pt; line-height:1.15;">PEMERINTAH KOTA MAKASSAR</div>
            <div style="font-size:14pt; line-height:1.2;">DINAS PENDIDIKAN</div>
            <div style="font-size:8.5pt; margin-top:4pt; line-height:1.25;">Jl. A.P. Pettarani No.62 Kel. Tamamaung Kec. Panakkukang</div>
            <div style="font-size:8.5pt; line-height:1.25;">Kota Makassar 90232, Sulawesi Selatan</div>
            <div style="font-size:7.5pt; font-style:italic; line-height:1.3;">Website: https://disdik.makassarkota.go.id , E-Mail : disdik&#64;makassarkota.go.id</div>
        @endif
    </div>
</div>

{{-- Garis ganda penutup kop --}}
<div style="border-top:2.6pt solid #000; border-bottom:0.8pt solid #000; height:1.6pt; margin-bottom:8pt;"></div>
