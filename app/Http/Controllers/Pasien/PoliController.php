<?php

namespace App\Http\Controllers\Pasien;

use App\Events\AntrianUpdated;
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
        $pasienId = Auth::id();

        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string|max:1000',
        ]);

        // Validasi integritas relasi: Pastikan jadwal yang dipilih sesuai dengan poli
        $jadwal = JadwalPeriksa::with('dokter.poli')->findOrFail($request->id_jadwal);
        if (!$jadwal->dokter || (string)$jadwal->dokter->id_poli !== (string)$request->id_poli) {
            return redirect()
                ->back()
                ->with('error', 'Jadwal dokter yang dipilih tidak sesuai dengan poli yang dipilih.')
                ->withInput();
        }

        // Ketentuan: Pasien tidak dapat mendaftar ke dua poli sekaligus & hanya dapat mendaftar kembali setelah proses periksa selesai
        $antrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->doesntHave('periksas')
            ->first();

        if ($antrianAktif) {
            return redirect()
                ->back()
                ->with('error', 'Anda masih memiliki antrean aktif yang belum selesai diperiksa. Anda tidak dapat mendaftar ke dua poli sekaligus.')
                ->withInput();
        }

        // Penomoran antrean yang aman dari race condition dengan DB Transaction
        $daftarPoli = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $pasienId) {
            $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)
                ->lockForUpdate()
                ->count();

            return DaftarPoli::create([
                'id_pasien' => $pasienId,
                'id_jadwal' => $request->id_jadwal,
                'keluhan' => $request->keluhan,
                'no_antrian' => $jumlahSudahDaftar + 1,
            ]);
        });

        $daftarPoli->load(['pasien', 'jadwalPeriksa.dokter.poli']);

        // Kirim notifikasi database ke akun dokter yang bertugas
        if ($daftarPoli->jadwalPeriksa && $daftarPoli->jadwalPeriksa->dokter) {
            try {
                $daftarPoli->jadwalPeriksa->dokter->notify(new \App\Notifications\PasienBaruMendaftarNotification($daftarPoli));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi pasien baru ke dokter: ' . $e->getMessage());
            }
        }

        // Broadcast real-time antrian via Reverb ke dokter dan pasien
        AntrianUpdated::dispatchSafe(
            $request->id_jadwal,
            null,
            "Pasien baru mendaftar: {$daftarPoli->pasien->nama} (No. Antrean #{$daftarPoli->no_antrian})",
            $daftarPoli->id_pasien,
            $daftarPoli->pasien->nama,
            $daftarPoli->no_antrian,
            'daftar',
            $daftarPoli->jadwalPeriksa?->id_dokter
        );

        return redirect()
            ->route('pasien.dashboard')
            ->with('message', 'Berhasil mendaftar ke poli! Silakan pantau nomor antrian Anda di dashboard.')
            ->with('type', 'success');
    }
}