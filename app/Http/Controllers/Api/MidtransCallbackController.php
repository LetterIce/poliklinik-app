<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Notifications\PembayaranDiverifikasiNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    /**
     * Handle webhook notifikasi HTTP POST dari Midtrans
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Health check / Test ping via GET atau HEAD
        if ($request->isMethod('get') || $request->isMethod('head')) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Midtrans notification webhook endpoint is active and ready.'
            ], 200);
        }

        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'midtrans';

        // 2. Jika merupakan Test Ping dari Midtrans dashboard (tanpa order_id atau payload tes)
        if (empty($orderId) || in_array($orderId, ['test-notification', 'test', 'midtrans-test'])) {
            Log::info('Midtrans Webhook Test Ping Verified Successfully.');
            return response()->json([
                'status' => 'ok',
                'message' => 'Test notification received and validated successfully'
            ], 200);
        }

        // 3. Verifikasi Signature Key (SHA-512)
        $serverKey = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans Webhook Invalid Signature Key', [
                'order_id' => $orderId,
                'received' => $signatureKey,
                'expected' => $expectedSignature
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // 4. Jika order_id merupakan test notifikasi resmi dari Midtrans (payment_notif_test_...)
        if (str_starts_with($orderId, 'payment_notif_test_')) {
            Log::info("Midtrans Webhook Test Notification successfully processed: {$orderId}");
            return response()->json([
                'status' => 'success',
                'message' => 'Test notification received and validated successfully'
            ], 200);
        }

        // 5. Cari data Pembayaran berdasarkan order_id
        $pembayaran = Pembayaran::with(['pasien', 'periksa.daftarPoli.jadwalPeriksa.dokter'])->where('order_id', $orderId)->first();

        if (!$pembayaran) {
            Log::warning("Midtrans Webhook Pembayaran with Order ID {$orderId} not found.");
            return response()->json([
                'status' => 'ok',
                'message' => 'Order not found in database, ignored.'
            ], 200);
        }

        // 3. Update status pembayaran berdasarkan transaction_status
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

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook processed successfully'
        ]);
    }

    /**
     * Tandai pembayaran lunas dan picu notifikasi
     */
    protected function markAsLunas(Pembayaran $pembayaran, string $metode): void
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

        // Kirim notifikasi lunas ke pasien
        $pasien = $pembayaran->pasien;
        if ($pasien) {
            try {
                $pasien->notify(new PembayaranDiverifikasiNotification($pembayaran));
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi lunas ke pasien: ' . $e->getMessage());
            }
        }

        Log::info("Pembayaran Order ID {$pembayaran->order_id} berhasil diupdate menjadi LUNAS via Midtrans.");
    }
}
