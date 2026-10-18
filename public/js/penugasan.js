/*
 * Halaman Admin > Penugasan Sekolah.
 *
 * Semua sekolah sudah ada di tabel; skrip ini hanya menyembunyikan/menampilkan
 * baris sesuai filter dan mengatur centang. Aturan pentingnya:
 *   - Centang SELALU sama dengan yang terlihat: baris yang tersembunyi karena
 *     filter otomatis dilepas centangnya. Jadi yang ditugaskan = yang kelihatan.
 *   - Sebelum menyimpan, jumlah sekolah dan nama verifikator ditanyakan lagi.
 *   - Filter diingat (sessionStorage) supaya setelah menyimpan, daftar tetap
 *     tersaring seperti sebelumnya.
 */
(function () {
    'use strict';

    function $(id) { return document.getElementById(id); }

    var form = $('formPenugasan');
    if (!form) { return; }

    var baris = Array.prototype.slice.call(document.querySelectorAll('tr[data-row]'));
    var el = {
        cari: $('fCari'), status: $('fStatus'), tugas: $('fTugas'),
        kecSemua: $('kecSemua'), reset: $('fReset'),
        info: $('infoHasil'), kosong: $('barisKosong'),
        pilihSemua: $('pilihSemua'), pilihTampil: $('pilihTampil'), pilihBelum: $('pilihBelum'),
        bar: $('barAksi'), jumlah: $('jumlahDipilih'), batal: $('hapusPilihan'),
        verifikator: $('fVerifikator'), btnTugaskan: $('btnTugaskan'), btnLepas: $('btnLepas')
    };
    var segBtn = Array.prototype.slice.call(document.querySelectorAll('[data-jenjang-btn]'));
    var chip = Array.prototype.slice.call(document.querySelectorAll('[data-kec]')).filter(function (b) { return b.tagName === 'BUTTON'; });

    var KUNCI = 'penugasan.filter';
    var f = { q: '', jenjang: '', status: '', tugas: '', kec: [] };

    /* ---------------- filter ---------------- */

    function cocok(tr) {
        var d = tr.dataset;
        if (f.jenjang !== '' && d.jenjang !== f.jenjang) { return false; }
        if (f.status !== '' && d.status !== f.status) { return false; }
        if (f.kec.length > 0 && f.kec.indexOf(d.kec) < 0) { return false; }

        if (f.tugas === 'belum' && d.tugas !== '') { return false; }
        if (f.tugas === 'sudah' && d.tugas === '') { return false; }
        if (f.tugas !== '' && f.tugas !== 'belum' && f.tugas !== 'sudah' && d.tugas !== f.tugas) { return false; }

        var kata = f.q.trim().toLowerCase().split(/\s+/).filter(Boolean);
        if (kata.length > 0) {
            var jerami = (d.nama || '') + ' ' + (d.npsn || '');
            for (var i = 0; i < kata.length; i++) {
                if (jerami.indexOf(kata[i]) < 0) { return false; }
            }
        }
        return true;
    }

    function cekDi(tr) { return tr.querySelector('input.cek'); }

    function terapkan() {
        var tampil = 0, dipilih = 0, tampilTercentang = 0;

        baris.forEach(function (tr) {
            var ok = cocok(tr);
            var cek = cekDi(tr);
            tr.style.display = ok ? '' : 'none';

            if (!ok && cek.checked) { cek.checked = false; }   // yang tersembunyi tidak boleh ikut tersimpan
            tr.classList.toggle('dipilih', cek.checked);

            if (ok) { tampil++; if (cek.checked) { tampilTercentang++; } }
            if (cek.checked) { dipilih++; }
        });

        if (el.info) { el.info.textContent = 'Menampilkan ' + tampil + ' dari ' + baris.length + ' sekolah'; }
        if (el.kosong) { el.kosong.style.display = (baris.length > 0 && tampil === 0) ? '' : 'none'; }

        if (el.pilihSemua) {
            el.pilihSemua.checked = tampil > 0 && tampilTercentang === tampil;
            el.pilihSemua.indeterminate = tampilTercentang > 0 && tampilTercentang < tampil;
        }

        if (el.jumlah) { el.jumlah.textContent = String(dipilih); }
        if (el.bar) { el.bar.hidden = dipilih === 0; }

        var adaTujuan = !!(el.verifikator && el.verifikator.value);
        if (el.btnTugaskan) {
            el.btnTugaskan.disabled = dipilih === 0 || !adaTujuan;
            el.btnTugaskan.title = adaTujuan ? '' : 'Pilih verifikator tujuan dulu';
        }
        if (el.btnLepas) { el.btnLepas.disabled = dipilih === 0; }

        segBtn.forEach(function (b) { b.classList.toggle('aktif', b.getAttribute('data-jenjang-btn') === f.jenjang); });
        chip.forEach(function (b) { b.classList.toggle('aktif', f.kec.indexOf(b.getAttribute('data-kec')) >= 0); });
        if (el.kecSemua) { el.kecSemua.classList.toggle('aktif', f.kec.length === 0); }
    }

    function simpan() {
        try { sessionStorage.setItem(KUNCI, JSON.stringify(f)); } catch (e) { /* abaikan */ }
    }

    function sinkronKontrol() {
        if (el.cari) { el.cari.value = f.q; }
        if (el.status) { el.status.value = f.status; }
        if (el.tugas) { el.tugas.value = f.tugas; }
    }

    function ubah() { terapkan(); simpan(); }

    function pulihkan() {
        try {
            var s = JSON.parse(sessionStorage.getItem(KUNCI) || 'null');
            if (!s || typeof s !== 'object') { return; }
            f.q = typeof s.q === 'string' ? s.q : '';
            f.jenjang = typeof s.jenjang === 'string' ? s.jenjang : '';
            f.status = typeof s.status === 'string' ? s.status : '';
            f.tugas = typeof s.tugas === 'string' ? s.tugas : '';
            var ada = chip.map(function (b) { return b.getAttribute('data-kec'); });
            f.kec = Array.isArray(s.kec) ? s.kec.filter(function (k) { return ada.indexOf(k) >= 0; }) : [];
            // nilai pilihan yang sudah tidak ada di dropdown dibuang
            if (el.tugas && !Array.prototype.some.call(el.tugas.options, function (o) { return o.value === f.tugas; })) { f.tugas = ''; }
            if (!segBtn.some(function (b) { return b.getAttribute('data-jenjang-btn') === f.jenjang; })) { f.jenjang = ''; }
        } catch (e) { /* abaikan */ }
    }

    /* ---------------- pemilihan ---------------- */

    function centangTampil(nilai) {
        baris.forEach(function (tr) {
            if (tr.style.display !== 'none') { cekDi(tr).checked = nilai; }
        });
        terapkan();
    }

    baris.forEach(function (tr) {
        tr.addEventListener('click', function (e) {
            var t = e.target;
            if (t && (t.tagName === 'INPUT' || t.tagName === 'A' || t.tagName === 'BUTTON')) { return; }
            var cek = cekDi(tr);
            cek.checked = !cek.checked;
            terapkan();
        });
        cekDi(tr).addEventListener('change', terapkan);
    });

    if (el.pilihSemua) { el.pilihSemua.addEventListener('change', function () { centangTampil(el.pilihSemua.checked); }); }
    if (el.pilihTampil) { el.pilihTampil.addEventListener('click', function () { centangTampil(true); }); }
    if (el.batal) { el.batal.addEventListener('click', function () { baris.forEach(function (tr) { cekDi(tr).checked = false; }); terapkan(); }); }
    if (el.pilihBelum) {
        el.pilihBelum.addEventListener('click', function () {
            f.tugas = 'belum'; sinkronKontrol(); terapkan(); simpan();
            centangTampil(true);
        });
    }

    /* ---------------- kontrol filter ---------------- */

    if (el.cari) { el.cari.addEventListener('input', function () { f.q = el.cari.value; ubah(); }); }
    if (el.status) { el.status.addEventListener('change', function () { f.status = el.status.value; ubah(); }); }
    if (el.tugas) { el.tugas.addEventListener('change', function () { f.tugas = el.tugas.value; ubah(); }); }

    segBtn.forEach(function (b) {
        b.addEventListener('click', function () { f.jenjang = b.getAttribute('data-jenjang-btn'); ubah(); });
    });

    chip.forEach(function (b) {
        b.addEventListener('click', function () {
            var k = b.getAttribute('data-kec');
            var i = f.kec.indexOf(k);
            if (i >= 0) { f.kec.splice(i, 1); } else { f.kec.push(k); }
            ubah();
        });
    });
    if (el.kecSemua) { el.kecSemua.addEventListener('click', function () { f.kec = []; ubah(); }); }

    if (el.reset) {
        el.reset.addEventListener('click', function () {
            f = { q: '', jenjang: '', status: '', tugas: '', kec: [] };
            sinkronKontrol(); ubah();
        });
    }

    // Klik pada kartu beban kerja verifikator = tampilkan sekolah miliknya
    Array.prototype.forEach.call(document.querySelectorAll('[data-lihat-tugas]'), function (b) {
        b.addEventListener('click', function () {
            f.tugas = b.getAttribute('data-lihat-tugas');
            sinkronKontrol(); ubah();
            if (form.scrollIntoView) { form.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    /* ---------------- simpan: konfirmasi dulu ---------------- */

    function namaVerifikator() {
        var o = el.verifikator && el.verifikator.options[el.verifikator.selectedIndex];
        return o ? o.textContent.replace(/\s*\(\d+ sekolah\)\s*$/, '').trim() : '';
    }

    function jumlahDipilih() {
        return baris.filter(function (tr) { return cekDi(tr).checked; }).length;
    }

    if (el.verifikator) { el.verifikator.addEventListener('change', terapkan); }

    if (el.btnTugaskan) {
        el.btnTugaskan.addEventListener('click', function (e) {
            var n = jumlahDipilih();
            if (n === 0 || !el.verifikator.value) { e.preventDefault(); return; }
            if (!window.confirm('Tugaskan ' + n + ' sekolah ke ' + namaVerifikator() + '?')) { e.preventDefault(); }
        });
    }
    if (el.btnLepas) {
        el.btnLepas.addEventListener('click', function (e) {
            var n = jumlahDipilih();
            if (n === 0) { e.preventDefault(); return; }
            if (!window.confirm('Lepaskan penugasan ' + n + ' sekolah? Sekolah-sekolah itu akan kembali berstatus "belum ditugaskan".')) { e.preventDefault(); }
        });
    }
    form.addEventListener('submit', function (e) {
        if (jumlahDipilih() === 0) { e.preventDefault(); }
    });

    /* ---------------- keadaan awal ---------------- */

    pulihkan();
    sinkronKontrol();
    terapkan();
})();
