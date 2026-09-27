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
            --blue-950:#071A2C; --blue-900:#0B2942; --blue-800:#0F3B5F; --blue-700:#145A8A;
            --blue-500:#2E86C8; --amber:#F2B84B; --green:#33C27F; --red:#E15A5F; --line:rgba(255,255,255,0.09);
            --font-head:'Plus Jakarta Sans',sans-serif; --font-body:'Inter',sans-serif;
        }
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;height:100%;background:var(--blue-950);font-family:var(--font-body);color:#EAF1F8;}
        body{
            background:
                radial-gradient(900px 400px at 15% -5%, rgba(46,134,200,0.18), transparent 60%),
                radial-gradient(700px 340px at 100% 0%, rgba(216,67,74,0.10), transparent 55%),
                var(--blue-950);
            padding:28px 40px 36px;
        }

        .topbar{
            display:flex; align-items:center; justify-content:space-between;
            padding-bottom:20px; margin-bottom:22px; border-bottom:1px solid var(--line);
        }
        .brand{display:flex; align-items:center; gap:16px;}
        .brand .emblem{
            width:56px;height:56px;border-radius:50%;background:#fff;
            display:flex;align-items:center;justify-content:center;padding:6px;overflow:hidden;
            box-shadow:0 10px 26px -8px rgba(0,0,0,0.5);
        }
        .brand .emblem img{width:100%;height:100%;object-fit:contain;}
        .brand h1{font-family:var(--font-head);font-size:22px;font-weight:800;margin:0;letter-spacing:-0.01em;}
        .brand p{font-size:13px;color:#9FB4C6;margin:3px 0 0;}

        .clock{text-align:right;}
        .clock .jam{font-family:var(--font-head);font-size:34px;font-weight:800;letter-spacing:0.02em;line-height:1;}
        .clock .tanggal{font-size:13.5px;color:#9FB4C6;margin-top:6px;text-transform:capitalize;}

        .ringkasan{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:26px;}
        .kartu{
            background:rgba(255,255,255,0.04); border:1px solid var(--line); border-radius:16px;
            padding:18px 20px;
        }
        .kartu .angka{font-family:var(--font-head);font-size:36px;font-weight:800;line-height:1;}
        .kartu .label{font-size:12.5px;color:#9FB4C6;margin-top:8px;letter-spacing:0.02em;}
        .kartu.menunggu .angka{color:var(--amber);}
        .kartu.ttd .angka{color:var(--blue-500);}
        .kartu.terkirim .angka{color:var(--green);}
        .kartu.ditolak .angka{color:var(--red);}

        .tabel-wrap{
            background:rgba(255,255,255,0.03); border:1px solid var(--line); border-radius:20px;
            overflow:hidden;
        }
        table{width:100%; border-collapse:collapse;}
        thead th{
            text-align:left; font-size:12.5px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase;
            color:#8FA6BB; padding:16px 22px; background:rgba(255,255,255,0.03); border-bottom:1px solid var(--line);
        }
        tbody td{
            padding:16px 22px; font-size:15px; border-bottom:1px solid var(--line); vertical-align:middle;
        }
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:nth-child(odd){background:rgba(255,255,255,0.015);}
        .sekolah-nama{font-family:var(--font-head);font-weight:700;font-size:16px;color:#fff;}
        .sekolah-sub{display:block;font-size:12.5px;color:#8FA6BB;margin-top:2px;}
        .badge{
            display:inline-flex;align-items:center;gap:7px;padding:6px 14px;border-radius:999px;
            font-size:12.5px;font-weight:700;white-space:nowrap;
        }
        .badge::before{content:'';width:8px;height:8px;border-radius:50%;background:currentColor;}
        .badge.menunggu{background:rgba(242,184,75,0.14);color:var(--amber);}
        .badge.ttd{background:rgba(46,134,200,0.16);color:#5CADEF;}
        .badge.terkirim{background:rgba(51,194,127,0.14);color:var(--green);}
        .badge.ditolak{background:rgba(225,90,95,0.14);color:var(--red);}

        .kosong{padding:60px 20px;text-align:center;color:#8FA6BB;font-size:15px;}

        .footer-note{
            text-align:center;margin-top:20px;font-size:12px;color:#6E8298;
        }

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
                        <td style="color:#8FA6BB;">{{ $i + 1 }}</td>
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
