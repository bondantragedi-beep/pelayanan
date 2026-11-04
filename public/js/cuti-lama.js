/*
 * Perhitungan "Lamanya Cuti" di browser — cermin dari App\Support\LamaCuti (PHP).
 * Dipakai form sekolah dan form verifikator. Server tetap penentu akhir.
 *
 * Aturan: Cuti Tahunan dihitung HARI KERJA (tanpa Sabtu/Minggu/hari libur).
 * Jenis lain: bila tepat N bulan -> "N bulan", selain itu hari kalender.
 */
(function (w) {
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function parse(s) { if (!s) return null; var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
    function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
    function addDays(d, n) { return new Date(d.getFullYear(), d.getMonth(), d.getDate() + n); }
    function addMonths(d, n) {
        var t = new Date(d.getFullYear(), d.getMonth() + n, 1);
        var maks = new Date(t.getFullYear(), t.getMonth() + 1, 0).getDate();
        t.setDate(Math.min(d.getDate(), maks));
        return t;
    }
    function hariKerja(a, b, libur) {
        var c = 0;
        for (var d = new Date(a); d <= b; d = addDays(d, 1)) {
            var wk = d.getDay();
            if (wk === 0 || wk === 6 || (libur && libur[fmt(d)])) continue;
            c++;
        }
        return c;
    }
    function kalender(a, b) { return Math.round((b - a) / 86400000) + 1; }
    function bulanPenuh(a, b) {
        var est = (b.getFullYear() - a.getFullYear()) * 12 + (b.getMonth() - a.getMonth()) + 1;
        for (var n = Math.max(1, est - 1); n <= est + 1; n++) {
            if (fmt(addDays(addMonths(a, n), -1)) === fmt(b)) return n;
        }
        return null;
    }

    /**
     * o = { jenis, mulai:'YYYY-MM-DD', selesai, ibadah:bool, libur:{}, maksTahunan:12, maksBulan:{besar:3,...} }
     * return { kosong, salah, teks, ket }
     */
    function hitung(o) {
        var a = parse(o.mulai), b = parse(o.selesai);
        if (!o.jenis || !a || !b) return { kosong: true, salah: true, teks: '—', ket: '' };
        if (b < a) return { salah: true, teks: '—', ket: 'Tanggal selesai sebelum tanggal mulai.' };

        var kerja = hariKerja(a, b, o.libur), kal = kalender(a, b);

        if (o.jenis === 'tahunan') {
            var r = { salah: false, teks: kerja + ' hari kerja', ket: '(' + kal + ' hari kalender, tanpa Sabtu/Minggu/hari libur)' };
            if (kerja < 1) { r.salah = true; r.ket = 'Rentang ini tidak mengandung hari kerja.'; }
            else if (!o.ibadah && kerja > o.maksTahunan) {
                r.salah = true;
                r.ket = 'Melebihi batas ' + o.maksTahunan + ' hari kerja. Persingkat, atau centang cuti karena ibadah bila untuk umroh/haji.';
            }
            return r;
        }

        var bln = bulanPenuh(a, b);
        var hasil = { salah: false, teks: bln ? bln + ' bulan' : kal + ' hari', ket: bln ? '(' + kal + ' hari kalender)' : '' };
        var maks = o.maksBulan && o.maksBulan[o.jenis];
        if (maks && b > addDays(addMonths(a, maks), -1)) {
            hasil.salah = true;
            hasil.ket = 'Melebihi batas maksimum ' + maks + ' bulan untuk jenis cuti ini.';
        }
        return hasil;
    }

    w.CutiLama = { parse: parse, fmt: fmt, addDays: addDays, addMonths: addMonths, hitung: hitung };
})(window);
