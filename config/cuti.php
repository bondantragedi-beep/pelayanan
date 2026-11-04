<?php

/*
|--------------------------------------------------------------------------
| Pengaturan layanan Cuti PNS
|--------------------------------------------------------------------------
| Dasar aturan: Peraturan BKN Nomor 24 Tahun 2017 tentang Tata Cara
| Pemberian Cuti PNS.
*/

return [

    /** Hak cuti tahunan per tahun (hari kerja) — Lampiran III.A angka 2. */
    'hak_tahunan' => 12,

    /**
     * Batas lama cuti tahunan yang boleh diajukan sekolah dalam SATU pengajuan
     * (hari kerja). Dikecualikan bila "cuti karena ibadah" (umroh/haji) dicentang.
     */
    'maks_tahunan' => 12,

    /** Lama cuti melahirkan (bulan) — Lampiran III.D angka 4. */
    'lama_melahirkan_bulan' => 3,

    /** Batas maksimum tiap jenis cuti selain tahunan. Satuan: bulan. */
    'maks_bulan' => [
        'besar'          => 3,   // III.B angka 1
        'melahirkan'     => 3,   // III.D angka 4 (boleh kurang, III.D angka 8)
        'alasan_penting' => 1,   // III.E angka 6
        'sakit'          => 12,  // III.C angka 7 (1 tahun; tambahan 6 bulan perlu tim penguji kesehatan)
    ],

    /** Pejabat yang memberi cuti — tercetak di bagian VIII formulir. */
    'pejabat' => [
        'jabatan' => 'Kepala Dinas Pendidikan Kota Makassar',
        'nama'    => 'ACHI SOLEMAN, S. STP., M. SI.',
        'pangkat' => 'Pembina TK. I',
        'nip'     => '19770831 199511 2 001',
    ],

    /**
     * Hari libur nasional & cuti bersama (tidak dihitung sebagai hari kerja
     * untuk cuti tahunan). Format: 'YYYY-MM-DD' => 'keterangan'.
     *
     * PENTING: isi/perbarui tiap tahun sesuai SKB 3 Menteri tentang Hari Libur
     * Nasional dan Cuti Bersama. Hari Sabtu & Minggu sudah otomatis dikecualikan.
     * Yang sudah terisi di bawah hanya yang tanggalnya tetap + contoh akhir 2025.
     */
    'hari_libur' => [
        '2025-12-25' => 'Hari Raya Natal',
        '2025-12-26' => 'Cuti Bersama Natal',
        '2026-01-01' => 'Tahun Baru Masehi',
        '2026-06-01' => 'Hari Lahir Pancasila',
        '2026-08-17' => 'Hari Kemerdekaan RI',
        '2026-12-25' => 'Hari Raya Natal',
        '2027-01-01' => 'Tahun Baru Masehi',
        '2027-06-01' => 'Hari Lahir Pancasila',
        '2027-08-17' => 'Hari Kemerdekaan RI',
        '2027-12-25' => 'Hari Raya Natal',
        // TODO: tambahkan Idul Fitri, Idul Adha, Nyepi, Waisak, dll. dan cuti bersama sesuai SKB.
    ],
];
