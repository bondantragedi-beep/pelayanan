@php
    // Helper format tanggal Indonesia, rupiah, dll. (tidak bergantung locale aplikasi)
    $F = \App\Support\FormatSurat::class;
    $kgbBerikutnya = $F::tambahTahun($v->tmt_baru, 2);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan KGB - {{ $v->nama_pegawai }}</title>
    <style>
        @page { margin: 22pt 40pt 30pt 36pt; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #000; line-height: 1.18; margin: 0; }
        table.daftar { border-collapse: collapse; margin-left: 54pt; width: 482pt; }
        table.daftar td { vertical-align: top; padding: 0 0 1pt 0; font-size: 12pt; }
        td.no  { width: 24pt; }
        td.lbl { width: 138pt; }
        td.sep { width: 10pt; }
        .justify { text-align: justify; }
    </style>
</head>
<body>

@include('pdf._kop', ['gaya' => 'pns'])

<div style="text-align:right; margin-right:44pt; margin-top:4pt;">Makassar, ${tanggal_naskah}</div>

<div style="margin-top:14pt; margin-left:16pt;">Nomor : ${nomor_naskah}</div>
<div style="margin-left:16pt;">Perihal : Surat Pemberitahuan Kenaikan Gaji Berkala</div>

<div style="margin-top:16pt; margin-left:40pt;">Yth. Kepala Badan Pengelolaan Keuangan Dan Aset Kota Makassar</div>
<div style="margin-left:67pt;">Di</div>
<div style="margin-left:80pt;">Makassar</div>

<div class="justify" style="margin-top:14pt; margin-left:54pt; margin-right:20pt; text-indent:36pt;">Dengan ini diberitahukan bahwa berhubung telah dipenuhinya masa kerja dan syarat &ndash; syarat lainnya kepada :</div>

<table class="daftar" style="margin-top:2pt;">
    <tr><td class="no">1.</td><td class="lbl">N a m a</td><td class="sep">:</td><td><b>{{ $v->nama_pegawai }}</b></td></tr>
    <tr><td class="no">2.</td><td class="lbl">N I P</td><td class="sep">:</td><td>{{ $v->nip_pegawai }}</td></tr>
    <tr><td class="no">3.</td><td class="lbl">Pangkat / Jabatan</td><td class="sep">:</td><td>{{ $v->pangkat_jabatan }}</td></tr>
    <tr><td class="no">4.</td><td class="lbl">P a d a</td><td class="sep">:</td><td>{{ $v->unit_kerja }}</td></tr>
    <tr><td class="no">5.</td><td class="lbl">Gaji Pokok Lama</td><td class="sep">:</td><td><b>{{ $F::rupiah($v->gaji_pokok_lama) }}</b></td></tr>
    <tr><td class="no">6.</td><td class="lbl">Surat Keputusan</td><td class="sep">:</td><td>{{ $v->sk_pejabat }}</td></tr>
    <tr><td class="no">7.</td><td class="lbl">T a n g g a l</td><td class="sep">:</td><td>{{ $F::tanggal($v->tanggal_sk) }} &nbsp;&nbsp;&nbsp;&nbsp; Nomor: {{ $v->nomor_sk }}</td></tr>
    <tr><td class="no">8.</td><td class="lbl">Gaji Pokok Baru</td><td class="sep">:</td><td><b>{{ $F::rupiah($v->gaji_pokok_baru) }}</b></td></tr>
    <tr><td class="no">9.</td><td class="lbl">Berdasarkan Masa Kerja</td><td class="sep">:</td><td>{{ $F::masaKerja($v->masa_kerja_tahun) }}</td></tr>
    <tr><td class="no">10.</td><td class="lbl">Dalam Golongan</td><td class="sep">:</td><td>{{ $v->golongan }}</td></tr>
    <tr><td class="no">11.</td><td class="lbl">Mulai Tanggal</td><td class="sep">:</td><td>{{ $F::tanggal($v->tmt_baru) }} KGB y.a.d : {{ $F::tanggal($kgbBerikutnya) }}</td></tr>
    <tr><td class="no">12.</td><td class="lbl">Keterangan</td><td class="sep">:</td><td>{{ $v->keterangan }}</td></tr>
</table>

<div class="justify" style="margin-top:16pt; margin-left:54pt; margin-right:20pt; text-indent:36pt;">Berdasarkan dengan Peraturan Pemerintah Nomor 5 Tahun 2024 kepada Pegawai Negeri Sipil tersebut dapat dibayarkan penghasilannya berdasarkan gaji pokok yang baru.</div>

<div style="margin-left:298pt; margin-top:22pt;">
    <div style="font-weight:bold;">KEPALA DINAS</div>
    <div style="margin-top:22pt; margin-left:22pt;">${ttd_pengirim}</div>
    <div style="margin-top:26pt; font-weight:bold; text-decoration:underline;">${nama_pengirim}</div>
    <div>Pangkat : Pembina Tk.I</div>
    <div>NIP. ${nip_pengirim}</div>
</div>

</body>
</html>
