<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        // Cek apakah pasien masih memiliki antrian aktif (belum selesai diperiksa)
        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->doesntHave('periksas')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->latest()
            ->first();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
            'antrianAktif' => $antrianAktif,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
            'id_pasien' => 'required|exists:users,id',
        ]);

        // Ketentuan: Pasien tidak dapat mendaftar ke dua poli sekaligus & hanya dapat mendaftar kembali setelah proses periksa selesai
        $antrianAktif = DaftarPoli::where('id_pasien', $request->id_pasien)
            ->doesntHave('periksas')
            ->first();

        if ($antrianAktif) {
            return redirect()
                ->back()
                ->with('error', 'Anda masih memiliki antrean aktif yang belum selesai diperiksa. Anda tidak dapat mendaftar ke dua poli sekaligus.')
                ->withInput();
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        DaftarPoli::create([
            'id_pasien' => $request->id_pasien,
            'id_jadwal' => $request->id_jadwal,
            'keluhan' => $request->keluhan,
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        return redirect()
            ->route('pasien.dashboard')
            ->with('message', 'Berhasil mendaftar ke poli! Silakan pantau nomor antrian Anda di dashboard.')
            ->with('type', 'success');
    }
}