<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use App\Support\ImporSekolahParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImporSekolah extends Command
{
    protected $signature = 'sekolah:impor
                            {file : Path file CSV daftar sekolah}
                            {--password=123456 : Password awal untuk sekolah BARU}
                            {--reset-password : Reset juga password sekolah yang SUDAH ada ke password awal}
                            {--dry-run : Hanya memeriksa file, tidak menyimpan apa pun}';

    protected $description = 'Impor / perbarui daftar sekolah dari CSV. Sekolah baru mendapat password awal (wajib diganti saat login).';

    public function handle(ImporSekolahParser $parser): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $passwordAwal = (string) $this->option('password');
        if (strlen($passwordAwal) < 6) {
            $this->error('Password awal minimal 6 karakter.');

            return self::FAILURE;
        }

        $hasil = $parser->baca($path);

        if ($hasil['kolom']) {
            $this->info('Kolom yang terbaca:');
            foreach ($hasil['kolom'] as $tujuan => $asal) {
                $this->line("  {$tujuan}  <-  \"{$asal}\"");
            }
        }

        foreach ($hasil['peringatan'] as $p) {
            $this->warn($p);
        }
        foreach ($hasil['errors'] as $e) {
            $this->error($e);
        }

        $rows = $hasil['rows'];
        if (! $rows) {
            $this->error('Tidak ada baris yang bisa diimpor.');

            return self::FAILURE;
        }

        $sudahAda = Sekolah::pluck('npsn')->flip();
        $baru = array_filter($rows, fn ($r) => ! isset($sudahAda[$r['npsn']]));
        $perbarui = count($rows) - count($baru);

        $this->newLine();
        $this->info(sprintf(
            'Siap diproses: %d baris valid (%d sekolah baru, %d sudah ada), %d baris bermasalah dilewati.',
            count($rows), count($baru), $perbarui, count($hasil['errors'])
        ));

        if ($this->option('dry-run')) {
            $this->comment('Mode --dry-run: tidak ada yang disimpan.');

            return self::SUCCESS;
        }

        // Password di-hash SEKALI saja lalu dipakai ulang (bcrypt lambat; 1000x hash = berpuluh detik).
        $hash = Hash::make($passwordAwal);
        $resetLama = (bool) $this->option('reset-password');

        DB::transaction(function () use ($rows, $sudahAda, $hash, $resetLama) {
            foreach ($rows as $r) {
                if (! isset($sudahAda[$r['npsn']])) {
                    Sekolah::create($r + [
                        'password'             => $hash,
                        'wajib_ganti_password' => true,
                    ]);

                    continue;
                }

                // Sekolah lama: perbarui data identitas. Password TIDAK disentuh,
                // kecuali dengan --reset-password (supaya impor ulang tidak
                // menghapus password yang sudah diganti sekolah).
                $data = $r;
                unset($data['npsn']);
                if ($resetLama) {
                    $data['password'] = $hash;
                    $data['wajib_ganti_password'] = true;
                    $data['password_diubah_pada'] = null;
                }
                Sekolah::where('npsn', $r['npsn'])->update($data);
            }
        });

        $this->newLine();
        $this->info('Selesai.');
        $this->line(sprintf('  Sekolah baru ditambahkan : %d', count($baru)));
        $this->line(sprintf('  Sekolah lama diperbarui  : %d%s', $perbarui, $resetLama ? ' (password ikut direset)' : ' (password tidak diubah)'));
        $this->line(sprintf('  Password awal sekolah baru: %s (wajib diganti saat login pertama)', $passwordAwal));
        $this->comment('Penugasan verifikator belum diisi: atur lewat menu Penugasan Sekolah di akun admin.');

        return self::SUCCESS;
    }
}
