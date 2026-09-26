<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengajuan berkas dari sekolah (KGB, dan nanti jenis layanan lain).
     *
     * "berkas" disimpan sebagai JSON supaya fleksibel untuk tiap jenis layanan
     * punya kelengkapan berkas yang beda-beda, contoh untuk KGB:
     *   {"sk_pangkat_terakhir": "kgb/40311001/sk-pangkat-terakhir/xxx.pdf",
     *    "sk_kgb_terakhir": "kgb/40311001/sk-kgb-terakhir/xxx.pdf"}
     *
     * "hasil_path" diisi verifikator/admin setelah dokumen ditandatangani
     * pimpinan dan siap diunduh sekolah lewat menu Inbox.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20);
            $table->string('jenis_layanan', 50);
            $table->json('berkas');
            $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
            $table->text('alasan_ditolak')->nullable();
            $table->string('hasil_path')->nullable();
            $table->string('diproses_oleh_nip', 18)->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();

            $table->foreign('npsn')->references('npsn')->on('sekolah')->cascadeOnDelete();
            $table->index(['npsn', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
