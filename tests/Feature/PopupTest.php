<?php

namespace Tests\Feature;

use App\Models\Popup;
use App\Models\Sekolah;
use App\Models\Verifikator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PopupTest extends TestCase
{
    use RefreshDatabase;

    private function sekolah(): Sekolah
    {
        $v = Verifikator::create(['nip' => '199001012015011001', 'nama' => 'V', 'kecamatan' => 'K', 'password' => 'x']);

        return Sekolah::create(['npsn' => '1', 'nama_sekolah' => 'SDN Uji', 'password' => 'x', 'verifikator_nip' => $v->nip, 'wajib_ganti_password' => false]);
    }

    private function admin(): Verifikator
    {
        return Verifikator::create(['nip' => '199901012020011001', 'nama' => 'Admin', 'kecamatan' => null, 'password' => 'x', 'is_admin' => true]);
    }

    public function test_halaman_admin_popup_terbuka_dan_ada_di_menu(): void
    {
        $this->actingAs($this->admin(), 'verifikator')->get(route('admin.popup.index'))
            ->assertOk()->assertSee('Upload Gambar Baru')->assertSee('Popup Pengumuman');
    }

    public function test_upload_aktif_langsung_tampil_di_sekolah_sekali_per_login(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin(), 'verifikator')->post(route('admin.popup.store'), [
            'judul' => 'Hari Budaya', 'aktifkan' => 1,
            'gambar' => UploadedFile::fake()->image('p.jpg', 600, 800),
        ])->assertSessionHasNoErrors();

        $popup = Popup::firstOrFail();
        $this->assertTrue($popup->aktif);
        Storage::disk('public')->assertExists($popup->gambar);

        auth()->guard('verifikator')->logout();
        $sekolah = $this->sekolah();

        $this->actingAs($sekolah, 'sekolah')->get(route('sekolah.dashboard'))
            ->assertOk()->assertSee('popupPengumuman', false)->assertSee("/popup-gambar/{$popup->id}", false);

        // sekali per login: kunjungan berikutnya di sesi yang sama tidak menampilkannya lagi
        $this->get(route('sekolah.cuti'))->assertOk()->assertDontSee('popupPengumuman', false);
    }

    public function test_popup_nonaktif_tidak_tampil(): void
    {
        Popup::create(['judul' => 'A', 'gambar' => 'popups/a.jpg', 'aktif' => false]);

        $this->actingAs($this->sekolah(), 'sekolah')->get(route('sekolah.dashboard'))
            ->assertOk()->assertDontSee('popupPengumuman', false);
    }

    public function test_mengaktifkan_satu_popup_mematikan_yang_lain(): void
    {
        $a = Popup::create(['judul' => 'A', 'gambar' => 'popups/a.jpg', 'aktif' => false]);
        $b = Popup::create(['judul' => 'B', 'gambar' => 'popups/b.jpg', 'aktif' => true]);

        $this->actingAs($this->admin(), 'verifikator')->patch(route('admin.popup.toggle', $a))->assertSessionHasNoErrors();

        $this->assertTrue($a->fresh()->aktif);
        $this->assertFalse($b->fresh()->aktif);
    }
}
