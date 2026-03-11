<?php

namespace App\Http\Controllers;

use App\Models\SepetItem;
use App\Models\Teklif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Sepet sayfasi
     */
    public function index(): View
    {
        $sepetItems = SepetItem::where('kullanici_id', auth()->id())
            ->with(['urun.kategori', 'urun.satici', 'teklif', 'konusma'])
            ->latest()
            ->get();

        $toplamTutar = $sepetItems->sum('fiyat');

        return view('cart.index', compact('sepetItems', 'toplamTutar'));
    }

    /**
     * Kabul edilen teklifi sepete ekle
     */
    public function add(Teklif $teklif): RedirectResponse
    {
        // Teklif kabul edilmis mi kontrol et
        if ($teklif->durum !== 'kabul_edildi') {
            return back()->with('hata', 'Yalnizca kabul edilmis teklifler sepete eklenebilir.');
        }

        // Kullanici bu teklifin alicisi mi kontrol et
        $konusma = $teklif->konusma;
        if ($konusma->alici_id !== auth()->id()) {
            return back()->with('hata', 'Bu teklif size ait degil.');
        }

        // Urun satilmis mi kontrol et
        $urun = $konusma->urun;
        if ($urun->satildi) {
            return back()->with('hata', 'Bu urun zaten satilmis.');
        }

        // Zaten sepette mi kontrol et
        $mevcutItem = SepetItem::where('kullanici_id', auth()->id())
            ->where('teklif_id', $teklif->id)
            ->first();

        if ($mevcutItem) {
            return back()->with('uyari', 'Bu urun zaten sepetinizde.');
        }

        // Sepete ekle
        SepetItem::create([
            'kullanici_id' => auth()->id(),
            'urun_id' => $urun->id,
            'teklif_id' => $teklif->id,
            'konusma_id' => $konusma->id,
            'fiyat' => $teklif->tutar,
        ]);

        return redirect()->route('cart.index')
            ->with('basarili', 'Urun sepete eklendi.');
    }

    /**
     * Sepetten urun sil
     */
    public function remove(SepetItem $item): RedirectResponse
    {
        // Kullanicinin kendi sepet itemi mi kontrol et
        if ($item->kullanici_id !== auth()->id()) {
            return back()->with('hata', 'Bu islem icin yetkiniz yok.');
        }

        $item->delete();

        return back()->with('basarili', 'Urun sepetten cikarildi.');
    }

    /**
     * Sepeti temizle
     */
    public function clear(): RedirectResponse
    {
        SepetItem::where('kullanici_id', auth()->id())->delete();

        return back()->with('basarili', 'Sepetiniz temizlendi.');
    }

    /**
     * Sepet sayisini JSON olarak dondur (AJAX icin)
     */
    public function count(): \Illuminate\Http\JsonResponse
    {
        $sayi = SepetItem::where('kullanici_id', auth()->id())->count();

        return response()->json(['sayi' => $sayi]);
    }
}
