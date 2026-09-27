<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Sekolah;
use App\Models\Verifikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Halaman penugasan sekolah ke verifikator — sekarang benar-benar
     * membaca & menyimpan relasi sekolah.verifikator_nip.
     */
    public function penugasan(Request $request)
    {
        $query = Sekolah::query();

        if ($kecamatan = $request->query('kecamatan')) {
            $query->where('kecamatan', $kecamatan);
        }
        if ($statusTugas = $request->query('status_tugas')) {
            $statusTugas === 'assigned'
                ? $query->whereNotNull('verifikator_nip')
                : $query->whereNull('verifikator_nip');
        }

        $daftarSekolah = $query->with('verifikator')->orderBy('nama_sekolah')->get();

        return view('dashboard.admin.penugasan', [
            'adminNama' => Auth::guard('verifikator')->user()->nama,
            'adminNip'  => Auth::guard('verifikator')->user()->nip,

            'daftarSekolah'     => $daftarSekolah->map(fn ($s) => [
                'npsn'        => $s->npsn,
                'nama'        => $s->nama_sekolah,
                'status'      => $s->status,
                'jenjang'     => $s->jenjang,
                'kecamatan'   => $s->kecamatan,
                'verifikator' => $s->verifikator->nama ?? null,
            ]),
            'daftarVerifikator' => Verifikator::where('is_admin', false)->orderBy('nama')->get()
                ->map(fn ($v) => ['id' => $v->nip, 'nama' => $v->nama, 'kecamatan' => $v->kecamatan]),
            'daftarKecamatan'   => Sekolah::whereNotNull('kecamatan')->distinct()->pluck('kecamatan'),
        ]);
    }

    /**
     * Simpan penugasan verifikator untuk satu sekolah — sekarang benar-benar tersimpan.
     */
    public function updatePenugasan(Request $request, string $npsn)
    {
        $request->validate([
            'verifikator_id' => ['nullable', 'exists:verifikators,nip'],
        ]);

        $sekolah = Sekolah::findOrFail($npsn);
        $sekolah->update(['verifikator_nip' => $request->input('verifikator_id') ?: null]);

        return redirect()
            ->route('admin.penugasan')
            ->with('success', "Penugasan verifikator untuk {$sekolah->nama_sekolah} berhasil disimpan.");
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
