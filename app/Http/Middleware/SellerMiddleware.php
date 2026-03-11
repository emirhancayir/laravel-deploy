<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->saticiMi()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Yetkisiz erişim'], 403);
            }

            return redirect()->route('home')
                ->with('hata', 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
