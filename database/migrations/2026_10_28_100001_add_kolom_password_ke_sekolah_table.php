<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * wajib_ganti_password : true selama sekolah masih memakai password awal
     *                        (hasil impor / reset admin). Sekolah dipaksa menggantinya
     *                        saat login. Default true = aman bila ada jalur yang lupa mengisi.
     *                        Akun sekolah yang SUDAH ada juga ikut true: mereka diminta
     *                        mengganti password sekali pada login berikutnya.
     * password_diubah_pada : kapan sekolah terakhir mengganti password sendiri.
     */
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->boolean('wajib_ganti_password')->default(true)->after('password');
            $table->timestamp('password_diubah_pada')->nullable()->after('wajib_ganti_password');
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn(['wajib_ganti_password', 'password_diubah_pada']);
        });
    }
};
