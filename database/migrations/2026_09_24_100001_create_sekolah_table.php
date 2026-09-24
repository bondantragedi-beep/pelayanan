<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel dasar untuk login & identitas sekolah.
     *
     * Kolom npsn/nama/jenjang/status/kelurahan/kecamatan mengikuti struktur
     * database sekolah yang akan diunggah nanti. Kolom "password" ditambahkan
     * khusus supaya sekolah bisa login.
     */
    public function up(): void
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->string('npsn', 20)->primary();
            $table->string('nama_sekolah');
            $table->string('jenjang', 20)->nullable();
            $table->enum('status', ['Negeri', 'Swasta'])->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
