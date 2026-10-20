<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="60">
    <title>Layar Monitoring — Dinas Pendidikan Kota Makassar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('imager/logo disdik.PNG') }}">

    <style>
        :root{
            --bg:#06131F; --panel:#0C1D2E; --line:rgba(255,255,255,0.08); --text:#EAF1F8; --muted:#8CA3B8;
            --amber:#F5B544; --green:#3DCB8B; --red:#F26A6E; --blue:#58A9EC;
            --font-head:'Plus Jakarta Sans',sans-serif; --font-body:'Inter',sans-serif;
        }
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;height:100%;background:var(--bg);font-family:var(--font-body);color:var(--text);-webkit-font-smoothing:antialiased;}
        body{padding:30px 44px 36px;}

        .topbar{
            display:flex; align-items:center; justify-content:space-between;
            padding-bottom:22px; margin-bottom:24px; border-bottom:1px solid var(--line);
        }
        .brand{display:flex; align-items:center; gap:16px;}
        .brand .emblem{
            width:54px;height:54px;border-radius:14px;background:#fff;
            display:flex;align-items:center;justify-content:center;padding:7px;overflow:hidden;
        }
        .brand .emblem img{width:100%;height:100%;object-fit:contain;}
        .brand h1{font-family:var(--font-head);font-size:22px;font-weight:800;margin:0;letter-spacing:-0.02em;}
        .brand p{font-size:13.5px;color:var(--muted);margin:4px 0 0;}

        .clock{text-align:right;}
        .clock .jam{font-family:var(--font-head);font-size:38px;font-weight:800;letter-spacing:-0.02em;line-height:1;font-variant-numeric:tabular-nums;}
        .clock .tanggal{font-size:13.5px;color:var(--muted);margin-top:7px;text-transform:capitalize;}

        .ringkasan{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:26px;}
        .kartu{
            background:var(--panel); border:1px solid var(--line); border-radius:18px;
            padding:20px 22px 18px; position:relative; overflow:hidden;
        }
        .kartu::before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:currentColor;opacity:.9;}
        .kartu .angka{font-family:var(--font-head);font-size:42px;font-weight:800;line-height:1;letter-spacing:-0.03em;font-variant-numeric:tabular-nums;}
        .kartu .label{font-size:13px;color:var(--muted);margin-top:10px;line-height:1.4;}
        .kartu.menunggu{color:var(--amber);}
        .kartu.ttd{color:var(--blue);}
        .kartu.terkirim{color:var(--green);}
        .kartu.ditolak{color:var(--red);}

        .tabel-wrap{background:var(--panel); border:1px solid var(--line); border-radius:20px; overflow:hidden;}
        table{width:100%; border-collapse:collapse;}
        thead th{
            text-align:left; font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase;
            color:var(--muted); padding:16px 22px; border-bottom:1px solid var(--line);
        }
        tbody td{padding:16px 22px; font-size:15px; border-bottom:1px solid rgba(255,255,255,0.05); vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        .sekolah-nama{font-family:var(--font-head);font-weight:700;font-size:16px;color:#fff;letter-spacing:-0.01em;}
        .sekolah-sub{display:block;font-size:12.5px;color:var(--muted);margin-top:2px;}
        .badge{
            display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:999px;
            font-size:12.5px;font-weight:600;white-space:nowrap;
        }
        .badge::before{content:'';width:7px;height:7px;border-radius:50%;background:currentColor;}
        .badge.menunggu{background:rgba(245,181,68,0.12);color:var(--amber);}
        .badge.ttd{background:rgba(88,169,236,0.13);color:var(--blue);}
        .badge.terkirim{background:rgba(61,203,139,0.12);color:var(--green);}
        .badge.ditolak{background:rgba(242,106,110,0.12);color:var(--red);}

        .kosong{padding:64px 20px;text-align:center;color:var(--muted);font-size:15px;}
        .footer-note{text-align:center;margin-top:22px;font-size:12px;color:#5F768B;}

        @media (max-width:900px){
            .ringkasan{grid-template-columns:repeat(2,1fr);}
            body{padding:20px;}
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="brand">
            <div class="emblem">
                <img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Disdik">
            </div>
            <div>
                <h1>Dinas Pendidikan Kota Makassar</h1>
                <p>Monitoring Pengajuan Layanan Kepegawaian — Layar Kantor</p>
            </div>
        </div>
        <div class="clock">
            <div class="jam" id="jam">--:--:--</div>
            <div class="tanggal" id="tanggal">Memuat tanggal...</div>
        </div>
    </div>

    <div class="ringkasan">
        <div class="kartu menunggu">
            <div class="angka">{{ $ringkasan['menunggu'] }}</div>
            <div class="label">Menunggu diproses verifikator</div>
        </div>
        <div class="kartu ttd">
            <div class="angka">{{ $ringkasan['menunggu_ttd'] }}</div>
            <div class="label">Menunggu tanda tangan pimpinan</div>
        </div>
        <div class="kartu terkirim">
            <div class="angka">{{ $ringkasan['terkirim'] }}</div>
            <div class="label">Selesai &amp; terkirim ke sekolah</div>
        </div>
        <div class="kartu ditolak">
            <div class="angka">{{ $ringkasan['ditolak'] }}</div>
            <div class="label">Ditolak</div>
        </div>
    </div>

    <div class="tabel-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">No</th>
                    <th>Sekolah</th>
                    <th>Verifikator</th>
                    <th>Layanan</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td style="color:var(--muted);">{{ $i + 1 }}</td>
                        <td>
                            <span class="sekolah-nama">{{ $row['sekolah'] }}</span>
                            <span class="sekolah-sub">Kec. {{ $row['kecamatan'] }}</span>
                        </td>
                        <td>{{ $row['verifikator'] }}</td>
                        <td>{{ $row['layanan'] }}</td>
                        <td>{{ $row['tanggal']->translatedFormat('l, d F Y H:i') }}</td>
                        <td>
                            @if ($row['tahap'] === 'pending')
                                <span class="badge menunggu">Menunggu Diproses</span>
                            @elseif ($row['tahap'] === 'menunggu_ttd')
                                <span class="badge ttd">Menunggu TTD Pimpinan</span>
                            @elseif ($row['tahap'] === 'terkirim')
                                <span class="badge terkirim">Terkirim ke Sekolah</span>
                            @else
                                <span class="badge ditolak">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="kosong">Belum ada data pengajuan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="footer-note">Halaman ini otomatis diperbarui setiap 60 detik. Terakhir dimuat: <span id="dimuat"></span></p>

    <script>
        function perbaruiJam() {
            var sekarang = new Date();
            var jamEl = document.getElementById('jam');
            var tglEl = document.getElementById('tanggal');
            var pad = function (n) { return n.toString().padStart(2, '0'); };

            jamEl.textContent = pad(sekarang.getHours()) + ':' + pad(sekarang.getMinutes()) + ':' + pad(sekarang.getSeconds());

            var opsi = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            tglEl.textContent = sekarang.toLocaleDateString('id-ID', opsi);
        }
        perbaruiJam();
        setInterval(perbaruiJam, 1000);

        document.getElementById('dimuat').textContent = new Date().toLocaleTimeString('id-ID');
    </script>

</body>
</html>
