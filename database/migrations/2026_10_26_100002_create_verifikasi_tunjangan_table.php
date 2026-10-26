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
            $table->enum('jenis_tunjangan', ['istri', 'suami', 'anak']);
            $table->unsignedTinyInteger('anak_ke')->nullable(); // hanya jika jenis_tunjangan = anak

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
