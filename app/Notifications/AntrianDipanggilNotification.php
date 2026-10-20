<?php

namespace App\Notifications;

use App\Models\DaftarPoli;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AntrianDipanggilNotification extends Notification
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
        $jadwal = $this->daftarPoli->jadwalPeriksa;
        $dokter = optional($jadwal->dokter)->nama ?? 'Dokter';
        $poli = optional($jadwal->dokter->poli)->nama_poli ?? 'Poliklinik';
        $noAntrian = $this->daftarPoli->no_antrian;

        return [
            'id_daftar_poli' => $this->daftarPoli->id,
            'title' => 'Panggilan Masuk ke Ruang Periksa!',
            'message' => "Nomor antrean Anda (#{$noAntrian}) sedang dipanggil oleh dr. {$dokter} ke Ruang {$poli}. Silakan segera masuk ke ruangan dokter.",
            'tipe' => 'antrian_dipanggil',
            'icon' => 'fa-bullhorn',
            'action_url' => route('pasien.dashboard'),
        ];
    }
}
