<?php

namespace Tests\Feature;

use App\Models\Pengajuan;
use App\Models\Sekolah;
use App\Models\Verifikator;
use App\Support\UrutanBerkas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrutanBerkasTest extends TestCase
{
    use RefreshDatabase;

    private function buat(string $label, string $status, int $umurHari, ?string $hasil = null): Pengajuan
    {
        $p = Pengajuan::create([
            'npsn' => '1', 'jenis_layanan' => 'kgb', 'berkas' => ['x' => 'a.pdf'],
            'status' => $status, 'hasil_path' => $hasil, 'alasan_ditolak' => $status === 'rejected' ? 'x' : null,
            'detail' => ['label' => $label],
        ]);
        $t = now()->subDays($umurHari);
        $p->forceFill(['created_at' => $t, 'updated_at' => $status === 'pending' ? $t : now()->subDays(max(0, $umurHari - 1))])->saveQuietly();

        return $p->fresh();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $v = Verifikator::create(['nip' => '199001012015011001', 'nama' => 'Verif', 'kecamatan' => 'K', 'password' => 'x']);
        Sekolah::create(['npsn' => '1', 'nama_sekolah' => 'SDN Uji', 'password' => 'x', 'verifikator_nip' => $v->nip, 'wajib_ganti_password' => false]);
    }

    public function test_urutan_tertunda_proses_ditolak_terkirim(): void
    {
        $terkirimLama = $this->buat('terkirim-lama', 'processed', 30, 'h/1.pdf');
        $baru         = $this->buat('baru-hari-ini', 'pending', 0);
        $seminggu     = $this->buat('tepat-7-hari', 'pending', 7);
        $ditolak      = $this->buat('ditolak', 'rejected', 4);
        $telat        = $this->buat('telat-12-hari', 'processed', 12);        // menunggu TTD, belum terkirim
        $proses3      = $this->buat('proses-3-hari', 'pending', 3);
        $terkirimBaru = $this->buat('terkirim-baru', 'processed', 2, 'h/2.pdf');
        $paling       = $this->buat('telat-20-hari', 'pending', 20);

        $urut = UrutanBerkas::urutkan(Pengajuan::all())->map(fn ($p) => $p->detail['label'])->all();

        $this->assertSame([
            'telat-20-hari', 'telat-12-hari', 'tepat-7-hari',     // tertunda: terlama paling atas
            'proses-3-hari', 'baru-hari-ini',                      // dalam proses: terlama dulu
            'ditolak',                                             // ditolak
            'terkirim-baru', 'terkirim-lama',                      // terkirim: paling bawah (terbaru dulu)
        ], $urut);
    }

    public function test_berkas_terkirim_tidak_pernah_dianggap_tertunda_walau_sudah_lama(): void
    {
        $p = $this->buat('lama-tapi-selesai', 'processed', 40, 'h/x.pdf');
        $this->assertSame(UrutanBerkas::TERKIRIM, UrutanBerkas::tingkat($p));
        $this->assertFalse(UrutanBerkas::tertunda($p));
    }

    public function test_batas_hari_bisa_diatur_lewat_config(): void
    {
        $p = $this->buat('lima-hari', 'pending', 5);
        $this->assertSame(UrutanBerkas::PROSES, UrutanBerkas::tingkat($p));

        config(['berkas.batas_tertunda_hari' => 5]);
        $this->assertSame(UrutanBerkas::TERTUNDA, UrutanBerkas::tingkat($p));
    }

    public function test_layar_kantor_menampilkan_urutan_dan_label_tertunda(): void
    {
        $this->buat('a', 'processed', 30, 'h/1.pdf');
        $this->buat('b', 'pending', 9);
        $this->buat('c', 'pending', 1);

        $r = $this->get(route('layar-kantor'))->assertOk();
        $r->assertSee('Perlu perhatian')->assertSee('Tertunda 9 hari')->assertSee('Selesai');
        $r->assertSeeInOrder(['grup tertunda', 'grup proses', 'grup terkirim'], false);
    }

    public function test_layar_kantor_menampilkan_nama_verifikator_penugasan_sekolah(): void
    {
        $this->buat('x', 'pending', 2);

        $this->get(route('layar-kantor'))->assertOk()->assertSee('Verif')->assertDontSee('Belum ada verifikator');
    }

    public function test_layar_kantor_kosong_tidak_error(): void
    {
        $this->get(route('layar-kantor'))->assertOk()->assertSee('Belum ada data pengajuan masuk');
    }

    public function test_data_masuk_admin_memakai_urutan_yang_sama(): void
    {
        $this->buat('a', 'processed', 30, 'h/1.pdf');
        $this->buat('b', 'pending', 9);
        $this->buat('c', 'pending', 1);

        $admin = Verifikator::create(['nip' => '199901012020011001', 'nama' => 'Admin', 'kecamatan' => null, 'password' => 'x', 'is_admin' => true]);
        $r = $this->actingAs($admin, 'verifikator')->get(route('admin.data-masuk'))->assertOk();
        $r->assertSee('Tertunda 9 hari');
    }
}
