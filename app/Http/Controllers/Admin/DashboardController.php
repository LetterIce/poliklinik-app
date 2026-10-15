<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Pembayaran;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilan utama Dashboard Administrator
     */
    public function index()
    {
        $admin = Auth::user();

        // 1. Metrik Utama Poliklinik
        $dokterCount = User::where('role', 'dokter')->count();
        $pasienCount = User::where('role', 'pasien')->count();
        $poliCount = Poli::count();
        $obatCount = Obat::count();

        // 2. Transaksi & Keuangan
        $pembayaranPendingCount = Pembayaran::where('status', 'menunggu_verifikasi')->count();
        $pembayaranLunasCount = Pembayaran::where('status', 'lunas')->count();
        $totalPendapatan = Pembayaran::where('status', 'lunas')->sum('jumlah_bayar');
        $pemeriksaanBulanIni = Periksa::whereMonth('tgl_periksa', now()->month)
            ->whereYear('tgl_periksa', now()->year)
            ->count();

        // 3. Pembayaran Menunggu Verifikasi (Aksi Cepat Admin)
        $pembayaranMenunggu = Pembayaran::where('status', 'menunggu_verifikasi')
            ->with(['pasien', 'periksa.daftarPoli.jadwalPeriksa.dokter.poli'])
            ->latest('tgl_bayar')
            ->take(5)
            ->get();

        // 4. Aktivitas Pemeriksaan Terbaru (Semua Poli)
        $riwayatPemeriksaanTerbaru = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'pembayaran'
        ])->latest('tgl_periksa')->take(6)->get();

        // 5. Poliklinik Overview
        $polis = Poli::withCount('dokters')->get();

        // 6. Peringatan Stok Obat Menipis (< 20)
        $obatMenipis = Obat::where('stok', '<', 20)
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'admin',
            'dokterCount',
            'pasienCount',
            'poliCount',
            'obatCount',
            'pembayaranPendingCount',
            'pembayaranLunasCount',
            'totalPendapatan',
            'pemeriksaanBulanIni',
            'pembayaranMenunggu',
            'riwayatPemeriksaanTerbaru',
            'polis',
            'obatMenipis'
        ));
    }
}
