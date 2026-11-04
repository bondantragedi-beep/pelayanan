<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * verifikasi_cuti : isian final formulir cuti (dipakai mencetak PDF
     *                   "Formulir Permintaan dan Pemberian Cuti").
     *                   Awalnya diisi dari data yang diinput sekolah (pengajuan.detail),
     *                   lalu diperiksa/dilengkapi verifikator.
     * saldo_cuti      : catatan pemakaian cuti tahunan per pegawai per tahun
     *                   -> sumber kolom "V. CATATAN CUTI" (N-2, N-1, N).
     *
     * Nomor naskah, tanggal naskah, jabatan tujuan, dan TTD pejabat sengaja TIDAK
     * disimpan: dibiarkan sebagai kode ${...} di PDF untuk diisi sistem persuratan.
     */
    public function up(): void
    {
        Schema::create('verifikasi_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan')->cascadeOnDelete();

            // I. Data pegawai (salinan saat diproses)
            $table->string('nip_pegawai', 18);
            $table->string('nama_pegawai');
            $table->string('jabatan')->nullable();
            $table->string('unit_kerja', 200)->nullable();
            $table->string('masa_kerja', 50)->nullable();
            $table->string('pangkat_golongan')->nullable();

            // II. Jenis cuti & III. Alasan
            $table->string('jenis_cuti', 30);              // tahunan|besar|sakit|melahirkan|alasan_penting|luar_tanggungan
            $table->boolean('cuti_ibadah')->default(false); // cuti tahunan untuk umroh/haji (boleh > 12 hari)
            $table->text('alasan');

            // IV. Lamanya cuti
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedSmallInteger('lama_angka');
            $table->string('lama_satuan', 10)->default('hari'); // hari|bulan|tahun

            // V. Catatan cuti: {"tahunan":[{"tahun":2024,"sisa":6,"ket":""},...],"besar":"","sakit":"",...}
            $table->json('catatan')->nullable();

            // VI. Alamat selama cuti
            $table->text('alamat_cuti');
            $table->string('telp', 30)->nullable();

            // VII & VIII
            $table->string('nama_kepsek')->nullable();
            $table->string('pertimbangan', 20)->default('disetujui'); // disetujui|perubahan|ditangguhkan|tidak_disetujui
            $table->string('keputusan', 20)->default('disetujui');

            // true setelah cuti tahunan ini dipotongkan ke saldo_cuti (sekali saja, saat dikirim ke sekolah)
            $table->boolean('saldo_terpotong')->default(false);

            $table->string('dibuat_oleh_nip', 18);
            $table->timestamps();
        });

        Schema::create('saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 18);
            $table->unsignedSmallInteger('tahun');
            $table->unsignedSmallInteger('terpakai')->default(0); // hari kerja cuti tahunan yang sudah dipakai
            $table->timestamps();

            $table->unique(['nip', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_cuti');
        Schema::dropIfExists('verifikasi_cuti');
    }
};
