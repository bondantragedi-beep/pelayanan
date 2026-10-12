@php
    $F = \App\Support\FormatSurat::class;
    $kgbBerikutnya = $F::tambahTahun($v->tmt_baru, 2);

    // Masa kerja golongan pada tanggal SK terakhir = masa kerja baru dikurangi 2 tahun (siklus KGB).
    $masaKerjaLamaTahun = max(0, (int) $v->masa_kerja_tahun - 2);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan KGB PPPK - {{ $v->nama_pegawai }}</title>
    <style>
        @page { margin: 26pt 36pt 30pt 36pt; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #000; line-height: 1.18; margin: 0; }
        table.daftar { border-collapse: collapse; margin-left: 38pt; width: 502pt; }
        table.daftar td { vertical-align: top; padding: 0 0 1.5pt 0; font-size: 12pt; }
        td.no  { width: 26pt; }
        td.lbl { width: 166pt; }
        td.sep { width: 12pt; }
        td.sub { padding-left: 20pt; }
        table.huruf { border-collapse: collapse; width: 100%; }
        table.huruf td { padding: 0; vertical-align: top; font-size: 12pt; }
        table.huruf td.h { width: 18pt; }
        .justify { text-align: justify; }
    </style>
</head>
<body>

@include('pdf._kop', ['gaya' => 'pppk'])

<div style="margin-top:8pt;">Nomor : ${nomor_naskah}</div>
<div>Perihal : Surat Pemberitahuan Kenaikan Gaji Berkala</div>

<div style="margin-top:14pt; margin-left:10pt;">Yth. Kepala Badan Pengelolaan Keuangan Dan Aset Kota Makassar</div>
<div style="margin-left:34pt;">di</div>
<div style="margin-left:38pt;">Makassar</div>

<div class="justify" style="margin-top:12pt; margin-left:60pt; text-indent:48pt;">Dengan ini memberikan Kenaikan Gaji Berkala kepada Pegawai Pemerintah dengan Perjanjian Kerja di bawah ini :</div>

<table class="daftar" style="margin-top:8pt;">
    <tr><td class="no">1.</td><td class="lbl">Nama</td><td class="sep">:</td><td><b>{{ $v->nama_pegawai }}</b></td></tr>
    <tr><td class="no">2.</td><td class="lbl">NIP</td><td class="sep">:</td><td>{{ $v->nip_pegawai }}</td></tr>
    <tr><td class="no">3.</td><td class="lbl">Golongan/Jabatan</td><td class="sep">:</td><td>{{ $v->pangkat_jabatan }}</td></tr>
    <tr><td class="no">4.</td><td class="lbl">Masa Perjanjian Kerja</td><td class="sep">:</td><td>{{ $F::masaKerja((int) $v->masa_perjanjian_tahun) }}</td></tr>
    <tr><td class="no">5.</td><td class="lbl">Perpanjangan Perjanjian Kerja</td><td class="sep">:</td><td>{{ $F::tanggal($v->perjanjian_mulai, true) }} s/d {{ $F::tanggal($v->perjanjian_selesai) }}</td></tr>
    <tr><td class="no">6.</td><td class="lbl">Unit Kerja</td><td class="sep">:</td><td>{{ $v->unit_kerja }}</td></tr>
    <tr><td class="no">7.</td><td class="lbl">Gaji Lama</td><td class="sep">:</td><td><b>{{ $F::rupiah($v->gaji_pokok_lama) }}</b></td></tr>

    <tr><td></td><td class="sub" colspan="3">Atas dasar surat keputusan terakhir tentang pangkat/gaji yang ditetapkan :</td></tr>
    <tr><td></td><td class="lbl sub"><table class="huruf"><tr><td class="h">a.</td><td>Oleh pejabat</td></tr></table></td><td class="sep">:</td><td>{{ $v->sk_pejabat }}</td></tr>
    <tr><td></td><td class="lbl sub"><table class="huruf"><tr><td class="h">b.</td><td>Tanggal</td></tr></table></td><td class="sep">:</td><td>{{ $F::tanggal($v->tanggal_sk) }}</td></tr>
    <tr><td></td><td class="lbl sub"><table class="huruf"><tr><td class="h">c.</td><td>Nomor</td></tr></table></td><td class="sep">:</td><td>{{ $v->nomor_sk }}</td></tr>
    <tr><td></td><td class="lbl sub"><table class="huruf"><tr><td class="h">d.</td><td>Tanggal Berlaku Gaji Tersebut</td></tr></table></td><td class="sep">:</td><td>{{ $F::tanggal($v->sk_berlaku_gaji) }}</td></tr>
    <tr><td></td><td class="lbl sub"><table class="huruf"><tr><td class="h">e.</td><td>Masa Kerja Golongan pada tanggal tersebut</td></tr></table></td><td class="sep">:</td><td>{{ $F::masaKerja($masaKerjaLamaTahun, (int) $v->masa_kerja_bulan, true) }}</td></tr>

    <tr><td></td><td class="sub" colspan="3">Diberikan kenaikan gaji berkala hingga memperoleh :</td></tr>
    <tr><td class="no">8.</td><td class="lbl">Gaji Baru</td><td class="sep">:</td><td>{{ $F::rupiah($v->gaji_pokok_baru) }}</td></tr>
    <tr><td class="no">9.</td><td class="lbl">Berdasarkan masa kerja</td><td class="sep">:</td><td>{{ $F::masaKerja((int) $v->masa_kerja_tahun, (int) $v->masa_kerja_bulan, true) }}</td></tr>
    <tr><td class="no">10.</td><td class="lbl">Mulai Tanggal</td><td class="sep">:</td><td>{{ $F::tanggal($v->tmt_baru) }} s/d {{ $F::tanggal($kgbBerikutnya) }}</td></tr>
</table>

<div class="justify" style="margin-top:12pt; margin-left:33pt;">Berdasarkan dengan Peraturan Pemerintah Nomor 11 Tahun 2024 kepada Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) tersebut dapat dibayarkan penghasilannya berdasarkan gaji pokok yang baru.</div>

<div style="margin-left:283pt; margin-top:18pt;">
    <div>Ditetapkan di Makassar</div>
    <div>Pada tanggal : ${tanggal_naskah}</div>
    <div style="margin-top:14pt; font-weight:bold;">KEPALA DINAS</div>
    <div style="margin-top:20pt; margin-left:22pt;">${ttd_pengirim}</div>
    <div style="margin-top:26pt; font-weight:bold; text-decoration:underline;">${nama_pengirim}</div>
    <div>Pangkat : Pembina Tingkat I</div>
    <div>Nip. ${nip_pengirim}</div>
</div>

</body>
</html>
