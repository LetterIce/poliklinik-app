<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman daftar seluruh notifikasi pengguna yang login
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifikasi.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     * Ketentuan: Notifikasi hanya bisa ditandai oleh pemiliknya
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Jika ada action_url di data notifikasi dan request bukan AJAX, bisa redirect ke sana
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Notifikasi ditandai sudah dibaca']);
        }

        $url = $notification->data['action_url'] ?? null;
        if ($url && request()->has('redirect')) {
            return redirect($url);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi milik pengguna yang login sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Semua notifikasi ditandai sudah dibaca']);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sebagai sudah dibaca.');
    }
}
