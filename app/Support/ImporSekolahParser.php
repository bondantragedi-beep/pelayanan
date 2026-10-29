<?php

namespace App\Support;

/**
 * Membaca file CSV daftar sekolah menjadi baris-baris siap simpan.
 * Murni PHP (tanpa Laravel) supaya mudah diuji.
 *
 * Nama kolom dibuat longgar: "NPSN", "Nama Sekolah", "Nama Satuan Pendidikan",
 * "Bentuk Pendidikan", "Status Sekolah", dst. sama-sama dikenali.
 * Pemisah kolom (koma / titik koma / tab) dideteksi otomatis, jadi CSV hasil
 * Excel Indonesia (titik koma) maupun Dapodik (koma) sama-sama bisa dibaca.
 */
class ImporSekolahParser
{
    /** kolom tujuan => kemungkinan nama kolom di file (sudah dinormalkan) */
    private const ALIAS = [
        'npsn'         => ['npsn'],
        'nama_sekolah' => ['namasekolah', 'nama', 'namasatuanpendidikan', 'namasatpen', 'namalembaga'],
        'jenjang'      => ['jenjang', 'bentukpendidikan', 'bentuk', 'jenjangpendidikan'],
        'status'       => ['status', 'statussekolah', 'statuskepemilikan'],
        'kelurahan'    => ['kelurahan', 'desakelurahan', 'desa'],
        'kecamatan'    => ['kecamatan'],
    ];

    private const JENJANG = [
        'TK'                                 => 'TK',
        'TAMAN KANAK-KANAK'                  => 'TK',
        'TAMAN KANAK KANAK'                  => 'TK',
        'SD'                                 => 'SD',
        'SEKOLAH DASAR'                      => 'SD',
        'SMP'                                => 'SMP',
        'SEKOLAH MENENGAH PERTAMA'           => 'SMP',
        'PKBM'                               => 'PKBM',
        'PUSAT KEGIATAN BELAJAR MASYARAKAT'  => 'PKBM',
        'KB'                                 => 'KB',
        'KELOMPOK BERMAIN'                   => 'KB',
    ];

    /**
     * @return array{rows: array<int,array>, errors: array<int,string>, peringatan: array<int,string>, kolom: array<string,string>}
     */
    public function baca(string $path): array
    {
        $isi = file_get_contents($path);
        if ($isi === false || trim($isi) === '') {
            return ['rows' => [], 'errors' => ['File kosong atau tidak bisa dibaca.'], 'peringatan' => [], 'kolom' => []];
        }

        $isi = preg_replace('/^\xEF\xBB\xBF/', '', $isi);           // buang BOM UTF-8 dari Excel
        if (! mb_check_encoding($isi, 'UTF-8')) {                    // CSV lama Excel bisa berupa Windows-1252
            $isi = mb_convert_encoding($isi, 'UTF-8', 'Windows-1252');
        }

        $baris = preg_split('/\R/', $isi);
        $pemisah = $this->deteksiPemisah($baris[0]);

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $isi);
        rewind($handle);

        $header = fgetcsv($handle, 0, $pemisah, '"', '\\');
        $peta = $this->petaKolom($header ?: []);

        $errors = [];
        foreach (['npsn', 'nama_sekolah'] as $wajib) {
            if (! isset($peta[$wajib])) {
                $errors[] = "Kolom wajib \"{$wajib}\" tidak ditemukan di baris pertama file.";
            }
        }
        if ($errors) {
            fclose($handle);

            return ['rows' => [], 'errors' => $errors, 'peringatan' => [], 'kolom' => []];
        }

        $rows = [];
        $peringatan = [];
        $sudahAda = [];
        $noBaris = 1;

        while (($r = fgetcsv($handle, 0, $pemisah, '"', '\\')) !== false) {
            $noBaris++;
            if (count(array_filter($r, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // baris kosong
            }

            $ambil = fn (string $k) => isset($peta[$k]) ? trim((string) ($r[$peta[$k]] ?? '')) : '';

            $npsn = $ambil('npsn');
            $nama = $ambil('nama_sekolah');

            if ($npsn === '' || $nama === '') {
                $errors[] = "Baris {$noBaris}: NPSN dan nama sekolah wajib diisi.";
                continue;
            }
            if (stripos($npsn, 'E+') !== false) {
                $errors[] = "Baris {$noBaris}: NPSN \"{$npsn}\" rusak (berubah jadi notasi ilmiah oleh Excel). Format kolom NPSN sebagai Teks lalu simpan ulang.";
                continue;
            }
            if (! preg_match('/^[0-9A-Za-z]{1,20}$/', $npsn)) {
                $errors[] = "Baris {$noBaris}: NPSN \"{$npsn}\" tidak valid (maksimal 20 huruf/angka).";
                continue;
            }
            if (isset($sudahAda[$npsn])) {
                $errors[] = "Baris {$noBaris}: NPSN {$npsn} dobel (sudah muncul di baris {$sudahAda[$npsn]}), baris ini dilewati.";
                continue;
            }
            $sudahAda[$npsn] = $noBaris;

            $status = $this->normalStatus($ambil('status'));
            if ($ambil('status') !== '' && $status === null) {
                $peringatan[] = "Baris {$noBaris} ({$npsn}): status \"{$ambil('status')}\" tidak dikenali (harus Negeri/Swasta), dikosongkan.";
            }

            $rows[] = [
                'npsn'         => $npsn,
                'nama_sekolah' => mb_substr($nama, 0, 150),
                'jenjang'      => $this->normalJenjang($ambil('jenjang')),
                'status'       => $status,
                'kelurahan'    => $ambil('kelurahan') !== '' ? mb_substr($ambil('kelurahan'), 0, 100) : null,
                'kecamatan'    => $ambil('kecamatan') !== '' ? mb_substr($ambil('kecamatan'), 0, 100) : null,
            ];
        }
        fclose($handle);

        $terbaca = [];
        foreach ($peta as $tujuan => $idx) {
            $terbaca[$tujuan] = (string) ($header[$idx] ?? '');
        }

        return ['rows' => $rows, 'errors' => $errors, 'peringatan' => $peringatan, 'kolom' => $terbaca];
    }

    private function deteksiPemisah(string $barisPertama): string
    {
        $hitung = [',' => substr_count($barisPertama, ','), ';' => substr_count($barisPertama, ';'), "\t" => substr_count($barisPertama, "\t")];
        arsort($hitung);

        return array_key_first($hitung);
    }

    private function petaKolom(array $header): array
    {
        $peta = [];
        foreach ($header as $idx => $nama) {
            $kunci = preg_replace('/[^a-z0-9]/', '', strtolower((string) $nama));
            foreach (self::ALIAS as $tujuan => $daftar) {
                if (! isset($peta[$tujuan]) && in_array($kunci, $daftar, true)) {
                    $peta[$tujuan] = $idx;
                }
            }
        }

        return $peta;
    }

    private function normalJenjang(string $v): ?string
    {
        $v = strtoupper(trim(preg_replace('/\s+/', ' ', $v)));

        return $v === '' ? null : mb_substr(self::JENJANG[$v] ?? $v, 0, 20);
    }

    private function normalStatus(string $v): ?string
    {
        return match (strtolower(trim($v))) {
            'negeri' => 'Negeri',
            'swasta' => 'Swasta',
            default  => null,
        };
    }
}
