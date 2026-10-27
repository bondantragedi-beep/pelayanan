<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Untuk yang sudah menjalankan migrasi tabel verifikasi_tunjangan versi awal
     * (kolom enum), ubah jadi string supaya muat pilihan "istri_anak" / "suami_anak".
     * Aman dijalankan juga di database baru.
     */
    public function up(): void
    {
        Schema::table('verifikasi_tunjangan', function (Blueprint $table) {
            $table->string('jenis_tunjangan', 20)->change();
        });
    }

    public function down(): void
    {
        // Tidak dikembalikan ke enum: nilai "istri_anak"/"suami_anak" bisa sudah tersimpan.
    }
};
