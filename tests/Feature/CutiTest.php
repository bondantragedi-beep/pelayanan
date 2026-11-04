<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Models\SaldoCuti;
use App\Models\Sekolah;
use App\Models\Verifikator;
use App\Models\VerifikasiCuti;
use App\Support\LamaCuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CutiTest extends TestCase
{
    use RefreshDatabase;

    private Sekolah $sekolah;
    private Verifikator $verifikator;
    private Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->verifikator = Verifikator::create(['nip' => '199001012015011001', 'nama' => 'Verif Uji', 'kecamatan' => 'Panakkukang', 'password' => 'rahasia']);
        $this->sekolah = Sekolah::create([
            'npsn' => '40311001', 'nama_sekolah' => 'SDN 1 Makassar', 'password' => 'rahasia',
            'verifikator_nip' => $this->verifikator->nip, 'wajib_ganti_password' => false,
        ]);
        $this->pegawai = Pegawai::create([
            'nip' => '198002012005011001', 'nama' => 'Muhammad Ikbal, S.Pd.', 'npsn' => '40311001',
            'unit_kerja' => 'SDN 1 Makassar', 'golongan_pangkat' => 'Penata Muda Tk. I (III/b)',
            'nama_jabatan' => 'Guru Ahli Pertama', 'masa_kerja_tahun' => 18, 'masa_kerja_bulan' => 2, 'hp' => '0812345',
        ]);
        Pegawai::create(['nip' => '197001012000011001', 'nama' => 'Hj. Kepala Uji, M.Pd.', 'npsn' => '40311001', 'nama_jabatan' => 'Kepala Sekolah']);
    }

    private function berkas(array $extra = []): array
    {
        return array_merge([
            'formulir_cuti'       => UploadedFile::fake()->create('formulir.pdf', 100, 'application/pdf'),
            'sk_pangkat_terakhir' => UploadedFile::fake()->create('sk.pdf', 100, 'application/pdf'),
            'rekap_absen'         => UploadedFile::fake()->create('absen.pdf', 100, 'application/pdf'),
        ], $extra);
    }

    private function data(array $o = []): array
    {
        return array_merge([
            'nip_pegawai' => $this->pegawai->nip, 'jenis_cuti' => 'tahunan', 'alasan' => 'Alasan Keluarga',
            'tanggal_mulai' => '2025-12-24', 'tanggal_selesai' => '2026-01-02',
            'alamat_cuti' => 'Jl. Hati Rela No. 50 Makassar', 'telp' => '0812345',
        ], $this->berkas(), $o);
    }

    public function test_hitung_hari_kerja_sesuai_contoh_formulir(): void
    {
        // Contoh formulir: 24 Des 2025 s/d 2 Jan 2026 = 5 hari kerja
        $this->assertSame(5, LamaCuti::hariKerja('2025-12-24', '2026-01-02'));
        $this->assertSame(['angka' => 3, 'satuan' => 'bulan'], array_intersect_key(LamaCuti::hitung('melahirkan', '2026-03-01', '2026-05-31'), ['angka' => 1, 'satuan' => 1]));
        $this->assertSame('2026-05-31', LamaCuti::selesaiSetelahBulan('2026-03-01', 3));
    }

    public function test_menu_cuti_terbuka_di_dashboard_sekolah(): void
    {
        $this->actingAs($this->sekolah, 'sekolah')->get(route('sekolah.dashboard'))
            ->assertOk()->assertSee(route('sekolah.cuti'), false);
        $this->actingAs($this->sekolah, 'sekolah')->get(route('sekolah.cuti'))->assertOk()->assertSee('Rekap Absen 2 Tahun Terakhir');
    }

    public function test_sekolah_mengajukan_cuti_tahunan(): void
    {
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data())
            ->assertRedirect(route('sekolah.berkas'))->assertSessionHasNoErrors();

        $p = Pengajuan::firstOrFail();
        $this->assertSame('cuti', $p->jenis_layanan);
        $this->assertSame(5, $p->detail['lama_angka']);
        $this->assertSame('Cuti Tahunan', $p->labelLayanan());
        $this->assertCount(3, $p->berkas);
    }

    public function test_cuti_tahunan_lebih_12_hari_kerja_ditolak_kecuali_ibadah(): void
    {
        $lebih = ['tanggal_mulai' => '2026-02-02', 'tanggal_selesai' => '2026-02-20']; // 15 hari kerja
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data($lebih))
            ->assertSessionHasErrors('tanggal_selesai');
        $this->assertSame(0, Pengajuan::count());

        // ibadah: wajib berkas tambahan
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data($lebih + ['cuti_ibadah' => 1]))
            ->assertSessionHasErrors('berkas_tambahan');

        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data($lebih + [
            'cuti_ibadah' => 1,
            'berkas_tambahan' => UploadedFile::fake()->create('kwitansi.pdf', 50, 'application/pdf'),
        ]))->assertSessionHasNoErrors();
        $this->assertSame('Cuti Tahunan (Ibadah)', Pengajuan::first()->labelLayanan());
    }

    public function test_melahirkan_wajib_surat_rs_dan_maks_3_bulan(): void
    {
        $d = $this->data(['jenis_cuti' => 'melahirkan', 'tanggal_mulai' => '2026-03-01', 'tanggal_selesai' => '2026-05-31']);
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $d)->assertSessionHasErrors('berkas_tambahan');

        $ok = $this->data(['jenis_cuti' => 'melahirkan', 'tanggal_mulai' => '2026-03-01', 'tanggal_selesai' => '2026-05-31',
            'berkas_tambahan' => UploadedFile::fake()->create('rs.pdf', 50, 'application/pdf')]);
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $ok)->assertSessionHasNoErrors();
        $this->assertSame('bulan', Pengajuan::first()->detail['lama_satuan']);

        $lebih = $this->data(['jenis_cuti' => 'melahirkan', 'tanggal_mulai' => '2026-03-01', 'tanggal_selesai' => '2026-06-15',
            'berkas_tambahan' => UploadedFile::fake()->create('rs.pdf', 50, 'application/pdf')]);
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $lebih)->assertSessionHasErrors('tanggal_selesai');
    }

    public function test_pegawai_sekolah_lain_ditolak(): void
    {
        Pegawai::create(['nip' => '198505052010011002', 'nama' => 'Orang Lain', 'npsn' => null]);
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data(['nip_pegawai' => '198505052010011002']))
            ->assertSessionHasErrors('nip_pegawai');
    }

    public function test_alur_verifikator_form_pdf_dan_potong_saldo(): void
    {
        $this->actingAs($this->sekolah, 'sekolah')->post(route('sekolah.cuti.store'), $this->data());
        $p = Pengajuan::firstOrFail();
        SaldoCuti::aturSisa($this->pegawai->nip, 2025, 12);

        $v = $this->actingAs($this->verifikator, 'verifikator');
        $v->get(route('verifikator.berkas.show', $p))->assertOk()->assertSee('Cuti Tahunan');
        $v->get(route('verifikator.berkas.dokumen', [$p, 'formulir_cuti']))->assertOk();

        $v->get(route('verifikator.cuti.terima', $p))->assertOk()
            ->assertSee('Hj. Kepala Uji')->assertSee('Penata Muda Tk. I (III/b)')->assertSee('18 Tahun 2 Bulan');

        $payload = [
            'nip_pegawai' => $this->pegawai->nip, 'jabatan' => 'Guru Ahli Pertama', 'unit_kerja' => 'SDN 1 Makassar',
            'masa_kerja' => '18 Tahun 2 Bulan', 'pangkat_golongan' => 'Penata Muda Tk. I (III/b)',
            'jenis_cuti' => 'tahunan', 'alasan' => 'Alasan Keluarga',
            'tanggal_mulai' => '2025-12-24', 'tanggal_selesai' => '2026-01-02',
            'sisa_n2' => 6, 'sisa_n1' => 6, 'sisa_n' => 11, 'ket_n2' => '', 'ket_n1' => '', 'ket_n' => '',
            'alamat_cuti' => 'Jl. Hati Rela No. 50 Makassar', 'telp' => '0812345',
            'nama_kepsek' => 'Hj. Kepala Uji, M.Pd.', 'pertimbangan' => 'disetujui', 'keputusan' => 'disetujui',
        ];
        $v->post(route('verifikator.cuti.terima.store', $p), $payload)->assertRedirect(route('verifikator.berkas.cetak', $p))->assertSessionHasNoErrors();

        $this->assertSame('processed', $p->fresh()->status);
        $vc = VerifikasiCuti::firstOrFail();
        $this->assertSame(5, $vc->lama_angka);
        $this->assertSame(6, SaldoCuti::sisa($this->pegawai->nip, 2024));

        $v->get(route('verifikator.berkas.cetak', $p))->assertOk()->assertSee('Formulir Cuti');

        $res = $v->get(route('verifikator.cuti.cetak.pdf', $p));
        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('content-type'));
        file_put_contents(sys_get_temp_dir() . '/cuti-uji.pdf', $res->baseResponse->getContent());

        // KGB tidak boleh membuka berkas cuti
        $v->get(route('verifikator.berkas.terima', $p))->assertNotFound();

        // Kirim hasil TTD -> saldo 2025 dipotong 5 hari (sisa 11 -> hak 12, terpakai 1 +5 = 6 -> sisa 6)
        $v->post(route('verifikator.berkas.kirim', $p), ['hasil_ttd' => UploadedFile::fake()->create('ttd.pdf', 50, 'application/pdf')])->assertSessionHasNoErrors();
        $this->assertSame(6, SaldoCuti::sisa($this->pegawai->nip, 2025));
        $this->assertTrue($vc->fresh()->saldo_terpotong);

        // Inbox sekolah bisa mengunduh hasilnya
        $s = $this->actingAs($this->sekolah, 'sekolah');
        $s->get(route('sekolah.inbox'))->assertOk()->assertSee('Cuti Tahunan');
        $s->get(route('sekolah.inbox.dokumen', $p))->assertOk();
    }
}
