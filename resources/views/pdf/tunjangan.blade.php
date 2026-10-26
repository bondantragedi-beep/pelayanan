<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar {{ $v->labelTunjangan() }} - {{ $v->nama_pegawai }}</title>
    <style>
        @page { margin: 22pt 40pt 30pt 36pt; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #000; line-height: 1.25; margin: 0; }
        table.surat { border-collapse: collapse; width: 100%; margin-top: 12pt; }
        table.surat th, table.surat td { border: 1pt solid #000; padding: 4pt 6pt; vertical-align: top; font-size: 12pt; }
        table.surat th { text-align: center; font-weight: bold; }
        table.identitas { border-collapse: collapse; margin-top: 8pt; }
        table.identitas td { border: none; padding: 2pt 0; vertical-align: top; font-size: 12pt; }
    </style>
</head>
<body>

@include('pdf._kop', ['gaya' => 'pns'])

<div style="text-align:right; margin-right:4pt; margin-top:4pt;">Makassar, ${tanggal_naskah}</div>

<div style="margin-top:6pt; margin-left:14pt;">Kepada</div>
<div style="margin-left:0;">Yth. Bapak Wali Kota Makassar</div>
<div style="margin-left:14pt;">c.q. BKPSDM Daerah Kota Makassar</div>
<div style="margin-left:36pt; margin-top:4pt;">di -</div>
<div style="margin-left:60pt; margin-top:4pt;">Makassar</div>

<div style="text-align:center; margin-top:22pt;">
    <div style="font-weight:bold; font-size:13pt;">SURAT PENGANTAR</div>
    <div>Nomor : ${nomor_naskah}</div>
</div>

<table class="surat">
    <thead>
        <tr>
            <th style="width:56%;">Uraian</th>
            <th style="width:16%;">Banyaknya</th>
            <th style="width:28%;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="height:150pt;">
                {{ $v->uraian() }}
                <table class="identitas">
                    <tr><td style="width:84pt;">Nama</td><td style="width:12pt;">:</td><td>{{ $v->nama_pegawai }}</td></tr>
                    <tr><td>NIP</td><td>:</td><td>{{ $v->nip_pegawai }}</td></tr>
                    <tr><td>Pangkat/Gol</td><td>:</td><td>{{ $v->pangkat_golongan }}</td></tr>
                    <tr><td>Jabatan</td><td>:</td><td>{{ $v->jabatan }}</td></tr>
                    <tr><td>Unit Kerja</td><td>:</td><td>{{ $v->unit_kerja }}</td></tr>
                </table>
            </td>
            <td>1 ( Satu )<br>rangkap</td>
            <td>Dikirim dengan hormat untuk penyelesaian selanjutnya.</td>
        </tr>
    </tbody>
</table>

<div style="margin-left:298pt; margin-top:34pt;">
    <div style="font-weight:bold;">KEPALA DINAS</div>
    <div style="margin-top:22pt; margin-left:22pt;">${ttd_pengirim}</div>
    <div style="margin-top:26pt; font-weight:bold; text-decoration:underline;">${nama_pengirim}</div>
    <div>Pangkat : Pembina Tk.I</div>
    <div>NIP. ${nip_pengirim}</div>
</div>

</body>
</html>
