<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel referensi gaji pokok berdasarkan golongan + masa kerja (tahun).
     * Dipakai untuk otomatis mengisi "gaji pokok lama" saat verifikator
     * mengisi form KGB: gaji pokok lama diambil dari baris (golongan sama,
     * masa_kerja_tahun = masa kerja yang diinput - 2).
     *
     * CATATAN: nilai di seeder masih contoh kasar, belum tabel gaji resmi.
     * Ganti isinya sesuai tabel PP Gaji PNS/PPPK yang berlaku.
     */
    public function up(): void
    {
        Schema::create('referensi_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('golongan', 10);
            $table->unsignedTinyInteger('masa_kerja_tahun');
            $table->unsignedBigInteger('gaji_pokok');
            $table->timestamps();

            $table->unique(['golongan', 'masa_kerja_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referensi_gaji');
    }
};
