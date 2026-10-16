/*
 * Logika form penerimaan KGB (verifikator).
 *
 * Data dikirim dari halaman lewat window.KGB_FORM:
 *   edit         : true jika sedang mengubah isian yang sudah tersimpan
 *   pegawai      : [{nip, nama, pangkat_jabatan, golongan, unit_kerja, status_pegawai}]
 *   gaji         : [{golongan, masa_kerja_tahun, gaji_pokok}]
 *   golongan     : {PNS: [...], PPPK: [...]}
 *   golonganAwal : golongan yang sudah terisi (edit / isian lama setelah error)
 *
 * Aturan "isi otomatis": kolom yang diisi otomatis ditandai data-auto="1".
 * Begitu verifikator mengetik sendiri, tanda itu dihapus, sehingga isian
 * manualnya tidak akan ditimpa lagi.
 */
(function () {
    'use strict';

    var cfg = window.KGB_FORM || {};
    var BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function $(id) { return document.getElementById(id); }

    var el = {
        jenis: $('jenisPegawai'),
        nip: $('nipPegawai'),
        nama: $('namaPegawai'),
        pangkat: $('pangkatJabatan'),
        labelPangkat: $('labelPangkat'),
        unit: $('unitKerja'),
        golongan: $('golongan'),
        tahun: $('masaKerjaTahun'),
        bulan: $('masaKerjaBulan'),
        masaLama: $('masaKerjaLama'),
        gajiLama: $('gajiLama'),
        gajiBaru: $('gajiBaru'),
        gajiHint: $('gajiHint'),
        gajiToggle: $('gajiToggle'),
        tmt: $('tmtBaru'),
        kgb: $('kgbBerikutnya'),
        labelKgb: $('labelKgbBerikutnya'),
        pMulai: $('perjanjianMulai'),
        pSelesai: $('perjanjianSelesai'),
        masaPerjanjian: $('masaPerjanjian')
    };

    if (!el.jenis || !el.nip || !el.golongan) { return; }

    var pegawai = cfg.pegawai || [];
    var gaji = cfg.gaji || [];
    var golonganPerJenis = cfg.golongan || { PNS: [], PPPK: [] };

    // Kalau jenis sudah terisi saat halaman dibuka (mengubah data / isian lama),
    // jangan ditebak ulang dari status pegawai.
    var jenisManual = el.jenis.value !== '';

    // true bila verifikator menekan "Ubah manual" pada gaji pokok
    var gajiManual = false;

    /* ---------------- util ---------------- */

    function setAuto(input, value) {
        if (!input) { return; }
        if (input.value === '' || input.dataset.auto === '1') {
            input.value = (value === null || value === undefined) ? '' : value;
            input.dataset.auto = input.value === '' ? '' : '1';
        }
    }

    function tandaiManual(input) {
        if (!input) { return; }
        input.addEventListener('input', function () { input.dataset.auto = ''; });
        input.addEventListener('change', function () { input.dataset.auto = ''; });
    }

    function formatRupiah(n) {
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseTanggal(v) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(v || '');
        return m ? { y: +m[1], m: +m[2], d: +m[3] } : null;
    }

    function kabisat(y) { return (y % 4 === 0 && y % 100 !== 0) || y % 400 === 0; }

    function tambahTahun(t, n) {
        var y = t.y + n, m = t.m, d = t.d;
        if (m === 2 && d === 29 && !kabisat(y)) { m = 3; d = 1; }   // sama seperti perilaku PHP
        return { y: y, m: m, d: d };
    }

    function formatTanggal(t) { return t.d + ' ' + BULAN[t.m - 1] + ' ' + t.y; }

    function selisihTahun(a, b) {
        var y = b.y - a.y;
        if (b.m < a.m || (b.m === a.m && b.d < a.d)) { y--; }
        return y;
    }

    function cariGaji(golongan, tahun) {
        for (var i = 0; i < gaji.length; i++) {
            if (gaji[i].golongan === golongan && Number(gaji[i].masa_kerja_tahun) === tahun) {
                return Number(gaji[i].gaji_pokok);
            }
        }
        return null;
    }

    function sumberTabel(jenis) {
        return jenis === 'PPPK' ? 'Perpres No. 11 Tahun 2024' : 'PP No. 5 Tahun 2024';
    }

    function mkgTersedia(golongan) {
        var th = [];
        for (var i = 0; i < gaji.length; i++) {
            if (gaji[i].golongan === golongan) { th.push(Number(gaji[i].masa_kerja_tahun)); }
        }
        th.sort(function (a, b) { return a - b; });
        if (th.length === 0) { return ''; }
        return th.length <= 8 ? th.join(', ') : th.slice(0, 4).join(', ') + ', ..., ' + th[th.length - 1];
    }

    function samaDenganTabel(input, nilai) {
        return !!input && nilai !== null && input.value.replace(/\D/g, '') === String(nilai);
    }

    function kunciGaji(kunci, adaDiTabel) {
        if (el.gajiLama) { el.gajiLama.readOnly = kunci; }
        if (el.gajiBaru) { el.gajiBaru.readOnly = kunci; }
        if (el.gajiToggle) {
            el.gajiToggle.style.display = adaDiTabel ? '' : 'none';
            el.gajiToggle.textContent = kunci ? 'Ubah manual' : 'Kembali ke otomatis';
        }
    }

    /* ---------------- tampilan sesuai jenis ---------------- */

    function bangunGolongan(jenis, pilih) {
        var daftar = golonganPerJenis[jenis] || [];
        var html = '<option value="">\u2014 Pilih \u2014</option>';
        for (var i = 0; i < daftar.length; i++) {
            html += '<option value="' + daftar[i] + '">' + daftar[i] + '</option>';
        }
        el.golongan.innerHTML = html;
        el.golongan.value = daftar.indexOf(pilih) >= 0 ? pilih : '';
    }

    function terapkanJenis() {
        var jenis = el.jenis.value;

        var blok = document.querySelectorAll('[data-only]');
        for (var i = 0; i < blok.length; i++) {
            blok[i].style.display = (blok[i].getAttribute('data-only') === jenis) ? '' : 'none';
        }

        var catatan = $('catatanPilihJenis');
        if (catatan) { catatan.style.display = jenis ? 'none' : 'block'; }

        if (el.labelPangkat) {
            el.labelPangkat.textContent = jenis === 'PPPK' ? 'Golongan / Jabatan' : 'Pangkat / Jabatan';
        }
        if (el.labelKgb) {
            el.labelKgb.textContent = jenis === 'PPPK'
                ? 'Sampai dengan (otomatis, +2 tahun)'
                : 'KGB y.a.d (otomatis, +2 tahun)';
        }

        var terpilih = el.golongan.value || cfg.golonganAwal || '';
        var auto = el.golongan.dataset.auto === '1';
        bangunGolongan(jenis, terpilih);
        el.golongan.dataset.auto = (auto && el.golongan.value) ? '1' : '';

        hitung();
    }

    /* ---------------- perhitungan ---------------- */

    function hitung() {
        var jenis = el.jenis.value;
        var gol = el.golongan.value;
        var th = parseInt(el.tahun.value, 10);
        var bl = parseInt(el.bulan ? el.bulan.value : '0', 10) || 0;
        var adaMasaKerja = !isNaN(th);

        // Masa kerja golongan saat SK terakhir = masa kerja baru - 2 tahun
        if (el.masaLama) {
            el.masaLama.value = adaMasaKerja ? (Math.max(th - 2, 0) + ' Tahun ' + bl + ' Bulan') : '';
        }

        // Gaji lama = (golongan, MKG - 2) ; gaji baru = (golongan, MKG) pada tabel gaji resmi
        var lama = (gol && adaMasaKerja) ? cariGaji(gol, th - 2) : null;
        var baru = (gol && adaMasaKerja) ? cariGaji(gol, th) : null;
        var lengkap = lama !== null && baru !== null;

        if (lengkap) {
            // nilai yang sama persis dengan tabel dianggap "otomatis" (juga saat membuka data tersimpan)
            if (samaDenganTabel(el.gajiLama, lama)) { el.gajiLama.dataset.auto = '1'; }
            if (samaDenganTabel(el.gajiBaru, baru)) { el.gajiBaru.dataset.auto = '1'; }
        } else {
            gajiManual = false;
        }

        setAuto(el.gajiLama, lama !== null ? formatRupiah(lama) : '');
        setAuto(el.gajiBaru, baru !== null ? formatRupiah(baru) : '');

        var terkunci = lengkap && !gajiManual &&
            el.gajiLama.dataset.auto === '1' && el.gajiBaru.dataset.auto === '1';
        kunciGaji(terkunci, lengkap);

        if (el.gajiHint) {
            if (!jenis || !gol || !adaMasaKerja) {
                el.gajiHint.textContent = '';
            } else if (lengkap) {
                var sesuai = samaDenganTabel(el.gajiLama, lama) && samaDenganTabel(el.gajiBaru, baru);
                var awal = terkunci ? 'Gaji terisi otomatis. '
                    : (sesuai ? 'Gaji dibuka untuk diubah manual. ' : 'Gaji diisi manual dan berbeda dari tabel resmi. ');
                el.gajiHint.textContent = awal + 'Sumber: ' + sumberTabel(jenis) + ', golongan ' + gol +
                    ' (MKG ' + (th - 2) + ' = Rp ' + formatRupiah(lama) + '; MKG ' + th + ' = Rp ' + formatRupiah(baru) + ').';
            } else {
                var tersedia = mkgTersedia(gol);
                el.gajiHint.textContent = 'Golongan ' + gol + ' dengan MKG ' + th + ' (atau ' + (th - 2) + ') tidak ada di tabel ' +
                    sumberTabel(jenis) + '.' + (tersedia ? ' MKG yang tersedia: ' + tersedia + '.' : '') +
                    ' Periksa kembali MKG, atau isi gaji manual.';
            }
        }

        // Tanggal KGB berikutnya = Mulai Tanggal + 2 tahun
        if (el.kgb) {
            var t = parseTanggal(el.tmt ? el.tmt.value : '');
            el.kgb.value = t ? formatTanggal(tambahTahun(t, 2)) : '';
        }
    }

    function hitungMasaPerjanjian() {
        if (!el.pMulai || !el.pSelesai || !el.masaPerjanjian) { return; }
        var a = parseTanggal(el.pMulai.value);
        var b = parseTanggal(el.pSelesai.value);
        if (a && b) {
            var y = selisihTahun(a, b);
            setAuto(el.masaPerjanjian, y >= 0 ? String(y) : '');
        }
    }

    /* ---------------- pilih pegawai lewat NIP ---------------- */

    function jenisDariStatus(status) {
        var s = String(status || '').toUpperCase();
        if (s === '') { return ''; }
        return /PPPK|P3K/.test(s) ? 'PPPK' : 'PNS';
    }

    function isiDariPegawai() {
        var nip = el.nip.value.replace(/\D/g, '');
        var p = null;
        for (var i = 0; i < pegawai.length; i++) {
            if (pegawai[i].nip === nip) { p = pegawai[i]; break; }
        }

        if (!p) {
            if (el.nama) { el.nama.value = ''; }
            return;
        }

        if (el.nama) { el.nama.value = p.nama; }

        // Tebak jenis (PNS/PPPK) dari status pegawai, kecuali verifikator sudah memilih sendiri.
        var tebakan = jenisDariStatus(p.status_pegawai);
        if (!jenisManual && tebakan && el.jenis.value !== tebakan) {
            el.jenis.value = tebakan;
            terapkanJenis();
        }

        setAuto(el.pangkat, p.pangkat_jabatan);
        setAuto(el.unit, p.unit_kerja);

        // Golongan: hanya diisi kalau ada di daftar jenis yang dipilih
        var daftar = golonganPerJenis[el.jenis.value] || [];
        if (p.golongan && daftar.indexOf(p.golongan) >= 0 &&
            (el.golongan.value === '' || el.golongan.dataset.auto === '1')) {
            el.golongan.value = p.golongan;
            el.golongan.dataset.auto = '1';
        }

        hitung();
    }

    /* ---------------- pasang event ---------------- */

    [el.pangkat, el.unit, el.gajiLama, el.gajiBaru, el.masaPerjanjian].forEach(tandaiManual);
    tandaiManual(el.golongan);

    el.jenis.addEventListener('change', function () {
        jenisManual = true;
        terapkanJenis();
    });

    el.nip.addEventListener('input', isiDariPegawai);
    el.nip.addEventListener('change', isiDariPegawai);

    if (el.gajiToggle) {
        el.gajiToggle.addEventListener('click', function () {
            if (el.gajiLama.readOnly) {
                // sedang terkunci -> buka untuk diubah manual
                gajiManual = true;
                el.gajiLama.dataset.auto = '';
                el.gajiBaru.dataset.auto = '';
            } else {
                // sedang terbuka -> kembali ke nilai tabel (menimpa isian manual)
                gajiManual = false;
                el.gajiLama.dataset.auto = '1';
                el.gajiBaru.dataset.auto = '1';
            }
            hitung();
        });
    }

    el.golongan.addEventListener('change', hitung);
    el.tahun.addEventListener('input', hitung);
    if (el.bulan) { el.bulan.addEventListener('input', hitung); }
    if (el.tmt) { el.tmt.addEventListener('input', hitung); el.tmt.addEventListener('change', hitung); }
    if (el.pMulai) { el.pMulai.addEventListener('change', hitungMasaPerjanjian); el.pMulai.addEventListener('input', hitungMasaPerjanjian); }
    if (el.pSelesai) { el.pSelesai.addEventListener('change', hitungMasaPerjanjian); el.pSelesai.addEventListener('input', hitungMasaPerjanjian); }

    /* ---------------- keadaan awal ---------------- */

    terapkanJenis();
    if (el.nip.value) { isiDariPegawai(); }
})();
