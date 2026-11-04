@php
    use App\Support\FormatSurat;
    use App\Support\LamaCuti;
    use App\Models\VerifikasiCuti;

    $cat    = $v->catatan ?? [];
    $thn    = $cat['tahunan'] ?? [];
    $n      = $v->tahunN();
    $tanda  = fn ($aktif) => $aktif ? '<span class="cek">&#10004;</span>' : '&nbsp;';
    $jenis  = $v->jenis_cuti;
    $pejabat = config('cuti.pejabat');
    $satuan = LamaCuti::satuanDicoret($v->lama_satuan ?: 'hari');
    $pilihan = VerifikasiCuti::PILIHAN_KEPUTUSAN;

    // Baris "Tahun | Sisa | Keterangan" untuk N-2, N-1, N
    $barisTahun = [];
    foreach ([-2 => 'N-2', -1 => 'N-1', 0 => 'N'] as $selisih => $kode) {
        $i = $selisih + 2;
        $barisTahun[] = [
            'tahun' => ($thn[$i]['tahun'] ?? ($n + $selisih)) . ' (' . $kode . ')',
            'sisa'  => $thn[$i]['sisa'] ?? '',
            'ket'   => $thn[$i]['ket'] ?? '',
        ];
    }

    // dompdf tidak membaca <colgroup>, jadi lebar kolom ditetapkan lewat baris pengukur tak terlihat.
    $lebar = function (array $w) {
        $td = array_map(fn ($x) => '<td style="width:' . $x . '%;"></td>', $w);
        return '<tr class="sp">' . implode('', $td) . '</tr>';
    };

    $lain = [
        ['2. CUTI BESAR', 'besar'],
        ['3. CUTI SAKIT', 'sakit'],
        ['4. CUTI MELAHIRKAN', 'melahirkan'],
        ['5. CUTI KARENA ALASAN PENTING', 'alasan_penting'],
        ['6. CUTI DI LUAR TANGGUNGAN NEGARA', 'luar_tanggungan'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Cuti - {{ $v->nama_pegawai }}</title>
    <style>
        @page { margin: 40pt 58pt 28pt 58pt; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; line-height: 1.2; margin: 0; }
        .cek { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }
        table.f { border-collapse: collapse; width: 100%; table-layout: fixed; margin-top: 9pt; }
        table.f td, table.f th { border: 0.75pt solid #000; padding: 1.5pt 4pt; vertical-align: top; font-size: 10pt; text-align: left; word-wrap: break-word; }
        table.f th.judul { background: #b3b3b3; font-weight: bold; }
        table.f td.c { text-align: center; }
        table.f tr.sp td { border: none; padding: 0; height: 0; line-height: 0; font-size: 0; }
    </style>
</head>
<body>

<div>Makassar, ${tanggal_naskah}</div>
<div>Kepada</div>
<div style="font-weight:bold;">Yth. ${jabatan_tujuan}</div>
<div>Di</div>
<div>Tempat</div>

<div style="text-align:center; font-weight:bold; font-size:11pt; margin-top:14pt;">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>

{{-- I. DATA PEGAWAI --}}
<table class="f">
    {!! $lebar([13, 33, 17, 37]) !!}
    <tr><th class="judul" colspan="4">I.&nbsp; DATA PEGAWAI</th></tr>
    <tr><td>Nama</td><td>{{ $v->nama_pegawai }}</td><td>NIP</td><td>{{ $v->nip_pegawai }}</td></tr>
    <tr><td>Jabatan</td><td>{{ $v->jabatan }}</td><td>Masa Kerja</td><td>{{ $v->masa_kerja }}</td></tr>
    <tr><td>Unit Kerja</td><td>{{ $v->unit_kerja }}</td><td>Pangkat/ Golongan</td><td>{{ $v->pangkat_golongan }}</td></tr>
</table>

{{-- II. JENIS CUTI --}}
<table class="f">
    {!! $lebar([43, 7, 43, 7]) !!}
    <tr><th class="judul" colspan="4">II.&nbsp; JENIS CUTI YANG DIAMBIL</th></tr>
    <tr>
        <td>1. CUTI TAHUNAN</td><td class="c">{!! $tanda($jenis === 'tahunan') !!}</td>
        <td>2. CUTI BESAR</td><td class="c">{!! $tanda($jenis === 'besar') !!}</td>
    </tr>
    <tr>
        <td>3. CUTI SAKIT</td><td class="c">{!! $tanda($jenis === 'sakit') !!}</td>
        <td>4. CUTI MELAHIRKAN</td><td class="c">{!! $tanda($jenis === 'melahirkan') !!}</td>
    </tr>
    <tr>
        <td>5. CUTI KARENA ALASAN PENTING</td><td class="c">{!! $tanda($jenis === 'alasan_penting') !!}</td>
        <td>6. CUTI DI LUAR TANGGUNGAN NEGARA</td><td class="c">{!! $tanda($jenis === 'luar_tanggungan') !!}</td>
    </tr>
</table>

{{-- III. ALASAN --}}
<table class="f">
    <tr><th class="judul">III.&nbsp;ALASAN CUTI</th></tr>
    <tr><td>{{ $v->alasan }}</td></tr>
</table>

{{-- IV. LAMANYA --}}
<table class="f">
    {!! $lebar([13, 27, 16, 44]) !!}
    <tr><th class="judul" colspan="4">IV.&nbsp;LAMANYA CUTI</th></tr>
    <tr>
        <td>Selama</td>
        <td>{{ $v->lama_angka }} (@foreach ($satuan as $i => $s)@if ($s['coret'])<span style="text-decoration:line-through;">{{ $s['teks'] }}</span>@else{{ $s['teks'] }}@endif{{ $i < 2 ? '/' : '' }}@endforeach)</td>
        <td>Mulai tanggal</td>
        <td>{{ FormatSurat::tanggal($v->tanggal_mulai, true) }} s/d {{ FormatSurat::tanggal($v->tanggal_selesai, true) }}</td>
    </tr>
</table>

{{-- V. CATATAN CUTI --}}
<table class="f">
    {!! $lebar([17, 12, 21, 36, 14]) !!}
    <tr><th class="judul" colspan="5">V.&nbsp; CATATAN CUTI</th></tr>
    <tr>
        <td colspan="3">1. CUTI TAHUNAN</td>
        <td>{{ $lain[0][0] }}</td><td>{{ $cat[$lain[0][1]] ?? '' }}</td>
    </tr>
    <tr>
        <td>Tahun</td><td>Sisa</td><td>Keterangan</td>
        <td>{{ $lain[1][0] }}</td><td>{{ $cat[$lain[1][1]] ?? '' }}</td>
    </tr>
    @foreach ($barisTahun as $i => $b)
        <tr>
            <td>{{ $b['tahun'] }}</td><td>{{ $b['sisa'] }}</td><td>{{ $b['ket'] }}</td>
            <td>{{ $lain[$i + 2][0] }}</td><td>{{ $cat[$lain[$i + 2][1]] ?? '' }}</td>
        </tr>
    @endforeach
</table>

{{-- VI. ALAMAT --}}
<table class="f">
    {!! $lebar([48, 9, 43]) !!}
    <tr><th class="judul" colspan="3">VI. ALAMAT SELAMA MENJALANKAN CUTI</th></tr>
    <tr>
        <td rowspan="2" style="height:60pt;">{{ $v->alamat_cuti }}</td>
        <td>Telp</td>
        <td>{{ $v->telp }}</td>
    </tr>
    <tr>
        <td colspan="2" class="c">
            Hormat Saya,
            <div style="height:30pt;"></div>
            <div style="font-weight:bold;">{{ $v->nama_pegawai }}</div>
            <div>NIP. {{ $v->nip_pegawai }}</div>
        </td>
    </tr>
</table>

{{-- VII. PERTIMBANGAN ATASAN LANGSUNG --}}
<table class="f">
    {!! $lebar([25, 25, 25, 25]) !!}
    <tr><th class="judul" colspan="4">VII. PERTIMBANGAN ATASAN LANGSUNG</th></tr>
    <tr>
        @foreach ($pilihan as $kode => $label)<td>{{ strtoupper($label) }}</td>@endforeach
    </tr>
    <tr>
        @foreach ($pilihan as $kode => $label)<td class="c" style="height:11pt;">{!! $tanda($v->pertimbangan === $kode) !!}</td>@endforeach
    </tr>
    <tr>
        <td colspan="2" style="height:52pt;">&nbsp;</td>
        <td colspan="2">
            Kepala Sekolah<br>{{ $namaSekolah }}
            <div style="height:26pt;"></div>
            {{ $v->nama_kepsek }}
        </td>
    </tr>
</table>

{{-- VIII. KEPUTUSAN PEJABAT --}}
<table class="f">
    {!! $lebar([25, 25, 25, 25]) !!}
    <tr><th class="judul" colspan="4">VIII. KEPUTUSAN PEJABAT YANG MEMBERIKAN CUTI</th></tr>
    <tr>
        @foreach ($pilihan as $kode => $label)<td>{{ strtoupper($label) }}</td>@endforeach
    </tr>
    <tr>
        @foreach ($pilihan as $kode => $label)<td class="c" style="height:11pt;">{!! $tanda($v->keputusan === $kode) !!}</td>@endforeach
    </tr>
    <tr>
        <td colspan="2" style="height:78pt;">&nbsp;</td>
        <td colspan="2">
            {{ $pejabat['jabatan'] }}
            <div style="margin-top:10pt;">${ttd_pengirim}</div>
            <div style="margin-top:16pt; font-weight:bold;">{{ $pejabat['nama'] }}</div>
            <div>Pangkat: {{ $pejabat['pangkat'] }}</div>
            <div>Nip. {{ $pejabat['nip'] }}</div>
        </td>
    </tr>
</table>

<div style="font-size:8pt; margin-top:10pt; line-height:1.25;">
    <div>Catatan:</div>
    <div>*&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;diisi oleh pejabat yang menangani bidang kepegawaian sebelum PNS mengajukan cuti</div>
    <div>**&nbsp;&nbsp;&nbsp;&nbsp;diberi tanda centang dan alasannya</div>
    <div>N&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= Cuti tahun berjalan</div>
    <div>N-1&nbsp;= Sisa cuti 1 tahun sebelumnya</div>
    <div>N-2&nbsp;= Sisa cuti 2 tahun sebelumnya</div>
</div>

</body>
</html>
