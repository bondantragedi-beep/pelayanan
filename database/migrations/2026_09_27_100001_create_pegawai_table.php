<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pegawai (guru/staf sekolah), dipakai untuk pencarian NIP oleh
     * verifikator saat mengisi form penerimaan berkas.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->string('nip', 18)->primary();
            $table->string('nama');
            $table->enum('status', ['PNS', 'PPPK'])->default('PNS');
            $table->string('npsn', 20);
            $table->string('pangkat_jabatan')->nullable();
            $table->string('golongan', 10)->nullable();
            $table->string('kecamatan')->nullable();
            $table->date('tmt')->nullable();
            $table->timestamps();

            $table->foreign('npsn')->references('npsn')->on('sekolah')->cascadeOnDelete();
            $table->index('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
