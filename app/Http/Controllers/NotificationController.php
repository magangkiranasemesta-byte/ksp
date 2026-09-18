<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Menampilkan semua notification user.
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    /**
     * Tandai satu notification sebagai sudah dibaca.
     */
    public function read(
        Request $request,
        string $id
    ): RedirectResponse {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        $url = data_get(
            $notification->data,
            'url'
        );

        if ($url) {
            return redirect()->to($url);
        }

        return back();
    }

    /**
     * Tandai semua notification sebagai sudah dibaca.
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with(
            'success',
            'Semua notifikasi telah ditandai sebagai sudah dibaca.'
        );
    }
}