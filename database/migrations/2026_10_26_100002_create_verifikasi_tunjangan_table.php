<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Isian verifikator saat menerima pengajuan tunjangan. Dipakai untuk
     * membuat PDF "Surat Pengantar" usul permohonan tunjangan ke BKPSDM.
     *
     * Kode ${nomor_naskah}, ${tanggal_naskah}, ${ttd_pengirim}, ${nama_pengirim},
     * ${nip_pengirim} sengaja TIDAK disimpan: dibiarkan apa adanya di PDF
     * untuk diisi sistem persuratan, sama seperti surat KGB.
     */
    public function up(): void
    {
        Schema::create('verifikasi_tunjangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan')->cascadeOnDelete();

            $table->enum('jenis_pegawai', ['PNS', 'PPPK']);
            $table->string('jenis_tunjangan', 20); // istri | suami | anak | istri_anak | suami_anak
            $table->string('anak_ke', 20)->nullable(); // hanya jika ada tunjangan anak; contoh "1" atau "1,2"

            $table->string('nip_pegawai', 18);
            $table->string('nama_pegawai');
            $table->string('pangkat_golongan');
            $table->string('jabatan');
            $table->string('unit_kerja', 200);

            $table->string('dibuat_oleh_nip', 18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_tunjangan');
    }
};
