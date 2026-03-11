<?php

namespace App\Http\Middleware;

use App\Models\IpBan;
use App\Models\IpListesi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIpBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Yeni IP listesi kontrolu (blacklist/whitelist)
        $ipKontrol = IpListesi::ipKontrol($ip);
        if (!$ipKontrol['izinli']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'IP adresiniz engellenmiş',
                    'reason' => $ipKontrol['sebep'],
                ], 403);
            }

            return response()->view('errors.ip-banned', [
                'message' => 'IP adresiniz engellenmiş.' . ($ipKontrol['sebep'] ? ' Sebep: ' . $ipKontrol['sebep'] : ''),
                'ban' => null,
            ], 403);
        }

        // Eski IpBan kontrolu (geriye uyumluluk)
        if (IpBan::isBanned($ip)) {
            $ban = IpBan::where('ip_address', $ip)->first();

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'IP adresiniz engellenmiş',
                    'reason' => $ban?->reason,
                    'expires_at' => $ban?->expires_at?->toDateTimeString(),
                ], 403);
            }

            $message = 'IP adresiniz engellenmiş.';
            if ($ban?->reason) {
                $message .= ' Sebep: ' . $ban->reason;
            }
            if ($ban?->expires_at) {
                $message .= ' Süre: ' . $ban->expires_at->format('d.m.Y H:i');
            }

            return response()->view('errors.ip-banned', [
                'message' => $message,
                'ban' => $ban,
            ], 403);
        }

        return $next($request);
    }
}
