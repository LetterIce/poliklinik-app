<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Periksa;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->initMidtrans();
    }

    /**
     * Inisialisasi konfigurasi Midtrans
     */
    protected function initMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    /**
     * Dapatkan atau buat Snap Token untuk suatu tagihan Periksa
     */
    public function getSnapToken(Periksa $periksa, Pembayaran $pembayaran): string
    {
        // Jika pembayaran sudah punya snap_token aktif dan status masih menunggu_verifikasi, gunakan token itu
        if ($pembayaran->snap_token && $pembayaran->status === 'menunggu_verifikasi') {
            return $pembayaran->snap_token;
        }

        $orderId = 'INV-' . $periksa->id . '-' . time();

        $pasien = $periksa->daftarPoli->pasien ?? auth()->user();
        $dokter = optional($periksa->daftarPoli->jadwalPeriksa)->dokter;
        $poli = optional($dokter)->poli;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $periksa->biaya_periksa,
            ],
            'customer_details' => [
                'first_name' => $pasien->nama ?? 'Pasien',
                'email' => $pasien->email ?? 'pasien@poliklinik.test',
                'phone' => $pasien->no_hp ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'PERIKSA-' . $periksa->id,
                    'price' => (int) $periksa->biaya_periksa,
                    'quantity' => 1,
                    'name' => 'Biaya Periksa: ' . ($poli->nama_poli ?? 'Poliklinik'),
                ],
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Simpan order_id dan snap_token ke database
            $pembayaran->update([
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'metode_pembayaran' => 'Midtrans (Online)',
            ]);

            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Gagal membuat transaksi Midtrans: ' . $e->getMessage());
        }
    }
}
