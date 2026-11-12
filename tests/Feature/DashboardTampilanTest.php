<?php

namespace Tests\Feature;

use App\Models\Pengajuan;
use App\Models\Sekolah;
use App\Models\Verifikator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTampilanTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(): array
    {
        $v = Verifikator::create(['nip' => '199001012015011001', 'nama' => 'Verif', 'kecamatan' => 'K', 'password' => 'x']);
        $s = Sekolah::create(['npsn' => '1', 'nama_sekolah' => 'SDN Uji', 'password' => 'x', 'verifikator_nip' => $v->nip, 'wajib_ganti_password' => false]);

        $b = ['sk_pangkat_terakhir' => 'x/a.pdf'];
        Pengajuan::create(['npsn' => '1', 'jenis_layanan' => 'kgb', 'berkas' => $b, 'status' => 'pending']);
        Pengajuan::create(['npsn' => '1', 'jenis_layanan' => 'cuti', 'berkas' => $b, 'detail' => ['jenis_cuti' => 'tahunan'], 'status' => 'pending']);
        Pengajuan::create(['npsn' => '1', 'jenis_layanan' => 'tunjangan', 'berkas' => $b, 'status' => 'processed']);
        Pengajuan::create(['npsn' => '1', 'jenis_layanan' => 'kgb', 'berkas' => $b, 'status' => 'processed', 'hasil_path' => 'hasil/x.pdf']);
        Pengajuan::create(['npsn' => '1', 'jenis_layanan' => 'kgb', 'berkas' => $b, 'status' => 'rejected', 'alasan_ditolak' => 'buram']);

        return [$s, $v];
    }

    public function test_dashboard_sekolah_menampilkan_ringkasan_dan_berkas_terbaru(): void
    {
        [$s] = $this->siapkan();

        $r = $this->actingAs($s, 'sekolah')->get(route('sekolah.dashboard'))->assertOk();
        $r->assertSee('Halo, SDN Uji')->assertSee('Ajukan Layanan')->assertSee('Berkas Terbaru');
        $r->assertSeeInOrder(['hs-stat-n">2<', 'hs-stat-n">1<', 'hs-stat-n">1<', 'hs-stat-n">1<'], false);   // menunggu, diproses, selesai, ditolak
        $r->assertSee('Cuti Tahunan');   // label berkas terbaru
    }

    public function test_dashboard_sekolah_kosong_tidak_error(): void
    {
        $v = Verifikator::create(['nip' => '199001012015011001', 'nama' => 'V', 'kecamatan' => 'K', 'password' => 'x']);
        $s = Sekolah::create(['npsn' => '2', 'nama_sekolah' => 'SDN Kosong', 'password' => 'x', 'verifikator_nip' => $v->nip, 'wajib_ganti_password' => false]);

        $this->actingAs($s, 'sekolah')->get(route('sekolah.dashboard'))->assertOk()->assertSee('Belum ada berkas');
    }

    public function test_dashboard_verifikator_menampilkan_ringkasan(): void
    {
        [, $v] = $this->siapkan();

        $this->actingAs($v, 'verifikator')->get(route('verifikator.dashboard'))
            ->assertOk()->assertSee('Menunggu Anda periksa')->assertSee('Menunggu tanda tangan');
    }

    public function test_beranda_memuat_carousel_dan_bagian_layanan(): void
    {
        $this->get('/')->assertOk()->assertSee('hc-stage', false)->assertSee('al-steps', false)->assertSee('ly-grid', false)->assertSee('ft-top', false);
    }
}
