<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $daftarPegawai = Pegawai::with('sekolah')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nip', 'like', "%{$q}%")
                        ->orWhere('nama', 'like', "%{$q}%")
                        ->orWhere('unit_kerja', 'like', "%{$q}%");
                });
            })
            ->when($request->boolean('belum_terhubung'), fn ($query) => $query->whereNull('npsn'))
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('dashboard.admin.pegawai.index', [
            'adminNama'     => auth('verifikator')->user()->nama,
            'adminNip'      => auth('verifikator')->user()->nip,
            'daftarPegawai' => $daftarPegawai,
            'q'             => $q,
            'totalBelumTerhubung' => Pegawai::whereNull('npsn')->count(),
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.pegawai.form', [
            'adminNama'     => auth('verifikator')->user()->nama,
            'adminNip'      => auth('verifikator')->user()->nip,
            'mode'          => 'create',
            'pegawai'       => null,
            'daftarSekolah' => Sekolah::orderBy('nama_sekolah')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->isiUnitKerja($this->validated($request, isCreate: true));
        Pegawai::create($validated);

        return redirect()->route('admin.data-master.pegawai.index')->with('success', 'Pegawai baru berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('dashboard.admin.pegawai.form', [
            'adminNama'     => auth('verifikator')->user()->nama,
            'adminNip'      => auth('verifikator')->user()->nip,
            'mode'          => 'edit',
            'pegawai'       => $pegawai,
            'daftarSekolah' => Sekolah::orderBy('nama_sekolah')->get(),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $this->isiUnitKerja($this->validated($request, isCreate: false, nipLama: $pegawai->nip));
        $pegawai->update($validated);

        return redirect()->route('admin.data-master.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();

        return redirect()->route('admin.data-master.pegawai.index')->with('success', "Data pegawai {$nama} telah dihapus.");
    }

    /**
     * Kalau Unit Kerja dikosongkan tapi sekolahnya dipilih, isi otomatis
     * dengan nama sekolah tersebut.
     */
    private function isiUnitKerja(array $data): array
    {
        if (empty($data['unit_kerja']) && ! empty($data['npsn'])) {
            $data['unit_kerja'] = Sekolah::find($data['npsn'])?->nama_sekolah;
        }

        return $data;
    }

    private function validated(Request $request, bool $isCreate, ?string $nipLama = null): array
    {
        return $request->validate([
            'nip'                  => ['required', 'digits:18', $isCreate ? 'unique:pegawai,nip' : 'in:' . $nipLama],
            'nama'                 => ['required', 'string', 'max:255'],
            'tempat_tanggal_lahir' => ['nullable', 'string', 'max:150'],

            'unit_kerja'           => ['nullable', 'string', 'max:200'],
            'npsn'                 => ['nullable', 'exists:sekolah,npsn'],

            'golongan_pangkat'     => ['nullable', 'string', 'max:100'],
            'tmt_golongan'         => ['nullable', 'date'],
            'eselon'               => ['nullable', 'string', 'max:20'],
            'nama_jabatan'         => ['nullable', 'string', 'max:200'],
            'tmt_jabatan'          => ['nullable', 'date'],
            'status_pegawai'       => ['nullable', 'string', 'max:30'],
            'tmt_pegawai'          => ['nullable', 'date'],
            'masa_kerja_tahun'     => ['nullable', 'integer', 'min:0', 'max:60'],
            'masa_kerja_bulan'     => ['nullable', 'integer', 'min:0', 'max:11'],

            'jenis_kelamin'        => ['nullable', 'string', 'max:20'],
            'agama'                => ['nullable', 'string', 'max:30'],
            'status_perkawinan'    => ['nullable', 'string', 'max:30'],
            'pendidikan_awal'      => ['nullable', 'string', 'max:100'],
            'pendidikan_akhir'     => ['nullable', 'string', 'max:100'],
            'no_npwp'              => ['nullable', 'string', 'max:30'],
            'no_ktp'               => ['nullable', 'string', 'max:20'],

            'alamat_rumah'         => ['nullable', 'string', 'max:500'],
            'telp'                 => ['nullable', 'string', 'max:30'],
            'hp'                   => ['nullable', 'string', 'max:30'],
            'email'                => ['nullable', 'email', 'max:150'],
        ], [
            'nip.unique'   => 'NIP ini sudah terdaftar.',
            'npsn.exists'  => 'Sekolah yang dipilih tidak ditemukan.',
            'email.email'  => 'Format e-mail tidak valid.',
        ]);
    }
}
