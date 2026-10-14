<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilan utama Dashboard Pasien
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Antrian aktif pasien yang belum selesai diperiksa
        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->doesntHave('periksas')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->latest()
            ->first();

        // Nomor yang sedang dilayani untuk antrian aktif ini
        $sedangDilayaniAktif = '-';
        if ($antrianAktif) {
            $nomor = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                ->doesntHave('periksas')
                ->orderBy('no_antrian', 'asc')
                ->value('no_antrian');
            
            $sedangDilayaniAktif = $nomor ?? 'Selesai';
        }

        // 2. Daftar jadwal poliklinik dan dokter beserta status nomor yang sedang dilayani
        $jadwals = JadwalPeriksa::with(['dokter.poli'])->get()->map(function ($jadwal) {
            $sedang = DaftarPoli::where('id_jadwal', $jadwal->id)
                ->doesntHave('periksas')
                ->orderBy('no_antrian', 'asc')
                ->value('no_antrian');

            if (!$sedang) {
                $adaSelesai = DaftarPoli::where('id_jadwal', $jadwal->id)->has('periksas')->exists();
                $sedang = $adaSelesai ? 'Selesai' : '-';
            }

            $jadwal->nomor_sedang_dilayani = $sedang;
            return $jadwal;
        });

        return view('pasien.dashboard', compact('user', 'antrianAktif', 'sedangDilayaniAktif', 'jadwals'));
    }

    /**
     * Endpoint API untuk Live Update nomor antrian tanpa reload halaman
     */
    public function statusAntrian()
    {
        $user = Auth::user();

        // Cek antrian aktif pasien
        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->doesntHave('periksas')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->latest()
            ->first();

        $activeQueueData = null;
        if ($antrianAktif) {
            $nomorSedangDilayani = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                ->doesntHave('periksas')
                ->orderBy('no_antrian', 'asc')
                ->value('no_antrian');

            $jamMulai = date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_mulai));
            $jamSelesai = date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_selesai));

            $activeQueueData = [
                'id' => $antrianAktif->id,
                'poli' => optional($antrianAktif->jadwalPeriksa->dokter->poli)->nama_poli ?? 'Poliklinik',
                'dokter' => optional($antrianAktif->jadwalPeriksa->dokter)->nama ?? 'Dokter',
                'jadwal' => $antrianAktif->jadwalPeriksa->hari . ' (' . $jamMulai . ' - ' . $jamSelesai . ')',
                'nomor_anda' => $antrianAktif->no_antrian,
                'sedang_dilayani' => $nomorSedangDilayani ?? 'Selesai',
            ];
        }

        // Status per jadwal
        $jadwalStatus = JadwalPeriksa::select('id')->get()->map(function ($j) {
            $sedang = DaftarPoli::where('id_jadwal', $j->id)
                ->doesntHave('periksas')
                ->orderBy('no_antrian', 'asc')
                ->value('no_antrian');

            if (!$sedang) {
                $adaSelesai = DaftarPoli::where('id_jadwal', $j->id)->has('periksas')->exists();
                $sedang = $adaSelesai ? 'Selesai' : '-';
            }

            return [
                'id' => $j->id,
                'sedang_dilayani' => $sedang,
            ];
        });

        return response()->json([
            'status' => 'success',
            'has_active_queue' => !is_null($activeQueueData),
            'active_queue' => $activeQueueData,
            'jadwals' => $jadwalStatus,
        ]);
    }
}
