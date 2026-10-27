<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tunjangan bisa diajukan untuk lebih dari satu anak (maks. 3), jadi
     * anak_ke disimpan sebagai teks, contoh: "1" atau "1,2". Data lama (angka) tetap terbaca.
     */
    public function up(): void
    {
        Schema::table('verifikasi_tunjangan', function (Blueprint $table) {
            $table->string('anak_ke', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Tidak dikembalikan ke angka: nilai "1,2" bisa sudah tersimpan.
    }
};
