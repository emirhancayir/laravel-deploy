<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Bildirimleri listele (Sayfa)
     */
    public function index(): View
    {
        $bildirimler = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('bildirimler'));
    }

    /**
     * Bildirimleri getir (AJAX)
     */
    public function liste(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $bildirimler = auth()->user()
            ->notifications()
            ->limit($limit)
            ->get()
            ->map(function ($bildirim) {
                return [
                    'id' => $bildirim->id,
                    'baslik' => $bildirim->data['baslik'] ?? '',
                    'mesaj' => $bildirim->data['mesaj'] ?? '',
                    'ikon' => $bildirim->data['ikon'] ?? 'bell',
                    'link' => $bildirim->data['link'] ?? '#',
                    'okundu' => !is_null($bildirim->read_at),
                    'tarih' => $bildirim->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'bildirimler' => $bildirimler,
            'okunmamis_sayisi' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Okunmamis bildirim sayisi (AJAX)
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $sayisi = $user->unreadNotifications()->count();

        // Son bildirimi de döndür (toast için)
        $sonBildirim = null;
        $latestNotif = $user->unreadNotifications()->latest()->first();
        if ($latestNotif) {
            $sonBildirim = [
                'id' => $latestNotif->id,
                'title' => $latestNotif->data['baslik'] ?? $latestNotif->data['title'] ?? 'Yeni Bildirim',
                'message' => $latestNotif->data['mesaj'] ?? $latestNotif->data['message'] ?? '',
                'link' => $latestNotif->data['link'] ?? route('notifications.index'),
            ];
        }

        return response()->json([
            'success' => true,
            'sayi' => $sayisi,
            'sonBildirim' => $sonBildirim,
        ]);
    }

    /**
     * Bildirimi okundu olarak isaretle (AJAX)
     */
    public function markAsRead(string $id): JsonResponse
    {
        $bildirim = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($bildirim) {
            $bildirim->markAsRead();
        }

        return response()->json([
            'success' => true,
            'okunmamis_sayisi' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Tumunu okundu olarak isaretle (AJAX)
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Tüm bildirimler okundu olarak işaretlendi.',
        ]);
    }

    /**
     * Bildirimi sil (AJAX)
     */
    public function destroy(string $id): JsonResponse
    {
        $bildirim = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($bildirim) {
            $bildirim->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Bildirim silindi.',
        ]);
    }
}
