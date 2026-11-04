<?php

namespace App\Models;

use App\Support\LamaCuti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiCuti extends Model
{
    protected $table = 'verifikasi_cuti';

    public const PILIHAN_KEPUTUSAN = [
        'disetujui'       => 'Disetujui',
        'perubahan'       => 'Perubahan',
        'ditangguhkan'    => 'Ditangguhkan',
        'tidak_disetujui' => 'Tidak Disetujui',
    ];

    protected $fillable = [
        'pengajuan_id',
        'nip_pegawai', 'nama_pegawai', 'jabatan', 'unit_kerja', 'masa_kerja', 'pangkat_golongan',
        'jenis_cuti', 'cuti_ibadah', 'alasan',
        'tanggal_mulai', 'tanggal_selesai', 'lama_angka', 'lama_satuan',
        'catatan',
        'alamat_cuti', 'telp',
        'nama_kepsek', 'pertimbangan', 'keputusan',
        'saldo_terpotong', 'dibuat_oleh_nip',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'cuti_ibadah'     => 'boolean',
            'saldo_terpotong' => 'boolean',
            'catatan'         => 'array',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function labelJenis(): string
    {
        $label = LamaCuti::JENIS[$this->jenis_cuti] ?? ucfirst((string) $this->jenis_cuti);

        return $this->cuti_ibadah ? $label . ' (Ibadah)' : $label;
    }

    /** Tahun berjalan (N) = tahun tanggal mulai cuti. */
    public function tahunN(): int
    {
        return (int) $this->tanggal_mulai->format('Y');
    }

    /**
     * Memotong cuti tahunan ini dari saldo, SEKALI saja (saat dikirim ke sekolah).
     * Hanya bila jenisnya tahunan dan keputusan akhirnya disetujui/perubahan.
     */
    public function potongSaldo(): void
    {
        if ($this->saldo_terpotong || $this->jenis_cuti !== 'tahunan') {
            return;
        }
        if (! in_array($this->keputusan, ['disetujui', 'perubahan'], true)) {
            return;
        }

        $hari = LamaCuti::hariKerja($this->tanggal_mulai, $this->tanggal_selesai);
        SaldoCuti::tambahTerpakai($this->nip_pegawai, $this->tahunN(), $hari);

        $this->update(['saldo_terpotong' => true]);
    }
}
