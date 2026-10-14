<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPendaftaranController extends Controller
{
    /**
     * Tampilkan riwayat semua pendaftaran poli pasien yang sedang login
     * Menggunakan eager loading untuk mencegah query berlebihan (N+1 query problem)
     */
    public function index()
    {
        $pasienId = Auth::id();

        $riwayatPendaftaran = DaftarPoli::where('id_pasien', $pasienId)
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksas.detailPeriksas.obat',
                'periksas.pembayaran'
            ])
            ->latest('id')
            ->paginate(10);

        return view('pasien.riwayat-daftar.index', compact('riwayatPendaftaran'));
    }

    /**
     * Tampilkan halaman detail hasil pemeriksaan untuk pendaftaran yang sudah selesai
     * Ketentuan keamanan: Pasien hanya dapat melihat pendaftaran miliknya sendiri
     */
    public function show($id)
    {
        $pasienId = Auth::id();

        $daftar = DaftarPoli::where('id_pasien', $pasienId)
            ->with([
                'pasien',
                'jadwalPeriksa.dokter.poli',
                'periksas.detailPeriksas.obat',
                'periksas.pembayaran'
            ])
            ->findOrFail($id);

        $periksa = $daftar->periksas->first();

        if (!$periksa) {
            return redirect()->route('pasien.riwayat-daftar.index')
                ->with('error', 'Pendaftaran ini belum diperiksa oleh dokter.');
        }

        return view('pasien.riwayat-daftar.show', compact('daftar', 'periksa'));
    }
}
