<?php

namespace App\Events;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $idJadwal;
    public $nomorSedangDilayani;
    public $namaPoli;
    public $namaDokter;
    public $jadwals;
    public $message;
    public $idPasien;
    public $namaPasien;

    /**
     * Broadcast event secara aman tanpa membuat aplikasi error jika Reverb belum jalan
     */
    public static function dispatchSafe(...$args): void
    {
        try {
            broadcast(new static(...$args));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('WebSocket Reverb offline/unreachable: ' . $e->getMessage());
        }
    }

    /**
     * Create a new event instance.
     */
    public function __construct($idJadwal = null, $nomorSedangDilayani = null, $message = null, $idPasien = null, $namaPasien = null)
    {
        $this->idJadwal = $idJadwal;
        $this->nomorSedangDilayani = $nomorSedangDilayani;
        $this->message = $message;
        $this->idPasien = $idPasien;
        $this->namaPasien = $namaPasien;

        if ($idJadwal) {
            $jadwal = JadwalPeriksa::with('dokter.poli')->find($idJadwal);
            if ($jadwal) {
                $this->namaDokter = optional($jadwal->dokter)->nama ?? 'Dokter';
                $this->namaPoli = optional($jadwal->dokter->poli)->nama_poli ?? 'Poliklinik';
            }
        }

        // Ambil status terkini seluruh jadwal untuk update tabel live
        $this->jadwals = JadwalPeriksa::select('id')->get()->map(function ($j) {
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
        })->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-channel'),
        ];
    }

    /**
     * Nama event yang didengarkan oleh client
     */
    public function broadcastAs(): string
    {
        return 'AntrianUpdated';
    }

    /**
     * Data yang dikirimkan ke WebSocket client
     */
    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'nomor_sedang_dilayani' => $this->nomorSedangDilayani,
            'nama_poli' => $this->namaPoli,
            'nama_dokter' => $this->namaDokter,
            'jadwals' => $this->jadwals,
            'message' => $this->message,
            'id_pasien' => $this->idPasien,
            'nama_pasien' => $this->namaPasien,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
