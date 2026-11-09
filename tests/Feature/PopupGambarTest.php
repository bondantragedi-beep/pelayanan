<?php
namespace Tests\Feature;
use App\Models\{Popup,Sekolah,Verifikator};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class PopupGambarTest extends TestCase {
  use RefreshDatabase;
  public function test_gambar_popup_bisa_dibuka_tanpa_storage_link(): void {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('p.jpg')->store('popups','public');
    $p = Popup::create(['judul'=>'X','gambar'=>$path,'aktif'=>true]);
    $v = Verifikator::create(['nip'=>'199001012015011001','nama'=>'V','kecamatan'=>'K','password'=>'x']);
    $s = Sekolah::create(['npsn'=>'1','nama_sekolah'=>'S','password'=>'x','verifikator_nip'=>$v->nip,'wajib_ganti_password'=>false]);
    $url = $p->gambar_url;
    $this->assertFileDoesNotExist(public_path("storage"));   // tidak ada symlink, seperti XAMPP bermasalah
    $this->actingAs($s,'sekolah')->get($url)->assertOk()->assertHeader('content-type','image/jpeg');
    auth()->guard('sekolah')->logout();
    $this->get($url)->assertForbidden();   // tamu tidak boleh
  }
}
