<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    private const GOLONGAN = [
        'I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d',
        'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e', 'IX',
    ];

    public function index(Request $request)
    {
        $daftarPegawai = Pegawai::with('sekolah')->orderBy('nama')->get();

        return view('dashboard.admin.pegawai.index', [
            'adminNama'     => auth('verifikator')->user()->nama,
            'adminNip'      => auth('verifikator')->user()->nip,
            'daftarPegawai' => $daftarPegawai,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.pegawai.form', [
            'adminNama'      => auth('verifikator')->user()->nama,
            'adminNip'       => auth('verifikator')->user()->nip,
            'mode'           => 'create',
            'pegawai'        => null,
            'daftarSekolah'  => Sekolah::orderBy('nama_sekolah')->get(),
            'daftarGolongan' => self::GOLONGAN,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request, isCreate: true);
        Pegawai::create($validated);

        return redirect()->route('admin.data-master.pegawai.index')->with('success', 'Pegawai baru berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('dashboard.admin.pegawai.form', [
            'adminNama'      => auth('verifikator')->user()->nama,
            'adminNip'       => auth('verifikator')->user()->nip,
            'mode'           => 'edit',
            'pegawai'        => $pegawai,
            'daftarSekolah'  => Sekolah::orderBy('nama_sekolah')->get(),
            'daftarGolongan' => self::GOLONGAN,
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $this->validated($request, isCreate: false, nipLama: $pegawai->nip);
        $pegawai->update($validated);

        return redirect()->route('admin.data-master.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();

        return redirect()->route('admin.data-master.pegawai.index')->with('success', "Data pegawai {$nama} telah dihapus.");
    }

    private function validated(Request $request, bool $isCreate, ?string $nipLama = null): array
    {
        return $request->validate([
            'nip'             => ['required', 'digits:18', $isCreate ? 'unique:pegawai,nip' : 'in:' . $nipLama],
            'nama'            => ['required', 'string', 'max:150'],
            'status'          => ['required', 'in:PNS,PPPK'],
            'npsn'            => ['required', 'exists:sekolah,npsn'],
            'pangkat_jabatan' => ['nullable', 'string', 'max:150'],
            'golongan'        => ['nullable', 'in:' . implode(',', self::GOLONGAN)],
            'kecamatan'       => ['nullable', 'string', 'max:100'],
            'tmt'             => ['nullable', 'date'],
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar.',
        ]);
    }
}
