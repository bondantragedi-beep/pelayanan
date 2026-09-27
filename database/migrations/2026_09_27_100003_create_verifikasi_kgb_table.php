<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detail hasil pengisian verifikator setelah sebuah pengajuan KGB
     * diterima: data pegawai, SK, masa kerja, gaji pokok lama, golongan,
     * dan TMT baru. Satu pengajuan hanya punya satu detail verifikasi.
     */
    public function up(): void
    {
        Schema::create('verifikasi_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan')->cascadeOnDelete();

            $table->string('nip_pegawai', 18);
            $table->string('nama_pegawai');
            $table->string('pangkat_jabatan')->nullable();

            $table->string('jenis_sk');
            $table->string('nomor_sk');
            $table->date('tanggal_sk');

            $table->unsignedTinyInteger('masa_kerja_tahun');
            $table->unsignedBigInteger('gaji_pokok_lama')->nullable();

            $table->string('golongan');
            $table->date('tmt_baru');

            $table->string('dibuat_oleh_nip', 18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_kgb');
    }
};
