<?php

namespace App\Notifications;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranDiunggahNotification extends Notification
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
        $pasienNama = optional($this->pembayaran->pasien)->nama ?? 'Pasien';
        $periksa = $this->pembayaran->periksa;
        $poli = optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? 'Poliklinik';
        $jumlah = number_format($this->pembayaran->jumlah_bayar, 0, ',', '.');

        return [
            'id_pembayaran' => $this->pembayaran->id,
            'id_periksa' => $this->pembayaran->id_periksa,
            'title' => 'Pembayaran Pasien Baru',
            'message' => "Pasien {$pasienNama} telah mengunggah bukti pembayaran sebesar Rp {$jumlah} untuk pemeriksaan di {$poli}.",
            'tipe' => 'pembayaran_diunggah',
            'icon' => 'fa-file-invoice-dollar',
            'action_url' => route('riwayat-pasien.show', $this->pembayaran->id_periksa),
        ];
    }
}
