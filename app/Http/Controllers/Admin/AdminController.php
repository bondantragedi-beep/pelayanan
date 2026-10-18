<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Sekolah;
use App\Models\Verifikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Halaman ringkasan admin — sekarang mengambil angka sungguhan dari database.
     */
    public function index(Request $request)
    {
        $dataMasukTerbaru = Pengajuan::with('sekolah')->latest()->take(5)->get();

        return view('dashboard.admin.index', [
            'adminNama' => Auth::guard('verifikator')->user()->nama,
            'adminNip'  => Auth::guard('verifikator')->user()->nip,

            'totalSekolah'           => Sekolah::count(),
            'totalVerifikator'       => Verifikator::where('is_admin', false)->count(),
            'belumDiproses'          => Pengajuan::where('status', 'pending')->count(),
            'sudahDiproses'          => Pengajuan::where('status', 'processed')->whereDate('diproses_pada', today())->count(),
            'sekolahBelumDitugaskan' => Sekolah::whereNull('verifikator_nip')->count(),

            'dataMasukTerbaru' => $dataMasukTerbaru->map(fn ($p) => [
                'sekolah'     => $p->sekolah->nama_sekolah ?? '—',
                'layanan'     => $p->labelLayanan(),
                'verifikator' => $p->sekolah->verifikator->nama ?? null,
                'tanggal'     => $p->created_at->translatedFormat('d M Y'),
                'status'      => $p->status,
            ]),
        ]);
    }

    /**
     * Halaman penugasan sekolah ke verifikator.
     *
     * Semua sekolah dimuat sekaligus (sekitar 370 baris, ringan). Penyaringan
     * (jenjang, kecamatan, status penugasan, pencarian) dan pencentangan
     * dilakukan di browser lewat public/js/penugasan.js supaya instan, lalu
     * hasil centangan dikirim sekali ke updatePenugasanMassal().
     */
    public function penugasan(Request $request)
    {
        $sekolah = Sekolah::with('verifikator:nip,nama')
            ->orderBy('kecamatan')
            ->orderBy('nama_sekolah')
            ->get();

        $verifikator = Verifikator::where('is_admin', false)->orderBy('nama')->get();

        // Beban kerja tiap verifikator: jumlah sekolah binaan, dipecah per jenjang.
        $milikVerifikator = $sekolah->whereNotNull('verifikator_nip')->groupBy('verifikator_nip');
        $daftarVerifikator = $verifikator->map(function ($v) use ($milikVerifikator) {
            $milik = $milikVerifikator->get($v->nip, collect());

            return [
                'nip'         => $v->nip,
                'nama'        => $v->nama,
                'total'       => $milik->count(),
                'per_jenjang' => $milik->countBy('jenjang')->all(),
            ];
        })->values()->all();

        // Jumlah sekolah per jenjang (SD, SMP, ...).
        $daftarJenjang = $sekolah->countBy('jenjang')->sortKeys()->all();

        // Jumlah sekolah per kecamatan, beserta yang belum ditugaskan. Kecamatan kosong ditaruh paling akhir.
        $daftarKecamatan = $sekolah->groupBy(fn ($s) => (string) $s->kecamatan)
            ->map(fn ($g) => ['total' => $g->count(), 'belum' => $g->whereNull('verifikator_nip')->count()])
            ->sortBy(fn ($v, $k) => $k === '' ? "\u{10FFFF}" : mb_strtolower($k))
            ->all();

        $sudah = $sekolah->whereNotNull('verifikator_nip')->count();

        return view('dashboard.admin.penugasan', [
            'adminNama' => Auth::guard('verifikator')->user()->nama,
            'adminNip'  => Auth::guard('verifikator')->user()->nip,

            'daftarSekolah'     => $sekolah,
            'daftarVerifikator' => $daftarVerifikator,
            'daftarJenjang'     => $daftarJenjang,
            'daftarKecamatan'   => $daftarKecamatan,
            'ringkasan'         => [
                'total' => $sekolah->count(),
                'sudah' => $sudah,
                'belum' => $sekolah->count() - $sudah,
            ],
        ]);
    }

    /**
     * Tugaskan banyak sekolah sekaligus ke satu verifikator, atau lepaskan
     * penugasannya (aksi = lepas). Dikirim dari halaman Penugasan Sekolah.
     */
    public function updatePenugasanMassal(Request $request)
    {
        $request->validate([
            'aksi'           => ['required', 'in:tugaskan,lepas'],
            'npsn'           => ['required', 'array', 'min:1', 'max:2000'],
            'npsn.*'         => ['string', 'max:20'],
            'verifikator_id' => ['required_if:aksi,tugaskan', 'nullable', 'string'],
        ], [
            'npsn.required'              => 'Centang minimal satu sekolah dulu.',
            'npsn.min'                   => 'Centang minimal satu sekolah dulu.',
            'verifikator_id.required_if' => 'Pilih verifikator tujuan dulu.',
        ]);

        $npsn = array_values(array_unique($request->input('npsn')));
        $nip = null;
        $nama = null;

        if ($request->input('aksi') === 'tugaskan') {
            // Hanya akun verifikator biasa yang boleh dipilih (bukan admin).
            $verifikator = Verifikator::where('is_admin', false)->find($request->input('verifikator_id'));
            if (! $verifikator) {
                throw ValidationException::withMessages(['verifikator_id' => 'Verifikator yang dipilih tidak ditemukan.']);
            }
            $nip = $verifikator->nip;
            $nama = $verifikator->nama;
        }

        $jumlah = 0;
        foreach (array_chunk($npsn, 500) as $bagian) {
            $jumlah += Sekolah::whereIn('npsn', $bagian)->count();
            Sekolah::whereIn('npsn', $bagian)->update(['verifikator_nip' => $nip]);
        }

        $pesan = $nip
            ? "{$jumlah} sekolah berhasil ditugaskan ke {$nama}."
            : "Penugasan {$jumlah} sekolah berhasil dilepas.";

        return redirect()->route('admin.penugasan')->with('success', $pesan);
    }

    /**
     * Halaman data masuk & monitoring — sekarang menarik dari tabel pengajuan asli.
     */
    public function dataMasuk(Request $request)
    {
        $query = Pengajuan::with('sekolah.verifikator');

        if ($tanggal = $request->query('tanggal')) {
            $query->whereDate('created_at', $tanggal);
        }
        if ($bulan = $request->query('bulan')) {
            $query->whereMonth('created_at', $bulan);
        }
        if ($tahun = $request->query('tahun')) {
            $query->whereYear('created_at', $tahun);
        }
        if ($status = $request->query('status')) {
            $status === 'pending'
                ? $query->where('status', 'pending')
                : ($status === 'rejected'
                    ? $query->where('status', 'rejected')
                    : $query->where('status', 'processed'));
        }

        $dataMasuk = $query->oldest()->get()->map(fn ($p) => [
            'sekolah'     => $p->sekolah->nama_sekolah ?? '—',
            'layanan'     => $p->labelLayanan(),
            'verifikator' => $p->diproses_oleh_nip
                ? (Verifikator::find($p->diproses_oleh_nip)->nama ?? null)
                : ($p->sekolah->verifikator->nama ?? null),
            'tanggal' => $p->created_at->toDateString(),
            'status'  => $p->status,
        ]);

        return view('dashboard.admin.data-masuk', [
            'adminNama' => Auth::guard('verifikator')->user()->nama,
            'adminNip'  => Auth::guard('verifikator')->user()->nip,

            'dataMasuk'   => $dataMasuk,
            'daftarTahun' => [date('Y'), date('Y') - 1],
        ]);
    }
}
