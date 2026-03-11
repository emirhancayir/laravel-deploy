<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Slider;
use App\Models\Urun;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function index(): View|Response
    {
        // Bakım modu kontrolü
        $maintenance = DB::table('site_settings')->where('key', 'maintenance_mode')->first();
        if ($maintenance && $maintenance->value === '1') {
            // Admin değilse bakım sayfası göster
            if (!auth()->check() || !in_array(auth()->user()->kullanici_tipi, ['admin', 'super_admin'])) {
                $msg = DB::table('site_settings')->where('key', 'maintenance_message')->first();
                return response()->view('maintenance', [
                    'message' => $msg?->value ?? 'Site bakım modundadır.'
                ], 503);
            }
        }
        $kategoriler = Kategori::where('aktif', true)->withCount('urunler')->get();

        // Ozel sliderlar
        $sliders = Slider::aktif()->sirali()->get();

        // En cok goruntulenenen urunler (populer)
        $populerUrunler = Urun::with('kategori')
            ->aktif()
            ->stokta()
            ->satilmamis()
            ->orderByDesc('goruntulenme_sayisi')
            ->limit(config('zamason.slider.populer_urun_sayisi', 8))
            ->get();

        // Yeni eklenen urunler
        $yeniUrunler = Urun::with('kategori')
            ->aktif()
            ->stokta()
            ->satilmamis()
            ->latest()
            ->limit(config('zamason.slider.yeni_urun_sayisi', 8))
            ->get();

        // Indirimli urunler (eski_fiyat > fiyat)
        $indirimliUrunler = Urun::with('kategori')
            ->aktif()
            ->stokta()
            ->satilmamis()
            ->whereNotNull('eski_fiyat')
            ->whereColumn('eski_fiyat', '>', 'fiyat')
            ->limit(config('zamason.slider.indirimli_urun_sayisi', 8))
            ->get();

        return view('home', compact(
            'kategoriler',
            'sliders',
            'populerUrunler',
            'yeniUrunler',
            'indirimliUrunler'
        ));
    }
}
