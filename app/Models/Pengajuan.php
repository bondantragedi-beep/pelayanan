<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $fillable = [
        'npsn',
        'jenis_layanan',
        'berkas',
        'detail',
        'status',
        'dilihat_pada',
        'dilihat_oleh_nip',
        'alasan_ditolak',
        'hasil_path',
        'diproses_oleh_nip',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'berkas'        => 'array',
            'detail'        => 'array',
            'dilihat_pada'  => 'datetime',
            'diproses_pada' => 'datetime',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }

    public function verifikasiKgb(): HasOne
    {
        return $this->hasOne(VerifikasiKgb::class, 'pengajuan_id');
    }

    public function verifikasiTunjangan(): HasOne
    {
        return $this->hasOne(VerifikasiTunjangan::class, 'pengajuan_id');
    }

    public function isTunjangan(): bool
    {
        return $this->jenis_layanan === 'tunjangan';
    }

    /** Data isian verifikator (KGB atau Tunjangan), null jika belum diisi. */
    public function dataVerifikasi(): VerifikasiKgb|VerifikasiTunjangan|null
    {
        return $this->isTunjangan() ? $this->verifikasiTunjangan : $this->verifikasiKgb;
    }

    /** Nama berkas ramah-baca untuk daftar dokumen di halaman verifikator. */
    public static function labelBerkas(string $key): string
    {
        return match ($key) {
            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
            'sk_kgb_terakhir'     => 'SK KGB Terakhir',
            'kp4'                 => 'KP4',
            'akta_kelahiran_anak' => 'Akta Kelahiran Anak',
            'kk'                  => 'Kartu Keluarga (KK)',
            'sk_terakhir'         => 'SK Terakhir',
            'buku_nikah'          => 'Buku Nikah',
            default               => $key,
        };
    }

    /** Label ramah-baca untuk jenis layanan, dipakai di tampilan. */
    public function labelLayanan(): string
    {
        return match ($this->jenis_layanan) {
            'kgb'       => 'Kenaikan Gaji Berkala (KGB)',
            'tunjangan' => 'Tunjangan' . $this->labelDetailTunjangan(),
            default => ucfirst($this->jenis_layanan),
        };
    }

    /**
     * Tahap pengajuan dari sudut pandang Verifikator/Admin — dipisah dari
     * "status" mentah karena satu status 'processed' masih punya 2 kondisi:
     * sudah diisi formnya tapi belum dikirim (menunggu TTD pimpinan),
     * atau sudah benar-benar dikirim ke sekolah (hasil_path terisi).
     */
    public function tahapVerifikator(): string
    {
        if ($this->status === 'rejected') {
            return 'rejected';
        }
        if ($this->status === 'processed' && $this->hasil_path) {
            return 'terkirim';
        }
        if ($this->status === 'processed') {
            return 'menunggu_ttd';
        }

        return 'pending';
    }

    /** Tambahan label di daftar: " (Anak, PPPK)" bila sekolah sudah memilih. */
    private function labelDetailTunjangan(): string
    {
        $jenis = ($this->detail['jenis_tunjangan'] ?? null) === 'anak' ? 'Anak' : (($this->detail['jenis_tunjangan'] ?? null) ? 'Istri/Suami' : null);
        $pegawai = $this->detail['jenis_pegawai'] ?? null;

        $bagian = array_filter([$jenis, $pegawai]);

        return $bagian ? ' (' . implode(', ', $bagian) . ')' : '';
    }
}
