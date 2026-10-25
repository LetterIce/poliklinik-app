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

    /**
     * Cek status pembayaran langsung ke API Midtrans dan update status menjadi Lunas secara instan
     */
    public function checkAndUpdateStatus(Pembayaran $pembayaran): ?string
    {
        if (!$pembayaran->order_id || $pembayaran->status === 'lunas') {
            return $pembayaran->status;
        }

        try {
            $status = \Midtrans\Transaction::status($pembayaran->order_id);
            $transactionStatus = is_object($status) ? ($status->transaction_status ?? null) : ($status['transaction_status'] ?? null);
            $fraudStatus = is_object($status) ? ($status->fraud_status ?? null) : ($status['fraud_status'] ?? null);
            $paymentType = is_object($status) ? ($status->payment_type ?? 'midtrans') : ($status['payment_type'] ?? 'midtrans');
            $metodeText = 'Midtrans (' . strtoupper(str_replace('_', ' ', $paymentType)) . ')';

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $this->markAsLunas($pembayaran, $metodeText);
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->markAsLunas($pembayaran, $metodeText);
            } elseif ($transactionStatus === 'pending') {
                $pembayaran->update([
                    'status' => 'menunggu_verifikasi',
                    'metode_pembayaran' => $metodeText,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $pembayaran->update([
                    'status' => 'ditolak',
                    'catatan_admin' => 'Transaksi Midtrans: ' . strtoupper($transactionStatus),
                ]);
            }

            return $pembayaran->fresh()->status;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::info("Cek status Midtrans order {$pembayaran->order_id}: " . $e->getMessage());
            return $pembayaran->status;
        }
    }

    /**
     * Tandai pembayaran sebagai lunas dan kirim notifikasi ke pasien
     */
    public function markAsLunas(Pembayaran $pembayaran, string $metode = 'Midtrans (Online)'): void
    {
        if ($pembayaran->status === 'lunas') {
            return;
        }

        $pembayaran->update([
            'status' => 'lunas',
            'metode_pembayaran' => $metode,
            'tgl_verifikasi' => now(),
            'tgl_bayar' => now(),
        ]);

        $pasien = $pembayaran->pasien;
        if ($pasien) {
            try {
                $pasien->notify(new \App\Notifications\PembayaranDiverifikasiNotification($pembayaran));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi lunas ke pasien: ' . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\Log::info("Pembayaran Order ID {$pembayaran->order_id} berhasil diupdate menjadi LUNAS via Midtrans.");
    }
}
