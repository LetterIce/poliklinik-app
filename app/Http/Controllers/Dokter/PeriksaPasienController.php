<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianUpdated;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    /**
     * Panggil antrean pasien secara real-time via WebSocket Reverb
     */
    public function panggilAntrian($id)
    {
        $daftarPoli = DaftarPoli::with(['pasien', 'jadwalPeriksa.dokter.poli'])->findOrFail($id);

        // Kirim notifikasi database ke akun pasien
        try {
            $daftarPoli->pasien->notify(new \App\Notifications\AntrianDipanggilNotification($daftarPoli));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi panggil pasien: ' . $e->getMessage());
        }

        // Siarkan secara real-time via WebSocket Reverb
        AntrianUpdated::dispatchSafe(
            $daftarPoli->id_jadwal,
            $daftarPoli->no_antrian,
            "Nomor antrean {$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}) sedang dipanggil.",
            $daftarPoli->id_pasien,
            $daftarPoli->pasien->nama
        );

        return redirect()->back()->with('success', "Nomor antrean #{$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}) berhasil dipanggil ke ruang periksa!");
    }

    public function create($id)
    {
        $daftarPoli = DaftarPoli::with(['pasien', 'jadwalPeriksa.dokter.poli'])->findOrFail($id);
        $obats = Obat::all();

        // Broadcast bahwa nomor antrean ini sedang dipanggil/dilayani
        AntrianUpdated::dispatchSafe(
            $daftarPoli->id_jadwal,
            $daftarPoli->no_antrian,
            "Nomor antrean {$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}) sedang dilayani.",
            $daftarPoli->id_pasien,
            $daftarPoli->pasien->nama
        );

        return view('dokter.periksa-pasien.create', compact('obats', 'id', 'daftarPoli'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_json' => 'required',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        $periksa = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $obatIds) {
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa' => now(),
                'catatan' => $request->catatan,
                'biaya_periksa' => $request->biaya_periksa + 150000,
            ]);

            if (is_array($obatIds)) {
                foreach ($obatIds as $idObat) {
                    DetailPeriksa::create([
                        'id_periksa' => $periksa->id,
                        'id_obat' => $idObat,
                    ]);

                    // Kurangi stok obat otomatis
                    $obat = Obat::find($idObat);
                    if ($obat && $obat->stok > 0) {
                        $obat->decrement('stok', 1);
                    }
                }
            }

            return $periksa;
        });

        // Cari nomor antrean berikutnya yang belum diperiksa pada jadwal ini
        $daftarPoli = DaftarPoli::find($request->id_daftar_poli);
        if ($daftarPoli) {
            $nextNomor = DaftarPoli::where('id_jadwal', $daftarPoli->id_jadwal)
                ->doesntHave('periksas')
                ->orderBy('no_antrian', 'asc')
                ->value('no_antrian');

            // Broadcast real-time update ke dashboard pasien via Reverb
            AntrianUpdated::dispatchSafe($daftarPoli->id_jadwal, $nextNomor ?? 'Selesai');
        }

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }
}