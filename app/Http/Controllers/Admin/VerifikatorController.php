<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Verifikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerifikatorController extends Controller
{
    public function index(Request $request)
    {
        $daftarVerifikator = Verifikator::withCount('sekolahBinaan')->orderBy('nama')->get();

        return view('dashboard.admin.verifikator.index', [
            'adminNama'         => auth('verifikator')->user()->nama,
            'adminNip'          => auth('verifikator')->user()->nip,
            'daftarVerifikator' => $daftarVerifikator,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.verifikator.form', [
            'adminNama'   => auth('verifikator')->user()->nama,
            'adminNip'    => auth('verifikator')->user()->nip,
            'mode'        => 'create',
            'verifikator' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request, isCreate: true);

        Verifikator::create([
            ...$validated,
            'is_admin' => $request->boolean('is_admin'),
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.data-master.verifikator.index')->with('success', 'Akun verifikator baru berhasil ditambahkan.');
    }

    public function edit(Verifikator $verifikator)
    {
        return view('dashboard.admin.verifikator.form', [
            'adminNama'   => auth('verifikator')->user()->nama,
            'adminNip'    => auth('verifikator')->user()->nip,
            'mode'        => 'edit',
            'verifikator' => $verifikator,
        ]);
    }

    public function update(Request $request, Verifikator $verifikator)
    {
        $validated = $this->validated($request, isCreate: false, nipLama: $verifikator->nip);
        $validated['is_admin'] = $request->boolean('is_admin');

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $verifikator->update($validated);

        return redirect()->route('admin.data-master.verifikator.index')->with('success', 'Data verifikator berhasil diperbarui.');
    }

    public function destroy(Request $request, Verifikator $verifikator)
    {
        abort_if($verifikator->nip === auth('verifikator')->id(), 422, 'Tidak bisa menghapus akun sendiri.');

        $nama = $verifikator->nama;
        $verifikator->delete(); // sekolah yang tadinya ditugaskan otomatis jadi "belum ditugaskan" (nullOnDelete)

        return redirect()->route('admin.data-master.verifikator.index')->with('success', "Akun verifikator {$nama} telah dihapus.");
    }

    private function validated(Request $request, bool $isCreate, ?string $nipLama = null): array
    {
        return $request->validate([
            'nip'       => ['required', 'digits:18', $isCreate ? 'unique:verifikators,nip' : 'in:' . $nipLama],
            'nama'      => ['required', 'string', 'max:150'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'password'  => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar.',
        ]);
    }
}
