<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Giris yapilmamissa
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Yetkisiz erisim'], 401);
            }
            return redirect()->route('login')->with('hata', 'Bu alana erisim icin giris yapmaniz gerekiyor.');
        }

        $user = auth()->user();

        // Banlı kullanici kontrolu
        if ($user->banliMi()) {
            auth()->logout();
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Hesabiniz askiya alinmis'], 403);
            }
            return redirect()->route('login')->with('hata', 'Hesabiniz askiya alinmis. Sebep: ' . ($user->ban_reason ?? 'Belirtilmemis'));
        }

        // Admin kontrolu
        if (!$user->adminMi()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bu alana erisim yetkiniz yok'], 403);
            }
            return redirect()->route('home')->with('hata', 'Bu alana erisim yetkiniz yok.');
        }

        return $next($request);
    }
}
