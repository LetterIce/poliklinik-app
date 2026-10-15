<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Periksa;
use App\Notifications\PembayaranDiunggahNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Tampilkan riwayat pemeriksaan pasien yang perlu atau sudah dibayar
     */
    public function index()
    {
        $pasienId = Auth::id();

        // Ambil semua pemeriksaan yang dimiliki pasien ini
        $periksas = Periksa::whereHas('daftarPoli', function ($q) use ($pasienId) {
            $q->where('id_pasien', $pasienId);
        })->with([
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat',
            'pembayaran'
        ])->latest('tgl_periksa')->get();

        return view('pasien.pembayaran.index', compact('periksas'));
    }

    /**
     * Form upload bukti pembayaran untuk suatu pemeriksaan
     */
    public function create($idPeriksa)
    {
        $pasienId = Auth::id();

        $periksa = Periksa::whereHas('daftarPoli', function ($q) use ($pasienId) {
            $q->where('id_pasien', $pasienId);
        })->with([
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat',
            'pembayaran'
        ])->findOrFail($idPeriksa);

        return view('pasien.pembayaran.create', compact('periksa'));
    }

    /**
     * Simpan bukti pembayaran dan kirim notifikasi ke dokter pemeriksa
     */
    public function store(Request $request, $idPeriksa)
    {
        $pasienId = Auth::id();

        $periksa = Periksa::whereHas('daftarPoli', function ($q) use ($pasienId) {
            $q->where('id_pasien', $pasienId);
        })->with('daftarPoli.jadwalPeriksa.dokter')->findOrFail($idPeriksa);

        $request->validate([
            'metode_pembayaran' => 'required|string|max:50',
            'bukti_bayar' => 'required|file|mimes:jpeg,png,jpg,pdf|max:3072',
        ], [
            'bukti_bayar.required' => 'Wajib mengunggah file bukti transfer / pembayaran.',
            'bukti_bayar.mimes' => 'Format file bukti bayar harus berupa JPG, PNG, atau PDF.',
            'bukti_bayar.max' => 'Ukuran file bukti bayar maksimal 3MB.',
        ]);

        // Simpan file bukti bayar
        $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

        // Simpan atau update record pembayaran
        $pembayaran = Pembayaran::updateOrCreate(
            ['id_periksa' => $periksa->id],
            [
                'id_pasien' => $pasienId,
                'jumlah_bayar' => $periksa->biaya_periksa,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_bayar' => $path,
                'status' => 'menunggu_verifikasi',
                'tgl_bayar' => now(),
            ]
        );

        // KETENTUAN: Dokter menerima notifikasi ketika pasien yang ia periksa membuat pembayaran
        $dokter = optional($periksa->daftarPoli->jadwalPeriksa)->dokter;
        if ($dokter) {
            $dokter->notify(new PembayaranDiunggahNotification($pembayaran));
        }

        return redirect()->route('pasien.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil diunggah! Notifikasi telah dikirim ke dokter dan pembayaran menunggu verifikasi admin.');
    }

    /**
     * Dapatkan Snap Token Midtrans untuk pembayaran online otomatis
     */
    public function getSnapToken($idPeriksa, \App\Services\MidtransService $midtransService)
    {
        $pasienId = Auth::id();

        $periksa = Periksa::whereHas('daftarPoli', function ($q) use ($pasienId) {
            $q->where('id_pasien', $pasienId);
        })->with(['daftarPoli.jadwalPeriksa.dokter.poli', 'pembayaran'])->findOrFail($idPeriksa);

        $pembayaran = Pembayaran::firstOrCreate(
            ['id_periksa' => $periksa->id],
            [
                'id_pasien' => $pasienId,
                'jumlah_bayar' => $periksa->biaya_periksa,
                'metode_pembayaran' => 'Midtrans (Online)',
                'status' => 'menunggu_verifikasi',
                'tgl_bayar' => now(),
            ]
        );

        if ($pembayaran->status === 'lunas') {
            return response()->json([
                'status' => 'already_paid',
                'message' => 'Tagihan pemeriksaan ini sudah berstatus LUNAS.'
            ], 400);
        }

        try {
            $snapToken = $midtransService->getSnapToken($periksa, $pembayaran);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'order_id' => $pembayaran->order_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

