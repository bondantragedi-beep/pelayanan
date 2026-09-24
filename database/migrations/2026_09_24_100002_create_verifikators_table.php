<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel dasar untuk login & identitas verifikator.
     *
     * "is_admin" menandai akun verifikator yang juga berperan sebagai admin
     * (sesuai arahan: admin login memakai akun verifikator dengan NIP yang sama).
     * "kecamatan" dipakai untuk membatasi wilayah sekolah/pegawai yang boleh
     * ditangani verifikator tsb.
     */
    public function up(): void
    {
        Schema::create('verifikators', function (Blueprint $table) {
            $table->string('nip', 18)->primary();
            $table->string('nama');
            $table->string('kecamatan')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikators');
    }
};
