<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard Dokter
     */
    public function index()
    {
        $dokter = Auth::user();
        $dokter->load('poli');

        // Ambil ID semua jadwal milik dokter ini
        $jadwalIds = JadwalPeriksa::where('id_dokter', $dokter->id)->pluck('id');

        // Konversi nama hari sekarang ke Bahasa Indonesia
        $hariMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $hariIni = $hariMap[(int) date('N')] ?? 'Senin';

        // Jadwal praktik dokter pada hari ini
        $jadwalHariIni = JadwalPeriksa::where('id_dokter', $dokter->id)
            ->where('hari', $hariIni)
            ->first();

        // 1. Antrean pasien yang menunggu diperiksa
        $antreanMenunggu = DaftarPoli::whereIn('id_jadwal', $jadwalIds)
            ->doesntHave('periksas')
            ->with(['pasien', 'jadwalPeriksa'])
            ->orderBy('id_jadwal')
            ->orderBy('no_antrian', 'asc')
            ->get();

        // 2. Jumlah pasien yang selesai diperiksa hari ini
        $selesaiHariIni = Periksa::whereHas('daftarPoli', function ($q) use ($jadwalIds) {
            $q->whereIn('id_jadwal', $jadwalIds);
        })->whereDate('tgl_periksa', today())->count();

        // 3. Total seluruh pasien yang pernah diperiksa oleh dokter ini
        $totalPasienDitangani = Periksa::whereHas('daftarPoli', function ($q) use ($jadwalIds) {
            $q->whereIn('id_jadwal', $jadwalIds);
        })->count();

        // 4. Riwayat pemeriksaan terakhir (5 teratas)
        $riwayatTerakhir = Periksa::whereHas('daftarPoli', function ($q) use ($jadwalIds) {
            $q->whereIn('id_jadwal', $jadwalIds);
        })->with(['daftarPoli.pasien', 'detailPeriksas.obat'])
          ->latest('tgl_periksa')
          ->take(5)
          ->get();

        // 5. Seluruh jadwal dokter (urut berdasarkan hari & jam)
        $urutanHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
        $jadwals = JadwalPeriksa::where('id_dokter', $dokter->id)
            ->get()
            ->sortBy(function ($j) use ($urutanHari) {
                return sprintf('%02d_%s', $urutanHari[$j->hari] ?? 8, $j->jam_mulai);
            });

        // Nomor antrean terdepan yang belum dilayani pada jadwal hari ini
        $antreanHariIni = $jadwalHariIni 
            ? $antreanMenunggu->where('id_jadwal', $jadwalHariIni->id)->first() 
            : $antreanMenunggu->first();

        $nomorSedangDipanggil = $antreanHariIni ? $antreanHariIni->no_antrian : '-';

        return view('dokter.dashboard', compact(
            'dokter',
            'jadwalHariIni',
            'antreanMenunggu',
            'selesaiHariIni',
            'totalPasienDitangani',
            'riwayatTerakhir',
            'jadwals',
            'hariIni',
            'nomorSedangDipanggil'
        ));
    }
}
