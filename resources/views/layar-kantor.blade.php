<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <noscript><meta http-equiv="refresh" content="60"></noscript>
    <title>Layar Monitoring — Dinas Pendidikan Kota Makassar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('imager/logo disdik.PNG') }}">

    <style>
        :root{
            --bg:#050F1A; --panel:#0B1B2C; --panel-2:#0F2236; --line:rgba(255,255,255,.07); --line-2:rgba(255,255,255,.12);
            --text:#EAF1F8; --muted:#8CA3B8; --faint:#5F768B;
            --merah:#FF5C5C; --amber:#F5B544; --biru:#58A9EC; --hijau:#3DCB8B; --ungu:#C58BF2;
            --font-head:'Plus Jakarta Sans',system-ui,sans-serif; --font-body:'Inter',system-ui,sans-serif;
            --kolom:46px minmax(210px,2.3fr) minmax(150px,1.5fr) minmax(150px,1.5fr) minmax(150px,1.4fr) minmax(190px,1.9fr);
        }
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--bg);color:var(--text);font-family:var(--font-body);-webkit-font-smoothing:antialiased;}
        body{
            height:100vh;display:flex;flex-direction:column;padding:26px 40px 20px;overflow:hidden;
            background:
                radial-gradient(900px 420px at 0% -10%,rgba(46,111,190,.32),transparent 65%),
                radial-gradient(800px 380px at 100% 0%,rgba(61,203,139,.14),transparent 65%),
                var(--bg);
        }

        /* garis kemajuan menuju pembaruan data berikutnya */
        .bar{position:fixed;left:0;top:0;height:3px;width:100%;background:linear-gradient(90deg,#2A7CC2,#58E0A8);transform-origin:left;transform:scaleX(0);z-index:9;}
        .bar.jalan{animation:bar 60s linear forwards;}
        @keyframes bar{to{transform:scaleX(1);}}

        /* ---------- Header ---------- */
        .top{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:20px;flex:0 0 auto;}
        .brand{display:flex;align-items:center;gap:16px;}
        .emblem{width:56px;height:56px;border-radius:16px;background:#fff;display:grid;place-items:center;padding:8px;box-shadow:0 14px 30px -14px rgba(0,0,0,.8);}
        .emblem img{width:100%;height:100%;object-fit:contain;}
        .brand h1{font-family:var(--font-head);font-size:24px;font-weight:800;margin:0;letter-spacing:-.025em;}
        .brand p{font-size:13.5px;color:var(--muted);margin:5px 0 0;}
        .kanan{display:flex;align-items:center;gap:28px;}
        .live{display:inline-flex;align-items:center;gap:9px;padding:8px 15px;border-radius:999px;background:rgba(61,203,139,.12);border:1px solid rgba(61,203,139,.28);color:var(--hijau);font-size:12.5px;font-weight:700;letter-spacing:.1em;}
        .live i{width:8px;height:8px;border-radius:50%;background:currentColor;animation:denyut 1.8s ease-out infinite;}
        @keyframes denyut{0%{box-shadow:0 0 0 0 rgba(61,203,139,.7);}80%,100%{box-shadow:0 0 0 10px rgba(61,203,139,0);}}
        .jam{font-family:var(--font-head);font-size:44px;font-weight:800;letter-spacing:-.03em;line-height:1;font-variant-numeric:tabular-nums;text-align:right;}
        .tanggal{font-size:14px;color:var(--muted);margin-top:7px;text-align:right;text-transform:capitalize;}

        /* ---------- Papan ---------- */
        #papan{flex:1;min-height:0;display:flex;flex-direction:column;}

        .kpi{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:18px;flex:0 0 auto;}
        .kartu{
            position:relative;overflow:hidden;display:flex;align-items:center;gap:16px;
            background:linear-gradient(160deg,var(--panel-2),var(--panel));border:1px solid var(--line);border-radius:20px;padding:18px 20px;
            color:var(--w);
        }
        .kartu::after{content:"";position:absolute;right:-40px;top:-50px;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,var(--w),transparent 68%);opacity:.14;}
        .kartu .ik{width:46px;height:46px;border-radius:14px;display:grid;place-items:center;flex:0 0 auto;background:color-mix(in srgb,var(--w) 16%,transparent);color:var(--w);}
        .kartu .ik svg{width:23px;height:23px;}
        .kartu .angka{font-family:var(--font-head);font-size:40px;font-weight:800;line-height:1;letter-spacing:-.03em;font-variant-numeric:tabular-nums;color:#fff;}
        .kartu .label{font-size:12.5px;color:var(--muted);margin-top:6px;line-height:1.35;}
        .kartu.tertunda{--w:var(--merah);border-color:rgba(255,92,92,.35);background:linear-gradient(160deg,#2A1217,#160B10);}
        .kartu.tertunda.ada{animation:alarm 2.4s ease-in-out infinite;}
        @keyframes alarm{0%,100%{box-shadow:0 0 0 0 rgba(255,92,92,0);}50%{box-shadow:0 0 0 4px rgba(255,92,92,.22),0 0 40px -6px rgba(255,92,92,.5);}}
        .kartu.menunggu{--w:var(--amber);}
        .kartu.ttd{--w:var(--biru);}
        .kartu.terkirim{--w:var(--hijau);}
        .kartu.ditolak{--w:var(--ungu);}

        /* ---------- Daftar ---------- */
        .board{flex:1;min-height:0;display:flex;flex-direction:column;background:linear-gradient(180deg,var(--panel),#091727);border:1px solid var(--line);border-radius:24px;overflow:hidden;}
        .kepala,.baris{display:grid;grid-template-columns:var(--kolom);gap:0 16px;align-items:center;padding:0 26px;}
        .kepala{flex:0 0 auto;padding-top:15px;padding-bottom:13px;border-bottom:1px solid var(--line-2);font-size:11.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);}
        .isi{flex:1;min-height:0;overflow:hidden;}

        .grup{display:flex;align-items:center;gap:12px;padding:15px 26px 11px;position:sticky;top:0;z-index:2;background:linear-gradient(180deg,#0B1B2C 70%,rgba(11,27,44,0));}
        .grup .titik{width:10px;height:10px;border-radius:50%;background:var(--g);box-shadow:0 0 14px var(--g);}
        .grup h2{margin:0;font-family:var(--font-head);font-size:14px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--g);}
        .grup .ket{font-size:12.5px;color:var(--muted);}
        .grup .jml{margin-left:auto;font-family:var(--font-head);font-size:12.5px;font-weight:700;padding:3px 11px;border-radius:999px;background:color-mix(in srgb,var(--g) 16%,transparent);color:var(--g);}
        .grup.tertunda{--g:var(--merah);} .grup.proses{--g:var(--biru);} .grup.ditolak{--g:var(--ungu);} .grup.terkirim{--g:var(--hijau);}

        .baris{position:relative;min-height:66px;padding-top:10px;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,.045);font-size:15px;}
        .baris .no{color:var(--faint);font-family:var(--font-head);font-weight:700;font-variant-numeric:tabular-nums;}
        .sekolah{font-family:var(--font-head);font-weight:700;font-size:16px;color:#fff;letter-spacing:-.01em;line-height:1.25;}
        .sub{display:block;font-size:12.5px;color:var(--muted);margin-top:3px;}
        .layanan{display:inline-flex;align-items:center;gap:9px;font-weight:600;}
        .layanan i{width:9px;height:9px;border-radius:3px;background:var(--lc,#9AA9B8);box-shadow:0 0 10px var(--lc,#9AA9B8);flex:0 0 auto;}
        .verif{display:flex;align-items:center;gap:10px;min-width:0;}
        .avatar{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;flex:0 0 auto;font-size:11.5px;font-weight:800;font-family:var(--font-head);color:#fff;background:linear-gradient(135deg,#2A7CC2,#0E4A80);}
        .avatar.kosong-v{background:rgba(255,255,255,.08);color:var(--faint);}
        .verif span:last-child{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .tgl b{display:block;font-weight:600;font-size:14.5px;}
        .tgl small{display:block;font-size:12.5px;color:var(--muted);margin-top:3px;}

        .status{display:flex;flex-direction:column;gap:8px;align-items:flex-start;}
        .pill{display:inline-flex;align-items:center;gap:8px;padding:5px 13px;border-radius:999px;font-size:12.5px;font-weight:700;white-space:nowrap;}
        .pill::before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor;}
        .pill.pending{background:rgba(245,181,68,.13);color:var(--amber);}
        .pill.ttd{background:rgba(88,169,236,.14);color:var(--biru);}
        .pill.terkirim{background:rgba(61,203,139,.13);color:var(--hijau);}
        .pill.ditolak{background:rgba(197,139,242,.14);color:var(--ungu);}
        .langkah{display:flex;gap:4px;width:150px;}
        .langkah i{flex:1;height:5px;border-radius:3px;background:rgba(255,255,255,.1);}
        .langkah i.s{background:var(--c);}
        .langkah i.a{background:var(--c);animation:kedip 1.6s ease-in-out infinite;}
        @keyframes kedip{50%{opacity:.35;}}

        /* berkas tertunda: garis merah di tepi + latar kemerahan + label umur */
        .baris.tertunda{background:linear-gradient(90deg,rgba(255,92,92,.14),rgba(255,92,92,.02) 55%);}
        .baris.tertunda::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--merah);box-shadow:0 0 16px var(--merah);}
        .telat{display:inline-flex;align-items:center;gap:7px;padding:4px 11px;border-radius:8px;background:var(--merah);color:#fff;font-size:12px;font-weight:800;letter-spacing:.02em;margin-top:6px;}
        .telat::before{content:"";width:7px;height:7px;border-radius:50%;background:#fff;animation:kedip 1.1s ease-in-out infinite;}

        /* berkas selesai: meredup, turun ke bawah */
        .baris.terkirim{opacity:.58;}
        .baris.terkirim .sekolah{font-weight:600;}

        .kosong{padding:70px 20px;text-align:center;color:var(--muted);font-size:16px;}
        .foot{flex:0 0 auto;display:flex;justify-content:space-between;gap:20px;margin-top:12px;font-size:12px;color:var(--faint);}
        .foot b{color:var(--muted);font-weight:600;}

        /* ---------- Layar kecil: tampil biasa (bukan papan TV) ---------- */
        @media (max-width:1100px){
            body{height:auto;overflow:auto;padding:18px;}
            .kpi{grid-template-columns:repeat(2,1fr);}
            .isi{overflow:visible;}
            .kepala{display:none;}
            .baris{grid-template-columns:34px 1fr;gap:6px 12px;padding-top:14px;padding-bottom:14px;}
            .baris>div:nth-child(n+3){grid-column:2;}
            .top{flex-direction:column;align-items:flex-start;}
            .jam,.tanggal{text-align:left;}
            .kanan{width:100%;justify-content:space-between;}
        }
    </style>
</head>
<body>

    <div class="bar" id="bar"></div>

    <header class="top">
        <div class="brand">
            <div class="emblem"><img src="{{ asset('imager/logo disdik.PNG') }}" alt="Logo Disdik"></div>
            <div>
                <h1>Dinas Pendidikan Kota Makassar</h1>
                <p>Monitoring Pengajuan Layanan Kepegawaian · Layar Kantor</p>
            </div>
        </div>
        <div class="kanan">
            <span class="live"><i></i> LIVE</span>
            <div>
                <div class="jam" id="jam">--:--:--</div>
                <div class="tanggal" id="tanggal">Memuat tanggal...</div>
            </div>
        </div>
    </header>

    @php
        $warnaLayanan = ['kgb' => '#5B9DF5', 'tunjangan' => '#3FC4C4', 'cuti' => '#5EDC9B'];
        $inisial = fn ($n) => collect(preg_split('/[\s,.]+/', trim((string) $n), -1, PREG_SPLIT_NO_EMPTY))
            ->reject(fn ($k) => str_contains($k, '.'))->take(2)->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('') ?: '?';
        // [label, kelas pil, jumlah segmen selesai, ada segmen aktif/berkedip]
        $tahapUi = [
            'pending'      => ['Menunggu diperiksa',     'pending',  1, true,  'var(--amber)'],
            'menunggu_ttd' => ['Menunggu TTD pimpinan',  'ttd',      2, true,  'var(--biru)'],
            'terkirim'     => ['Terkirim ke sekolah',    'terkirim', 4, false, 'var(--hijau)'],
            'rejected'     => ['Ditolak',                'ditolak',  1, false, 'var(--ungu)'],
        ];
    @endphp

    <div id="papan">

        {{-- ============ Ringkasan ============ --}}
        <section class="kpi">
            <div class="kartu tertunda {{ $ringkasan['tertunda'] > 0 ? 'ada' : '' }}">
                <span class="ik"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="13" r="7.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 9V13L14.5 14.5M9 3H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                <div><div class="angka" data-n="{{ $ringkasan['tertunda'] }}">{{ $ringkasan['tertunda'] }}</div><div class="label">Tertunda lebih dari {{ $batas }} hari</div></div>
            </div>
            <div class="kartu menunggu">
                <span class="ik"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                <div><div class="angka" data-n="{{ $ringkasan['menunggu'] }}">{{ $ringkasan['menunggu'] }}</div><div class="label">Menunggu diproses verifikator</div></div>
            </div>
            <div class="kartu ttd">
                <span class="ik"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 18.5L9.2 17.4L18.4 8.2C19.2 7.4 19.2 6.1 18.4 5.3L18.2 5.1C17.4 4.3 16.1 4.3 15.3 5.1L6.1 14.3L5 18.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
                <div><div class="angka" data-n="{{ $ringkasan['menunggu_ttd'] }}">{{ $ringkasan['menunggu_ttd'] }}</div><div class="label">Menunggu tanda tangan pimpinan</div></div>
            </div>
            <div class="kartu terkirim">
                <span class="ik"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 12.5L11 15L16 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <div><div class="angka" data-n="{{ $ringkasan['terkirim'] }}">{{ $ringkasan['terkirim'] }}</div><div class="label">Selesai &amp; terkirim ke sekolah</div></div>
            </div>
            <div class="kartu ditolak">
                <span class="ik"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M9 9L15 15M15 9L9 15" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg></span>
                <div><div class="angka" data-n="{{ $ringkasan['ditolak'] }}">{{ $ringkasan['ditolak'] }}</div><div class="label">Ditolak</div></div>
            </div>
        </section>

        {{-- ============ Daftar berkas ============ --}}
        <section class="board">
            <div class="kepala">
                <span>No</span><span>Sekolah</span><span>Layanan</span><span>Verifikator</span><span>Tanggal masuk</span><span>Status</span>
            </div>

            <div class="isi" id="gulir">
                @forelse ($kelompok as $g)
                    <div class="grup {{ $g['kunci'] }}">
                        <span class="titik"></span>
                        <h2>{{ $g['judul'] }}</h2>
                        <span class="ket">{{ $g['ket'] }}</span>
                        <span class="jml">{{ $g['baris']->count() }} berkas</span>
                    </div>

                    @foreach ($g['baris'] as $row)
                        @php [$labelTahap, $kelasPil, $selesaiSeg, $aktif, $warnaSeg] = $tahapUi[$row['tahap']]; @endphp
                        <div class="baris {{ $g['kunci'] }}">
                            <div class="no">{{ $row['no'] }}</div>
                            <div>
                                <span class="sekolah">{{ $row['sekolah'] }}</span>
                                <span class="sub">Kec. {{ $row['kecamatan'] }}</span>
                            </div>
                            <div><span class="layanan" style="--lc: {{ $warnaLayanan[$row['jenis']] ?? '#9AA9B8' }}"><i></i>{{ $row['layanan'] }}</span></div>
                            @php $adaVerif = $row['verifikator'] !== 'Belum ada verifikator'; @endphp
                            <div class="verif"><span class="avatar {{ $adaVerif ? '' : 'kosong-v' }}">{{ $adaVerif ? $inisial($row['verifikator']) : '?' }}</span><span style="{{ $adaVerif ? '' : 'color:var(--faint);font-style:italic;' }}">{{ $row['verifikator'] }}</span></div>
                            <div class="tgl">
                                <b>{{ $row['tanggal']->translatedFormat('d M Y, H:i') }}</b>
                                <small>{{ $row['umur_teks'] }}</small>
                            </div>
                            <div class="status">
                                <span class="pill {{ $kelasPil }}">{{ $labelTahap }}</span>
                                <div class="langkah" style="--c: {{ $warnaSeg }}" title="Masuk → Diperiksa → TTD → Terkirim">
                                    @for ($s = 0; $s < 4; $s++)
                                        <i class="{{ $s < $selesaiSeg ? 's' : ($s === $selesaiSeg && $aktif ? 'a' : '') }}"></i>
                                    @endfor
                                </div>
                                @if ($g['kunci'] === 'tertunda')
                                    <span class="telat">Tertunda {{ $row['umur'] }} hari</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="kosong">Belum ada data pengajuan masuk.</div>
                @endforelse
            </div>
        </section>

        <div class="foot">
            <span>Urutan otomatis: <b>tertunda &gt; {{ $batas }} hari</b> di paling atas, berkas yang sudah <b>terkirim</b> ke sekolah turun ke bawah.</span>
            <span>Total <b>{{ $total }}</b> berkas · Diperbarui otomatis tiap 60 detik · Terakhir dimuat <b id="dimuat">{{ now()->format('H:i:s') }}</b></span>
        </div>
    </div>

    <script>
    (function () {
        var pad = function (n) { return n.toString().padStart(2, '0'); };

        /* Jam & tanggal */
        function jam() {
            var d = new Date();
            document.getElementById('jam').textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            document.getElementById('tanggal').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        jam(); setInterval(jam, 1000);

        /* Angka ringkasan naik perlahan saat tampil */
        function hitungNaik() {
            document.querySelectorAll('.angka[data-n]').forEach(function (el) {
                var target = parseInt(el.dataset.n, 10) || 0, t0 = null;
                if (!target) { return; }
                function langkah(t) {
                    if (t0 === null) { t0 = t; }
                    var p = Math.min(1, (t - t0) / 900);
                    el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) { requestAnimationFrame(langkah); }
                }
                el.textContent = '0';
                requestAnimationFrame(langkah);
            });
        }

        /* Daftar menggulir sendiri bila lebih panjang dari layar (untuk TV) */
        var gulirTimer = null;
        function mulaiGulir() {
            clearInterval(gulirTimer);
            var el = document.getElementById('gulir');
            if (!el || el.scrollHeight <= el.clientHeight + 4) { return; }
            var jeda = 0, tahan = false;
            el.onmouseenter = function () { tahan = true; };
            el.onmouseleave = function () { tahan = false; };
            gulirTimer = setInterval(function () {
                if (tahan) { return; }
                if (jeda > 0) { jeda--; return; }
                if (el.scrollTop + el.clientHeight >= el.scrollHeight - 1) {
                    jeda = 120;                                  // diam ±4 dtk di dasar, lalu kembali ke atas
                    setTimeout(function () { el.scrollTo({ top: 0, behavior: 'smooth' }); jeda = 100; }, 3500);
                    return;
                }
                el.scrollTop += 1;
            }, 38);
        }

        /* Bilah kemajuan menuju pembaruan berikutnya */
        function mulaiBar() {
            var b = document.getElementById('bar');
            b.classList.remove('jalan'); void b.offsetWidth; b.classList.add('jalan');
        }

        /* Pembaruan tanpa memuat ulang halaman (tidak berkedip di TV) */
        function segarkan() {
            fetch(location.href, { cache: 'no-store', headers: { 'X-Requested-With': 'fetch' } })
                .then(function (r) { return r.ok ? r.text() : Promise.reject(); })
                .then(function (html) {
                    var baru = new DOMParser().parseFromString(html, 'text/html').getElementById('papan');
                    if (!baru) { return; }
                    var lama = document.getElementById('gulir'), pos = lama ? lama.scrollTop : 0;
                    document.getElementById('papan').innerHTML = baru.innerHTML;
                    var g = document.getElementById('gulir');
                    if (g) { g.scrollTop = pos; }
                    hitungNaik(); mulaiGulir(); mulaiBar();
                })
                .catch(function () { /* gagal sesaat: coba lagi di putaran berikutnya */ });
        }

        hitungNaik(); mulaiBar();
        setTimeout(mulaiGulir, 4000);
        setInterval(segarkan, 60000);
    })();
    </script>

</body>
</html>
