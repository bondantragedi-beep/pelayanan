<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan penanda "sudah dilihat dokumennya" ke tabel pengajuan.
     * Verifikator wajib membuka minimal satu dokumen (lewat rute yang
     * mencatat kolom ini) sebelum tombol Terima/Tolak bisa dipakai —
     * pengecekan ini dijalankan di server, bukan cuma di tampilan.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->timestamp('dilihat_pada')->nullable()->after('berkas');
            $table->string('dilihat_oleh_nip', 18)->nullable()->after('dilihat_pada');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn(['dilihat_pada', 'dilihat_oleh_nip']);
        });
    }
};
