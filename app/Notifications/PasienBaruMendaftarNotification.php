<?php

namespace App\Notifications;

use App\Models\DaftarPoli;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasienBaruMendaftarNotification extends Notification
{
    use Queueable;

    public $daftarPoli;

    /**
     * Create a new notification instance.
     */
    public function __construct(DaftarPoli $daftarPoli)
    {
        $this->daftarPoli = $daftarPoli;
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
        $pasienNama = optional($this->daftarPoli->pasien)->nama ?? 'Pasien Baru';
        $noAntrian = $this->daftarPoli->no_antrian;
        $keluhan = $this->daftarPoli->keluhan ? " (Keluhan: {$this->daftarPoli->keluhan})" : '';

        return [
            'id_daftar_poli' => $this->daftarPoli->id,
            'title' => 'Pasien Baru Mendaftar!',
            'message' => "Pasien {$pasienNama} mendaftar antrean #{$noAntrian}{$keluhan}.",
            'tipe' => 'pasien_mendaftar',
            'icon' => 'fa-user-plus',
            'action_url' => route('periksa-pasien.index'),
        ];
    }
}
