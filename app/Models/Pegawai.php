<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip', 'nama', 'tempat_tanggal_lahir',
        'unit_kerja', 'npsn',
        'golongan_pangkat', 'tmt_golongan', 'eselon',
        'nama_jabatan', 'tmt_jabatan',
        'status_pegawai', 'tmt_pegawai',
        'masa_kerja_tahun', 'masa_kerja_bulan',
        'jenis_kelamin', 'agama', 'status_perkawinan',
        'pendidikan_awal', 'pendidikan_akhir',
        'no_npwp', 'no_ktp',
        'alamat_rumah', 'telp', 'hp', 'email',
    ];

    protected function casts(): array
    {
        return [
            'tmt_golongan' => 'date',
            'tmt_jabatan'  => 'date',
            'tmt_pegawai'  => 'date',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }

    /**
     * Golongan ruang hasil bacaan dari kolom "Golongan Pangkat" di Excel,
     * contoh: "Penata Muda Tk. I (III/b)" -> "III/b", "IX" -> "IX".
     * Dipakai form verifikator untuk mengisi dropdown golongan otomatis.
     * Mengembalikan null kalau formatnya tidak dikenali.
     */
    protected function golongan(): Attribute
    {
        return Attribute::get(function () {
            $teks = trim((string) $this->golongan_pangkat);

            if ($teks === '') {
                return null;
            }

            if (preg_match('/\b(IV|III|II|I)\s*\/\s*([a-e])\b/i', $teks, $m)) {
                return strtoupper($m[1]) . '/' . strtolower($m[2]);
            }

            if (preg_match('/^[IVXivx]+$/', $teks)) {
                return strtoupper($teks);
            }

            return null;
        });
    }

    /**
     * Gabungan golongan/pangkat + nama jabatan, dipakai untuk mengisi
     * kolom "Pangkat / Jabatan" di form verifikator & surat KGB.
     */
    protected function pangkatJabatan(): Attribute
    {
        return Attribute::get(function () {
            return collect([$this->golongan_pangkat, $this->nama_jabatan])
                ->filter(fn ($v) => filled($v))
                ->implode(' / ');
        });
    }
}
