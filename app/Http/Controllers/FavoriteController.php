<?php

namespace App\Http\Controllers;

use App\Models\Favori;
use App\Models\Urun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favoriler = auth()->user()->favoriler()
            ->with('urun.kategori')
            ->latest()
            ->paginate(12);

        return view('favorites.index', compact('favoriler'));
    }

    public function toggle(Request $request)
    {
        try {
            // Kullanıcı giriş yapmamışsa
            if (!auth()->check()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lütfen giriş yapın',
                    ], 401);
                }
                return redirect()->route('login')->with('hata', 'Lütfen giriş yapın');
            }

            $request->validate([
                'urun_id' => ['required', 'exists:urunler,id'],
            ]);

            $favori = Favori::where('kullanici_id', auth()->id())
                ->where('urun_id', $request->urun_id)
                ->first();

            if ($favori) {
                $favori->delete();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'favoride' => false,
                        'message' => 'Favorilerden çıkarıldı',
                    ]);
                }
                return back()->with('basarili', 'Favorilerden çıkarıldı');
            }

            Favori::create([
                'kullanici_id' => auth()->id(),
                'urun_id' => $request->urun_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'favoride' => true,
                    'message' => 'Favorilere eklendi',
                ]);
            }
            return back()->with('basarili', 'Favorilere eklendi');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('hata', 'Bir hata oluştu');
        }
    }

    public function remove(Urun $urun): RedirectResponse
    {
        Favori::where('kullanici_id', auth()->id())
            ->where('urun_id', $urun->id)
            ->delete();

        return back()->with('basarili', 'Ürün favorilerden çıkarıldı!');
    }
}
