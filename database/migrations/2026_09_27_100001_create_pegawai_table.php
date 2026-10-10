<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pegawai — kolomnya mengikuti file Excel data pegawai:
     * No, Nama, Tempat Tanggal Lahir, NIP, Unit Kerja, Golongan Pangkat,
     * TMT Golongan, Eselon, Nama Jabatan, TMT Jabatan, Status Pegawai,
     * TMT Pegawai, Masa Kerja Tahun/Bulan, Jenis Kelamin, Agama,
     * Status Perkawinan, Pendidikan Awal/Akhir, No NPWP, No KTP,
     * Alamat Rumah, Telp, HP, E-mail.
     *
     * Kolom "No" tidak disimpan (hanya nomor urut di Excel).
     *
     * Catatan penting:
     *  - Excel hanya punya "Unit Kerja" (nama sekolah), BUKAN NPSN. Jadi
     *    unit_kerja disimpan apa adanya, sedangkan npsn diisi (boleh kosong)
     *    setelah dicocokkan dengan tabel sekolah. Pegawai yang npsn-nya masih
     *    kosong belum muncul di pencarian NIP milik verifikator mana pun.
     *  - Hampir semua kolom dibuat nullable karena data Excel sering ada
     *    sel kosong; hanya NIP dan Nama yang wajib.
     *  - Tempat Tanggal Lahir disimpan sebagai teks utuh seperti di Excel.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->string('nip', 18)->primary();
            $table->string('nama');
            $table->string('tempat_tanggal_lahir', 150)->nullable();

            // Penempatan
            $table->string('unit_kerja', 200)->nullable();
            $table->string('npsn', 20)->nullable();

            // Kepegawaian
            $table->string('golongan_pangkat', 100)->nullable();
            $table->date('tmt_golongan')->nullable();
            $table->string('eselon', 20)->nullable();
            $table->string('nama_jabatan', 200)->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->string('status_pegawai', 30)->nullable();
            $table->date('tmt_pegawai')->nullable();
            $table->unsignedTinyInteger('masa_kerja_tahun')->nullable();
            $table->unsignedTinyInteger('masa_kerja_bulan')->nullable();

            // Data pribadi
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('agama', 30)->nullable();
            $table->string('status_perkawinan', 30)->nullable();
            $table->string('pendidikan_awal', 100)->nullable();
            $table->string('pendidikan_akhir', 100)->nullable();
            $table->string('no_npwp', 30)->nullable();
            $table->string('no_ktp', 20)->nullable();

            // Kontak
            $table->text('alamat_rumah')->nullable();
            $table->string('telp', 30)->nullable();
            $table->string('hp', 30)->nullable();
            $table->string('email', 150)->nullable();

            $table->timestamps();

            // nullOnDelete: kalau sebuah sekolah dihapus, data pegawainya TIDAK
            // ikut terhapus — hanya jadi "belum terhubung ke sekolah".
            $table->foreign('npsn')->references('npsn')->on('sekolah')->nullOnDelete();
            $table->index('unit_kerja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
