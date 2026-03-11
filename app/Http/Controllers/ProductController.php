<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrunStoreRequest;
use App\Http\Requests\UrunUpdateRequest;
use App\Models\Il;
use App\Models\Kategori;
use App\Models\KategoriAttribute;
use App\Models\StokHareketi;
use App\Models\Urun;
use App\Models\UrunAttributeValue;
use App\Models\UrunResim;
use App\Models\YasakliKelime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Urun::with(['kategori', 'satici', 'il'])->aktif()->stokta()->satilmamis();

        // Kategori filtresi
        if ($request->filled('category')) {
            $kat = \App\Models\Kategori::where('slug', $request->category)->first();
            if ($kat) {
                $query->where('kategori_id', $kat->id);
            }
        }

        // Arama (başlık ve açıklamada)
        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('urun_adi', 'like', '%' . $arama . '%')
                    ->orWhere('aciklama', 'like', '%' . $arama . '%');
            });
        }

        // Fiyat aralığı filtresi
        if ($request->filled('min_fiyat')) {
            $query->where('fiyat', '>=', $request->min_fiyat);
        }
        if ($request->filled('max_fiyat')) {
            $query->where('fiyat', '<=', $request->max_fiyat);
        }

        // İl filtresi
        if ($request->filled('il')) {
            $query->where('il_id', $request->il);
        }

        // Sıralama
        $siralama = $request->get('siralama', 'yeni');
        switch ($siralama) {
            case 'fiyat_artan':
                $query->orderBy('fiyat', 'asc');
                break;
            case 'fiyat_azalan':
                $query->orderBy('fiyat', 'desc');
                break;
            case 'populer':
                $query->orderBy('goruntulenme_sayisi', 'desc');
                break;
            case 'yeni':
            default:
                $query->latest();
                break;
        }

        $urunler = $query->paginate(12);
        $kategoriler = Kategori::where('aktif', true)->get();
        $iller = Il::orderBy('il_adi')->get();

        // Fiyat aralığını bul (filtreler için)
        $fiyatAraligi = Urun::aktif()->stokta()->satilmamis()
            ->selectRaw('MIN(fiyat) as min_fiyat, MAX(fiyat) as max_fiyat')
            ->first();

        return view('products.index', compact('urunler', 'kategoriler', 'iller', 'fiyatAraligi'));
    }

    public function show(Urun $urun): View
    {
        $urun->load(['kategori', 'satici', 'resimler', 'il', 'ilce', 'mahalle', 'attributeValues.attribute']);

        // Goruntulenme sayisini artir
        $urun->increment('goruntulenme_sayisi');

        $benzerUrunler = Urun::where('kategori_id', $urun->kategori_id)
            ->where('id', '!=', $urun->id)
            ->aktif()
            ->stokta()
            ->satilmamis()
            ->limit(4)
            ->get();

        return view('products.show', compact('urun', 'benzerUrunler'));
    }

    public function create(): View
    {
        $kategoriler = Kategori::where('aktif', true)->with('attributes')->get();
        $iller = Il::orderBy('il_adi')->get();
        return view('products.create', compact('kategoriler', 'iller'));
    }

    public function store(UrunStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Yasakli kelime kontrolu - Urun adi
        $urunAdiKontrol = YasakliKelime::metinGecerliMi($validated['urun_adi'], 'urun_adi');
        if (!$urunAdiKontrol['gecerli']) {
            return back()->withInput()->withErrors(['urun_adi' => $urunAdiKontrol['mesaj']]);
        }

        // Yasakli kelime kontrolu - Aciklama
        $aciklamaKontrol = YasakliKelime::metinGecerliMi($validated['aciklama'], 'urun_aciklama');
        if (!$aciklamaKontrol['gecerli']) {
            return back()->withInput()->withErrors(['aciklama' => $aciklamaKontrol['mesaj']]);
        }

        $yuklenenResimler = [];

        // Resim yükleme işlemi - güvenli kontroller ile
        try {
            if ($request->hasFile('resimler')) {
                $urunlerDir = storage_path('app/public/urunler');
                if (!is_dir($urunlerDir)) {
                    mkdir($urunlerDir, 0755, true);
                }

                $izinliMimeTipleri = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxBoyut = 10 * 1024 * 1024; // 10MB

                foreach ($request->file('resimler') as $resim) {
                    // Tüm kontrolleri try-catch içinde yap
                    try {
                        if (!$resim || !$resim->isValid()) {
                            continue;
                        }

                        $tempPath = $resim->getPathname();
                        if (!file_exists($tempPath)) {
                            continue;
                        }

                        // Boyut kontrolü
                        if ($resim->getSize() > $maxBoyut) {
                            continue;
                        }

                        // MIME tipi kontrolü
                        $mimeType = $resim->getMimeType();
                        if (!in_array($mimeType, $izinliMimeTipleri)) {
                            continue;
                        }

                        $resimAdi = uniqid('urun_') . '.' . $resim->getClientOriginalExtension();
                        $resim->move($urunlerDir, $resimAdi);
                        $yuklenenResimler[] = $resimAdi;
                    } catch (\Exception $e) {
                        // Tek bir resim hatası tüm işlemi durdurmasın
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Resim yükleme hatası - devam et (resim olmadan)
        }

        $urun = Urun::create([
            'satici_id' => auth()->id(),
            'kategori_id' => $validated['kategori_id'] ?: null,
            'urun_adi' => $validated['urun_adi'],
            'aciklama' => $validated['aciklama'],
            'fiyat' => $validated['fiyat'],
            'stok' => $validated['stok'],
            'resim' => $yuklenenResimler[0] ?? null,
            'durum' => $validated['durum'],
            'il_id' => $validated['il_id'],
            'ilce_id' => $validated['ilce_id'],
            'mahalle_id' => $validated['mahalle_id'] ?? null,
            'adres_detay' => $validated['adres_detay'] ?? null,
        ]);

        foreach ($yuklenenResimler as $sira => $resim) {
            UrunResim::create([
                'urun_id' => $urun->id,
                'resim' => $resim,
                'sira' => $sira,
            ]);
        }

        // Stok hareketi kaydet
        if ($validated['stok'] > 0) {
            StokHareketi::kaydet(
                $urun->id,
                'giris',
                $validated['stok'],
                0,
                'Urun ilk kayit'
            );
        }

        // Kategori attributelarini kaydet
        if ($request->has('attributes') && is_array($request->attributes)) {
            foreach ($request->attributes as $attributeId => $deger) {
                if (!empty($deger)) {
                    UrunAttributeValue::create([
                        'urun_id' => $urun->id,
                        'attribute_id' => $attributeId,
                        'deger' => is_array($deger) ? implode(',', $deger) : $deger,
                    ]);
                }
            }
        }

        return redirect()->route('seller.dashboard')
            ->with('basarili', 'Urun basariyla eklendi!');
    }

    public function edit(Urun $urun): View
    {
        if ($urun->satici_id !== auth()->id()) {
            abort(403, 'Bu urunu duzenleme yetkiniz yok.');
        }

        $urun->load(['resimler', 'il', 'ilce', 'mahalle', 'attributeValues']);
        $kategoriler = Kategori::where('aktif', true)->with('attributes')->get();
        $iller = Il::orderBy('il_adi')->get();

        return view('products.edit', compact('urun', 'kategoriler', 'iller'));
    }

    public function update(UrunUpdateRequest $request, Urun $urun): RedirectResponse
    {
        $validated = $request->validated();

        // Yasakli kelime kontrolu - Urun adi
        $urunAdiKontrol = YasakliKelime::metinGecerliMi($validated['urun_adi'], 'urun_adi');
        if (!$urunAdiKontrol['gecerli']) {
            return back()->withInput()->withErrors(['urun_adi' => $urunAdiKontrol['mesaj']]);
        }

        // Yasakli kelime kontrolu - Aciklama
        $aciklamaKontrol = YasakliKelime::metinGecerliMi($validated['aciklama'], 'urun_aciklama');
        if (!$aciklamaKontrol['gecerli']) {
            return back()->withInput()->withErrors(['aciklama' => $aciklamaKontrol['mesaj']]);
        }

        $yuklenenResimler = [];

        // Resim yükleme işlemi - güvenli kontroller ile
        try {
            if ($request->hasFile('resimler')) {
                $urunlerDir = storage_path('app/public/urunler');
                if (!is_dir($urunlerDir)) {
                    mkdir($urunlerDir, 0755, true);
                }

                $izinliMimeTipleri = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxBoyut = 10 * 1024 * 1024; // 10MB

                foreach ($request->file('resimler') as $resim) {
                    try {
                        if (!$resim || !$resim->isValid()) {
                            continue;
                        }

                        $tempPath = $resim->getPathname();
                        if (!file_exists($tempPath)) {
                            continue;
                        }

                        if ($resim->getSize() > $maxBoyut) {
                            continue;
                        }

                        $mimeType = $resim->getMimeType();
                        if (!in_array($mimeType, $izinliMimeTipleri)) {
                            continue;
                        }

                        $resimAdi = uniqid('urun_') . '.' . $resim->getClientOriginalExtension();
                        $resim->move($urunlerDir, $resimAdi);
                        $yuklenenResimler[] = $resimAdi;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Resim yükleme hatası - devam et
        }

        $urun->update([
            'kategori_id' => $validated['kategori_id'] ?: null,
            'urun_adi' => $validated['urun_adi'],
            'aciklama' => $validated['aciklama'],
            'fiyat' => $validated['fiyat'],
            'stok' => $validated['stok'],
            'durum' => $validated['durum'],
            'resim' => $yuklenenResimler[0] ?? $urun->resim,
            'il_id' => $validated['il_id'],
            'ilce_id' => $validated['ilce_id'],
            'mahalle_id' => $validated['mahalle_id'] ?? null,
            'adres_detay' => $validated['adres_detay'] ?? null,
        ]);

        if (!empty($yuklenenResimler)) {
            $maxSira = $urun->resimler()->max('sira') ?? -1;
            foreach ($yuklenenResimler as $index => $resim) {
                UrunResim::create([
                    'urun_id' => $urun->id,
                    'resim' => $resim,
                    'sira' => $maxSira + $index + 1,
                ]);
            }
        }

        // Stok degisikligi varsa kaydet
        $eskiStok = $urun->getOriginal('stok');
        $yeniStok = $validated['stok'];
        if ($eskiStok != $yeniStok) {
            $fark = $yeniStok - $eskiStok;
            StokHareketi::kaydet(
                $urun->id,
                'duzeltme',
                $fark,
                $eskiStok,
                'Manuel stok guncelleme'
            );
        }

        // Kategori attributelarini guncelle
        if ($request->has('attributes') && is_array($request->attributes)) {
            // Mevcut attributelari sil
            $urun->attributeValues()->delete();

            // Yenilerini ekle
            foreach ($request->attributes as $attributeId => $deger) {
                if (!empty($deger)) {
                    UrunAttributeValue::create([
                        'urun_id' => $urun->id,
                        'attribute_id' => $attributeId,
                        'deger' => is_array($deger) ? implode(',', $deger) : $deger,
                    ]);
                }
            }
        }

        return redirect()->route('seller.dashboard')
            ->with('basarili', 'Urun basariyla guncellendi!');
    }

    public function destroy(Urun $urun): RedirectResponse
    {
        if ($urun->satici_id !== auth()->id()) {
            return redirect()->route('seller.dashboard')
                ->with('hata', 'Bu ürünü silme yetkiniz yok.');
        }

        // Tamamlanmış ödeme varsa silme, pasif yap
        $tamamlanmisOdeme = $urun->odemeler()->where('durum', 'odendi')->exists();
        if ($tamamlanmisOdeme) {
            $urun->update(['durum' => 'pasif', 'satildi' => true]);
            return redirect()->route('seller.dashboard')
                ->with('basarili', 'Ürün satış geçmişi olduğu için silmek yerine pasif yapıldı.');
        }

        try {
            // Foreign key kontrolünü kapat
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Tüm ilişkili tabloları temizle (foreign key sırasına göre)
            \DB::table('sepet')->where('urun_id', $urun->id)->delete();
            \DB::table('sepet_items')->where('urun_id', $urun->id)->delete();
            \DB::table('siparis_detaylari')->where('urun_id', $urun->id)->delete();
            \DB::table('favoriler')->where('urun_id', $urun->id)->delete();
            \DB::table('yorumlar')->where('urun_id', $urun->id)->delete();
            \DB::table('kargolar')->where('urun_id', $urun->id)->delete();
            \DB::table('odemeler')->where('urun_id', $urun->id)->delete();
            \DB::table('stok_hareketleri')->where('urun_id', $urun->id)->delete();
            \DB::table('urun_attribute_values')->where('urun_id', $urun->id)->delete();

            // Ürün resimlerini sil
            $resimler = \DB::table('urun_resimleri')->where('urun_id', $urun->id)->get();
            foreach ($resimler as $resim) {
                Storage::delete('public/urunler/' . $resim->resim);
            }
            \DB::table('urun_resimleri')->where('urun_id', $urun->id)->delete();

            // Konuşmaları ve ilişkili kayıtları sil
            $konusmaIds = \DB::table('konusmalar')->where('urun_id', $urun->id)->pluck('id');
            if ($konusmaIds->count() > 0) {
                \DB::table('mesajlar')->whereIn('konusma_id', $konusmaIds)->delete();
                \DB::table('teklifler')->whereIn('konusma_id', $konusmaIds)->delete();
                \DB::table('konusmalar')->where('urun_id', $urun->id)->delete();
            }

            // Ana resmi sil
            if ($urun->resim) {
                Storage::delete('public/urunler/' . $urun->resim);
            }

            // Ürünü sil
            \DB::table('urunler')->where('id', $urun->id)->delete();

            // Foreign key kontrolünü aç
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('seller.dashboard')
                ->with('basarili', 'Ürün başarıyla silindi!');
        } catch (\Exception $e) {
            // Foreign key kontrolünü aç (hata durumunda da)
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('seller.dashboard')
                ->with('hata', 'Ürün silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Ürünü satıldı/satılmadı olarak işaretle
     */
    public function toggleSold(Request $request, Urun $urun): RedirectResponse
    {
        if ($urun->satici_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $satildi = $request->input('satildi', 0) == 1;
        $urun->update(['satildi' => $satildi]);

        $mesaj = $satildi ? 'Urun satildi olarak isaretlendi.' : 'Urun tekrar satisa acildi.';

        return back()->with('basarili', $mesaj);
    }

    /**
     * Kategori attributelarini getir (AJAX)
     */
    public function getCategoryAttributes(Kategori $kategori): JsonResponse
    {
        $attributes = $kategori->attributes()->orderBy('sira')->get();

        return response()->json([
            'success' => true,
            'attributes' => $attributes,
        ]);
    }
}
