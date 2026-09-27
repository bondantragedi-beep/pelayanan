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
            'password' => Hash::make($validated['password']),
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
        } else {
            unset($validated['password']);
        }

        $sekolah->update($validated);

        return redirect()->route('admin.data-master.sekolah.index')->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        $nama = $sekolah->nama_sekolah;
        $sekolah->delete(); // pegawai & pengajuan terkait ikut terhapus (cascadeOnDelete)

        return redirect()->route('admin.data-master.sekolah.index')->with('success', "Sekolah {$nama} beserta data pegawai & pengajuannya telah dihapus.");
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
