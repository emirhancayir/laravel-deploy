<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions  Gerekli yetki(ler)
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Giris yapilmamissa
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Yetkisiz erisim'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin degilse direkt reddet
        if (!$user->adminMi()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bu islemi yapmaya yetkiniz yok'], 403);
            }
            return redirect()->route('home')->with('hata', 'Bu islemi yapmaya yetkiniz yok.');
        }

        // Super admin her seyi yapabilir
        if ($user->superAdminMi()) {
            return $next($request);
        }

        // Verilen yetkilerden en az birine sahip mi?
        if (!$user->hasAnyPermission($permissions)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bu islemi yapmaya yetkiniz yok'], 403);
            }
            return redirect()->route('admin.dashboard')->with('hata', 'Bu islemi yapmaya yetkiniz yok.');
        }

        return $next($request);
    }
}
