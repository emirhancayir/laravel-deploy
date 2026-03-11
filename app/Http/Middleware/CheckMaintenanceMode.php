<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Bakım modu aktif mi kontrol et
        $result = DB::table('site_settings')->where('key', 'maintenance_mode')->first();

        // Ayar yoksa veya kapalıysa devam et
        if (!$result || $result->value !== '1') {
            return $next($request);
        }

        // Admin kullanıcıları geçebilir
        if (auth()->check() && in_array(auth()->user()->kullanici_tipi, ['admin', 'super_admin'])) {
            return $next($request);
        }

        // Bakım mesajını al
        $msgResult = DB::table('site_settings')->where('key', 'maintenance_message')->first();
        $message = $msgResult?->value ?? 'Site şu anda bakım modundadır.';

        return response()->view('maintenance', ['message' => $message], 503);
    }
}
