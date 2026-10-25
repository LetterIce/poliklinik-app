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
     * Keamanan: Dokter hanya dapat memanggil antrean yang terjadwal untuk dirinya
     */
    public function panggilAntrian($id)
    {
        $daftarPoli = DaftarPoli::with(['pasien', 'jadwalPeriksa.dokter.poli'])
            ->whereHas('jadwalPeriksa', function ($q) {
                $q->where('id_dokter', Auth::id());
            })
            ->findOrFail($id);

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
            $daftarPoli->pasien->nama,
            $daftarPoli->no_antrian,
            'panggil',
            $daftarPoli->jadwalPeriksa?->id_dokter
        );

        return redirect()->back()->with('success', "Nomor antrean #{$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}) berhasil dipanggil ke ruang periksa!");
    }

    public function create($id)
    {
        $daftarPoli = DaftarPoli::with(['pasien', 'jadwalPeriksa.dokter.poli'])
            ->whereHas('jadwalPeriksa', function ($q) {
                $q->where('id_dokter', Auth::id());
            })
            ->findOrFail($id);

        if ($daftarPoli->periksas()->exists()) {
            return redirect()->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah selesai diperiksa sebelumnya.');
        }

        $obats = Obat::all();

        // Broadcast bahwa nomor antrean ini sedang dipanggil/dilayani
        AntrianUpdated::dispatchSafe(
            $daftarPoli->id_jadwal,
            $daftarPoli->no_antrian,
            "Nomor antrean {$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}) sedang dilayani.",
            $daftarPoli->id_pasien,
            $daftarPoli->pasien->nama,
            $daftarPoli->no_antrian,
            'layani',
            $daftarPoli->jadwalPeriksa?->id_dokter
        );

        $biayaDokterDefault = Auth::user()->tarif ?? 150000;

        return view('dokter.periksa-pasien.create', compact('obats', 'id', 'daftarPoli', 'biayaDokterDefault'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'biaya_dokter' => 'nullable|numeric|min:0',
            'obat_json' => 'nullable',
            'catatan' => 'nullable|string|max:2000',
        ]);

        // Verifikasi kepemilikan: Hanya dokter yang ditugaskan yang dapat memeriksa
        $daftarPoli = DaftarPoli::with('jadwalPeriksa')
            ->whereHas('jadwalPeriksa', function ($q) {
                $q->where('id_dokter', Auth::id());
            })
            ->findOrFail($request->id_daftar_poli);

        // Mencegah pemeriksaan ganda (idempotency check)
        if ($daftarPoli->periksas()->exists()) {
            return redirect()->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah selesai diperiksa sebelumnya.');
        }

        $obatIds = json_decode($request->obat_json, true) ?: [];
        if (!is_array($obatIds)) {
            $obatIds = [];
        }

        // Validasi ketersediaan stok obat
        if (!empty($obatIds)) {
            $outOfStock = Obat::whereIn('id', $obatIds)->where('stok', '<=', 0)->pluck('nama_obat')->toArray();
            if (!empty($outOfStock)) {
                return redirect()->back()
                    ->with('error', 'Obat berikut saat ini habis stok: ' . implode(', ', $outOfStock))
                    ->withInput();
            }
        }

        // Biaya Dokter dinamis (dari input form dokter, profil dokter, atau default)
        $biayaDokter = $request->filled('biaya_dokter')
            ? (int) $request->biaya_dokter
            : (int) (Auth::user()->tarif ?? 150000);

        // Keamanan: Hitung biaya secara server-side (Tarif dokter + Total Obat) untuk mencegah manipulasi client-side
        $totalBiayaObat = !empty($obatIds) ? (int) Obat::whereIn('id', $obatIds)->sum('harga') : 0;
        $totalBiayaPeriksa = $biayaDokter + $totalBiayaObat;

        $periksa = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $obatIds, $biayaDokter, $totalBiayaPeriksa, $daftarPoli) {
            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa' => now(),
                'catatan' => $request->catatan,
                'biaya_dokter' => $biayaDokter,
                'biaya_periksa' => $totalBiayaPeriksa,
            ]);

            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);

                // Kurangi stok obat otomatis secara aman dengan row locking
                $obat = Obat::where('id', $idObat)->lockForUpdate()->first();
                if ($obat && $obat->stok > 0) {
                    $obat->decrement('stok', 1);
                }
            }

            return $periksa;
        });

        // Cari nomor antrean berikutnya yang belum diperiksa pada jadwal ini
        $nextNomor = DaftarPoli::where('id_jadwal', $daftarPoli->id_jadwal)
            ->doesntHave('periksas')
            ->orderBy('no_antrian', 'asc')
            ->value('no_antrian');

        // Broadcast real-time update ke dashboard pasien via Reverb
        AntrianUpdated::dispatchSafe(
            $daftarPoli->id_jadwal,
            $nextNomor ?? 'Selesai',
            null,
            null,
            null,
            null,
            'selesai',
            $daftarPoli->jadwalPeriksa?->id_dokter
        );

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }
}