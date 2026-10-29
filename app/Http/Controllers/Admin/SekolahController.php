<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\Verifikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $daftarSekolah = Sekolah::with('verifikator')->orderBy('nama_sekolah')->get();

        return view('dashboard.admin.sekolah.index', [
            'adminNama'     => auth('verifikator')->user()->nama,
            'adminNip'      => auth('verifikator')->user()->nip,
            'daftarSekolah' => $daftarSekolah,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.sekolah.form', [
            'adminNama'  => auth('verifikator')->user()->nama,
            'adminNip'   => auth('verifikator')->user()->nip,
            'mode'       => 'create',
            'sekolah'    => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request, isCreate: true);

        Sekolah::create([
            ...$validated,
            'password'             => Hash::make($validated['password']),
            'wajib_ganti_password' => true, // password buatan admin = password awal, sekolah harus menggantinya
        ]);

        return redirect()->route('admin.data-master.sekolah.index')->with('success', 'Sekolah baru berhasil ditambahkan.');
    }

    public function edit(Sekolah $sekolah)
    {
        return view('dashboard.admin.sekolah.form', [
            'adminNama' => auth('verifikator')->user()->nama,
            'adminNip'  => auth('verifikator')->user()->nip,
            'mode'      => 'edit',
            'sekolah'   => $sekolah,
        ]);
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $validated = $this->validated($request, isCreate: false, npsnLama: $sekolah->npsn);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
            $validated['wajib_ganti_password'] = true; // admin yang menetapkan -> sekolah harus menggantinya sendiri
            $validated['password_diubah_pada'] = null;
        } else {
            unset($validated['password']);
        }

        $sekolah->update($validated);

        return redirect()->route('admin.data-master.sekolah.index')->with('success', 'Data sekolah berhasil diperbarui.');
    }

    /**
     * Reset password sekolah ke password awal (123456) dan wajibkan
     * sekolah menggantinya saat login berikutnya. Untuk sekolah yang lupa password.
     */
    public function resetPassword(Sekolah $sekolah)
    {
        $sekolah->forceFill([
            'password'             => Hash::make(Sekolah::PASSWORD_AWAL),
            'wajib_ganti_password' => true,
            'password_diubah_pada' => null,
        ])->save();

        return redirect()
            ->route('admin.data-master.sekolah.index')
            ->with('success', "Password {$sekolah->nama_sekolah} direset ke " . Sekolah::PASSWORD_AWAL . '. Sekolah wajib menggantinya saat login berikutnya.');
    }

    public function destroy(Sekolah $sekolah)
    {
        $nama = $sekolah->nama_sekolah;
        $sekolah->delete(); // pengajuan sekolah ikut terhapus (cascade); pegawai tetap ada, hanya jadi 'belum terhubung ke sekolah' (nullOnDelete)

        return redirect()->route('admin.data-master.sekolah.index')->with('success', "Sekolah {$nama} telah dihapus. Data pegawainya tetap tersimpan dan kini berstatus belum terhubung ke sekolah; pengajuan milik sekolah ini ikut terhapus.");
    }

    private function validated(Request $request, bool $isCreate, ?string $npsnLama = null): array
    {
        $rules = [
            'npsn'         => ['required', 'string', 'max:20', $isCreate ? 'unique:sekolah,npsn' : 'in:' . $npsnLama],
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'jenjang'      => ['required', 'string', 'max:20'],
            'status'       => ['required', 'in:Negeri,Swasta'],
            'kelurahan'    => ['nullable', 'string', 'max:100'],
            'kecamatan'    => ['required', 'string', 'max:100'],
            'password'     => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
        ];

        return $request->validate($rules, [
            'npsn.unique' => 'NPSN ini sudah terdaftar.',
        ]);
    }
}
