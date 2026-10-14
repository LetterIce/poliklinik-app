<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Notifications\PembayaranDiverifikasiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    /**
     * Tampilkan daftar seluruh pembayaran pasien untuk Admin
     */
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Pembayaran::with([
            'pasien',
            'periksa.daftarPoli.jadwalPeriksa.dokter.poli',
            'verifikator'
        ])->latest('tgl_bayar');

        if ($status && in_array($status, ['menunggu_verifikasi', 'lunas', 'ditolak'])) {
            $query->where('status', $status);
        }

        $countMenunggu = Pembayaran::where('status', 'menunggu_verifikasi')->count();
        $pembayarans = $query->paginate(15)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans', 'status', 'countMenunggu'));
    }

    /**
     * Verifikasi pembayaran menjadi LUNAS dan kirim notifikasi ke Pasien
     */
    public function verifikasi($id)
    {
        $pembayaran = Pembayaran::with(['pasien', 'periksa.daftarPoli.jadwalPeriksa.dokter.poli'])->findOrFail($id);

        // KETENTUAN: Satu kejadian hanya menghasilkan satu notifikasi, tidak terkirim ganda
        if ($pembayaran->status === 'lunas') {
            return redirect()->back()->with('message', 'Pembayaran ini sudah berstatus LUNAS sebelumnya.');
        }

        $pembayaran->update([
            'status' => 'lunas',
            'tgl_verifikasi' => now(),
            'verified_by' => Auth::id(),
        ]);

        // KETENTUAN: Pasien menerima notifikasi ketika pembayarannya diverifikasi (lunas) oleh admin
        $pasien = $pembayaran->pasien;
        if ($pasien) {
            $pasien->notify(new PembayaranDiverifikasiNotification($pembayaran));
        }

        return redirect()->back()->with('success', "Pembayaran pasien {$pasien->nama} berhasil diverifikasi Lunas! Notifikasi telah dikirim ke pasien.");
    }

    /**
     * Tolak pembayaran dengan catatan
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:255',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update([
            'status' => 'ditolak',
            'tgl_verifikasi' => now(),
            'verified_by' => Auth::id(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', 'Pembayaran telah ditandai ditolak.');
    }
}
