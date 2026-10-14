<?php

namespace App\Notifications;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranDiverifikasiNotification extends Notification
{
    use Queueable;

    public $pembayaran;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pembayaran $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $periksa = $this->pembayaran->periksa;
        $poli = optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? 'Poliklinik';
        $dokter = optional($periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? 'Dokter';
        $jumlah = number_format($this->pembayaran->jumlah_bayar, 0, ',', '.');

        return [
            'id_pembayaran' => $this->pembayaran->id,
            'id_periksa' => $this->pembayaran->id_periksa,
            'title' => 'Pembayaran Terverifikasi (Lunas)',
            'message' => "Pembayaran pemeriksaan Anda di {$poli} ({$dokter}) sebesar Rp {$jumlah} telah diverifikasi (LUNAS) oleh Admin.",
            'tipe' => 'pembayaran_diverifikasi',
            'icon' => 'fa-check-circle',
            'action_url' => route('pasien.pembayaran.index'),
        ];
    }
}
