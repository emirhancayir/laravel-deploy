<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Urun;
use App\Models\Kategori;
use App\Models\AdminActivityLog;
use App\Notifications\UrunOnayBildirimi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProductManagementController extends Controller
{
    /**
     * Urun listesi
     */
    public function index(Request $request): View
    {
        $query = Urun::with(['satici', 'kategori', 'il']);

        // Durum filtresi
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }

        // Kategori filtresi
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Satici filtresi
        if ($request->filled('satici')) {
            $query->where('satici_id', $request->satici);
        }

        // Arama
        if ($request->filled('ara')) {
            $ara = $request->ara;
            $query->where(function ($q) use ($ara) {
                $q->where('urun_adi', 'like', "%$ara%")
                  ->orWhere('aciklama', 'like', "%$ara%")
                  ->orWhereHas('satici', function ($sq) use ($ara) {
                      $sq->where('ad', 'like', "%$ara%")
                         ->orWhere('soyad', 'like', "%$ara%")
                         ->orWhere('email', 'like', "%$ara%");
                  });
            });
        }

        // Sıralama
        $siralama = $request->get('siralama', 'yeni');
        switch ($siralama) {
            case 'eski':
                $query->oldest();
                break;
            case 'fiyat_artan':
                $query->orderBy('fiyat', 'asc');
                break;
            case 'fiyat_azalan':
                $query->orderBy('fiyat', 'desc');
                break;
            case 'goruntulenme':
                $query->orderBy('goruntulenme_sayisi', 'desc');
                break;
            case 'yeni':
            default:
                $query->latest();
                break;
        }

        $urunler = $query->paginate(20);
        $kategoriler = Kategori::orderBy('kategori_adi')->get();

        // İstatistikler
        $istatistikler = [
            'toplam' => Urun::count(),
            'aktif' => Urun::where('durum', 'aktif')->count(),
            'beklemede' => Urun::where('durum', 'beklemede')->count(),
            'pasif' => Urun::where('durum', 'pasif')->count(),
            'satildi' => Urun::where('satildi', true)->count(),
        ];

        return view('admin.products.index', compact('urunler', 'kategoriler', 'istatistikler'));
    }

    /**
     * Urun detayi
     */
    public function show(Urun $urun): View
    {
        $urun->load(['satici', 'kategori', 'resimler', 'il', 'ilce', 'mahalle', 'attributeValues.attribute', 'stokHareketleri']);

        return view('admin.products.show', compact('urun'));
    }

    /**
     * Urun duzenleme formu
     */
    public function edit(Urun $urun): View
    {
        $urun->load(['resimler', 'attributeValues']);
        $kategoriler = Kategori::orderBy('kategori_adi')->get();

        return view('admin.products.edit', compact('urun', 'kategoriler'));
    }

    /**
     * Urun guncelleme
     */
    public function update(Request $request, Urun $urun): RedirectResponse
    {
        $validated = $request->validate([
            'urun_adi' => 'required|string|max:255',
            'aciklama' => 'required|string',
            'fiyat' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'durum' => 'required|in:aktif,pasif,beklemede,reddedildi',
            'kategori_id' => 'nullable|exists:kategoriler,id',
        ]);

        $eskiDeger = $urun->toArray();
        $urun->update($validated);

        AdminActivityLog::log('urun.updated', $urun, $eskiDeger, $validated,
            "Ürün güncellendi: {$urun->urun_adi}");

        return redirect()->route('admin.products.show', $urun)
            ->with('basarili', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Onay bekleyen urunler
     */
    public function pending(Request $request): View
    {
        $query = Urun::with(['satici', 'kategori'])
            ->where('onay_durumu', 'beklemede');

        // Arama
        if ($request->filled('ara')) {
            $ara = $request->ara;
            $query->where(function ($q) use ($ara) {
                $q->where('urun_adi', 'like', "%$ara%")
                  ->orWhereHas('satici', function ($sq) use ($ara) {
                      $sq->where('ad', 'like', "%$ara%")
                         ->orWhere('soyad', 'like', "%$ara%");
                  });
            });
        }

        $urunler = $query->latest()->paginate(20);
        $bekleyenSayisi = Urun::where('onay_durumu', 'beklemede')->count();

        return view('admin.products.pending', compact('urunler', 'bekleyenSayisi'));
    }

    /**
     * Urunu onayla
     */
    public function approve(Urun $urun): RedirectResponse
    {
        $urun->update([
            'durum' => 'aktif',
            'onay_durumu' => 'onaylandi',
            'onaylandi_tarih' => now(),
        ]);

        // Satici'ya bildirim gonder
        if ($urun->satici) {
            $urun->satici->notify(new UrunOnayBildirimi($urun, 'onaylandi'));
        }

        AdminActivityLog::log('urun.approved', $urun, null, null,
            "Ürün onaylandı: {$urun->urun_adi}");

        return back()->with('basarili', 'Ürün onaylandı ve yayına alındı.');
    }

    /**
     * Urunu reddet
     */
    public function reject(Request $request, Urun $urun): RedirectResponse
    {
        $request->validate([
            'red_nedeni' => 'required|string|max:500',
        ]);

        $urun->update([
            'durum' => 'pasif',
            'onay_durumu' => 'reddedildi',
            'red_nedeni' => $request->red_nedeni,
        ]);

        // Satici'ya bildirim gonder
        if ($urun->satici) {
            $urun->satici->notify(new UrunOnayBildirimi($urun, 'reddedildi', $request->red_nedeni));
        }

        AdminActivityLog::log('urun.rejected', $urun, null, ['sebep' => $request->red_nedeni],
            "Ürün reddedildi: {$urun->urun_adi}");

        return back()->with('basarili', 'Ürün reddedildi.');
    }

    /**
     * Urunu pasife al
     */
    public function deactivate(Urun $urun): RedirectResponse
    {
        $urun->update(['durum' => 'pasif']);

        AdminActivityLog::log('urun.deactivated', $urun, null, null,
            "Ürün pasife alındı: {$urun->urun_adi}");

        return back()->with('basarili', 'Ürün pasife alındı.');
    }

    /**
     * Urunu sil
     */
    public function destroy(Urun $urun): RedirectResponse
    {
        $urunAdi = $urun->urun_adi;

        // Tamamlanmis odeme varsa silme, pasif yap
        $tamamlanmisOdeme = $urun->odemeler()->where('durum', 'odendi')->exists();
        if ($tamamlanmisOdeme) {
            $urun->update(['durum' => 'pasif', 'satildi' => true]);
            return redirect()->route('admin.products.index')
                ->with('basarili', 'Ürün satış geçmişi olduğu için silmek yerine pasif yapıldı.');
        }

        try {
            // Foreign key kontrolünü kapat
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $urunId = $urun->id;

            // Iliskili kayitlari sil
            foreach ($urun->konusmalar as $konusma) {
                $konusma->odemeler()->delete();
                $konusma->teklifler()->delete();
                $konusma->mesajlar()->delete();
                $konusma->delete();
            }

            $urun->favoriler()->delete();
            $urun->yorumlar()->delete();

            // Sipariş detaylarını sil
            \DB::table('siparis_detaylari')->where('urun_id', $urunId)->delete();
            \DB::table('sepet')->where('urun_id', $urunId)->delete();
            \DB::table('sepet_items')->where('urun_id', $urunId)->delete();

            // Resimleri sil
            foreach ($urun->resimler as $resim) {
                Storage::delete('public/urunler/' . $resim->resim);
                $resim->delete();
            }

            if ($urun->resim) {
                Storage::delete('public/urunler/' . $urun->resim);
            }

            $urun->stokHareketleri()->delete();
            $urun->attributeValues()->delete();

            AdminActivityLog::log('urun.deleted', $urun, $urun->toArray(), null,
                "Ürün silindi: {$urunAdi}");

            \DB::table('urunler')->where('id', $urunId)->delete();

            // Foreign key kontrolünü aç
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('admin.products.index')
                ->with('basarili', 'Ürün başarıyla silindi.');
        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->route('admin.products.index')
                ->with('hata', 'Ürün silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toplu islem
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        // GET isteklerini geri yonlendir
        if ($request->isMethod('get')) {
            return redirect()->route('admin.products.index')
                ->with('hata', 'Toplu islem icin urun secmelisiniz.');
        }

        // DELETE istekleri icin islem=sil olarak ayarla
        if ($request->isMethod('delete') && !$request->has('islem')) {
            $request->merge(['islem' => 'sil']);
        }

        $request->validate([
            'islem' => 'required|in:onayla,reddet,pasif,sil',
            'urunler' => 'required|array|min:1',
            'urunler.*' => 'exists:urunler,id',
        ]);

        $urunler = Urun::whereIn('id', $request->urunler)->get();
        $sayi = $urunler->count();

        foreach ($urunler as $urun) {
            switch ($request->islem) {
                case 'onayla':
                    $urun->update(['durum' => 'aktif']);
                    break;
                case 'reddet':
                    $urun->update(['durum' => 'reddedildi']);
                    break;
                case 'pasif':
                    $urun->update(['durum' => 'pasif']);
                    break;
                case 'sil':
                    // Tamamlanmis odeme varsa silme, pasif yap
                    $tamamlanmisOdeme = $urun->odemeler()->where('durum', 'odendi')->exists();
                    if ($tamamlanmisOdeme) {
                        $urun->update(['durum' => 'pasif', 'satildi' => true]);
                        continue 2; // Bir sonraki urune gec
                    }

                    // Foreign key kontrolünü kapat
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');

                    $urunId = $urun->id;

                    // Iliskili kayitlari sil
                    foreach ($urun->konusmalar as $konusma) {
                        $konusma->odemeler()->delete();
                        $konusma->teklifler()->delete();
                        $konusma->mesajlar()->delete();
                        $konusma->delete();
                    }

                    $urun->favoriler()->delete();
                    $urun->yorumlar()->delete();

                    // Sipariş detaylarını ve sepeti sil
                    \DB::table('siparis_detaylari')->where('urun_id', $urunId)->delete();
                    \DB::table('sepet')->where('urun_id', $urunId)->delete();
                    \DB::table('sepet_items')->where('urun_id', $urunId)->delete();

                    foreach ($urun->resimler as $resim) {
                        Storage::delete('public/urunler/' . $resim->resim);
                        $resim->delete();
                    }

                    if ($urun->resim) {
                        Storage::delete('public/urunler/' . $urun->resim);
                    }

                    $urun->stokHareketleri()->delete();
                    $urun->attributeValues()->delete();

                    \DB::table('urunler')->where('id', $urunId)->delete();

                    // Foreign key kontrolünü aç
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    break;
            }
        }

        $islemAdi = match ($request->islem) {
            'onayla' => 'onaylandı',
            'reddet' => 'reddedildi',
            'pasif' => 'pasife alındı',
            'sil' => 'silindi',
        };

        AdminActivityLog::log('urun.bulk_action', null, null, [
            'islem' => $request->islem,
            'urun_sayisi' => $sayi,
        ], "{$sayi} ürün toplu olarak {$islemAdi}");

        return back()->with('basarili', "{$sayi} ürün başarıyla {$islemAdi}.");
    }
}
