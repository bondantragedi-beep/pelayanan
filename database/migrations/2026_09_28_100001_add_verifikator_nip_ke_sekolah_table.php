<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relasi penugasan sekolah -> verifikator yang SUNGGUHAN, diatur admin
     * lewat halaman Penugasan Sekolah. Sebelumnya verifikator hanya
     * "kebetulan" cocok lewat kolom kecamatan yang sama; sekarang eksplisit
     * per sekolah, dan bisa null (belum ditugaskan).
     *
     * nullOnDelete: kalau akun verifikator dihapus, sekolah yang tadinya
     * ditugaskan ke dia jadi "belum ditugaskan" lagi (bukan ikut terhapus).
     */
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->string('verifikator_nip', 18)->nullable()->after('kecamatan');
            $table->foreign('verifikator_nip')->references('nip')->on('verifikators')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['verifikator_nip']);
            $table->dropColumn('verifikator_nip');
        });
    }
};
