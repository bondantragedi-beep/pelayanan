<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom "detail" menyimpan pilihan non-berkas dari sekolah (JSON), supaya
     * kolom "berkas" tetap hanya berisi path file. Contoh layanan tunjangan:
     *   {"jenis_pegawai": "PPPK", "jenis_tunjangan": "anak"}
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->json('detail')->nullable()->after('berkas');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('detail');
        });
    }
};
