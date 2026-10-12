<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Isian verifikator saat menerima pengajuan KGB. Dipakai untuk membuat
     * PDF surat. Formatnya ada dua (PNS dan PPPK), jadi sebagian kolom
     * hanya terisi untuk salah satunya:
     *
     *  - PNS only : keterangan
     *  - PPPK only: sk_berlaku_gaji, perjanjian_mulai, perjanjian_selesai,
     *               masa_perjanjian_tahun
     *
     * Yang TIDAK disimpan karena bisa dihitung saat membuat PDF:
     *  - tanggal "KGB y.a.d" / "s/d"  = tmt_baru + 2 tahun
     *  - masa kerja golongan saat SK terakhir (PPPK) = masa kerja baru - 2 tahun
     *
     * Kode ${nomor_naskah}, ${tanggal_naskah}, ${ttd_pengirim}, ${nama_pengirim},
     * ${nip_pengirim} sengaja TIDAK disimpan di sini: dibiarkan apa adanya di
     * PDF untuk diisi oleh sistem persuratan lain.
     */
    public function up(): void
    {
        Schema::create('verifikasi_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan')->cascadeOnDelete();

            // Menentukan format surat (PNS / PPPK)
            $table->enum('jenis_pegawai', ['PNS', 'PPPK']);

            // Identitas pegawai
            $table->string('nip_pegawai', 18);
            $table->string('nama_pegawai');
            $table->string('pangkat_jabatan');   // PNS: "Pangkat / Jabatan" - PPPK: "Golongan/Jabatan"
            $table->string('unit_kerja', 200);

            // Surat keputusan terakhir
            $table->string('sk_pejabat', 200);   // contoh: "PRESIDEN REPUBLIK INDONESIA", "Walikota Makassar"
            $table->date('tanggal_sk');
            $table->string('nomor_sk', 100);
            $table->date('sk_berlaku_gaji')->nullable();          // PPPK

            // Golongan, masa kerja (baru) dan gaji
            $table->string('golongan', 10);
            $table->unsignedTinyInteger('masa_kerja_tahun');
            $table->unsignedTinyInteger('masa_kerja_bulan')->default(0);
            $table->unsignedBigInteger('gaji_pokok_lama');
            $table->unsignedBigInteger('gaji_pokok_baru');
            $table->date('tmt_baru');                              // "Mulai Tanggal"

            // Khusus PNS
            $table->string('keterangan')->nullable();

            // Khusus PPPK
            $table->unsignedTinyInteger('masa_perjanjian_tahun')->nullable();
            $table->date('perjanjian_mulai')->nullable();
            $table->date('perjanjian_selesai')->nullable();

            $table->string('dibuat_oleh_nip', 18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_kgb');
    }
};
