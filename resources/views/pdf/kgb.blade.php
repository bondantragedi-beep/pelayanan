<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Helvetica", sans-serif; font-size: 12px; color: #1a1a1a; }
        .kop { text-align: center; border-bottom: 3px double #0B3D63; padding-bottom: 10px; margin-bottom: 22px; }
        .kop h1 { font-size: 15px; margin: 0 0 2px; text-transform: uppercase; }
        .kop h2 { font-size: 13px; margin: 0; text-transform: uppercase; }
        .judul { text-align: center; margin-bottom: 4px; }
        .judul h3 { font-size: 13px; text-decoration: underline; margin: 0 0 2px; text-transform: uppercase; }
        .judul p { margin: 0; font-size: 11.5px; }
        .isi { margin-top: 18px; line-height: 1.7; text-align: justify; }
        table.data { width: 100%; border-collapse: collapse; margin: 14px 0; }
        table.data td { padding: 4px 6px; vertical-align: top; font-size: 12px; }
        table.data td.label { width: 200px; }
        table.data td.titik { width: 12px; }
        .ttd-block { margin-top: 46px; width: 260px; float: right; text-align: center; font-size: 12px; }
        .ttd-space { height: 70px; }
        .footer-note { clear: both; margin-top: 90px; font-size: 10px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>

    <div class="kop">
        <h1>Pemerintah Kota Makassar</h1>
        <h2>Dinas Pendidikan</h2>
    </div>

    <div class="judul">
        <h3>Surat Keputusan Kenaikan Gaji Berkala</h3>
        <p>Nomor: {{ $pengajuan->verifikasiKgb->nomor_sk }}</p>
    </div>

    <div class="isi">
        <p>Berdasarkan {{ $pengajuan->verifikasiKgb->jenis_sk }} tanggal {{ $pengajuan->verifikasiKgb->tanggal_sk->translatedFormat('d F Y') }}, dengan ini ditetapkan Kenaikan Gaji Berkala atas nama pegawai berikut:</p>

        <table class="data">
            <tr><td class="label">Nama</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->nama_pegawai }}</td></tr>
            <tr><td class="label">NIP</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->nip_pegawai }}</td></tr>
            <tr><td class="label">Pangkat / Jabatan</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->pangkat_jabatan }}</td></tr>
            <tr><td class="label">Unit Kerja</td><td class="titik">:</td><td>{{ $pengajuan->sekolah->nama_sekolah }} (NPSN {{ $pengajuan->sekolah->npsn }})</td></tr>
            <tr><td class="label">Golongan</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->golongan }}</td></tr>
            <tr><td class="label">Masa Kerja Golongan</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->masa_kerja_tahun }} tahun</td></tr>
            <tr>
                <td class="label">Gaji Pokok Lama</td><td class="titik">:</td>
                <td>{{ $pengajuan->verifikasiKgb->gaji_pokok_lama ? 'Rp ' . number_format($pengajuan->verifikasiKgb->gaji_pokok_lama, 0, ',', '.') : '—' }}</td>
            </tr>
            <tr><td class="label">Terhitung Mulai Tanggal (TMT)</td><td class="titik">:</td><td>{{ $pengajuan->verifikasiKgb->tmt_baru->translatedFormat('d F Y') }}</td></tr>
        </table>

        <p>Demikian surat keputusan ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>
    </div>

    <div class="ttd-block">
        <p>Makassar, {{ now()->translatedFormat('d F Y') }}<br>Kepala Dinas Pendidikan<br>Kota Makassar</p>
        <div class="ttd-space"></div>
        <p><strong>(&nbsp;.......................................&nbsp;)</strong><br>NIP. .......................................</p>
    </div>

    <div class="footer-note">
        Dokumen ini dicetak otomatis oleh Sistem Pelayanan Administrasi Kepegawaian — Dinas Pendidikan Kota Makassar.
        Diproses oleh verifikator NIP {{ $pengajuan->verifikasiKgb->dibuat_oleh_nip }} pada {{ $pengajuan->verifikasiKgb->created_at->translatedFormat('d F Y, H:i') }}.
    </div>

</body>
</html>
