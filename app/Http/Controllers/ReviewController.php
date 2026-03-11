<?php

namespace App\Http\Controllers;

use App\Models\Yorum;
use App\Models\Urun;
use App\Models\Odeme;
use App\Models\YasakliKelime;
use App\Notifications\YeniYorumBildirimi;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Yorum ekle (sadece satın alan kullanıcılar)
     */
    public function store(Request $request, Urun $urun)
    {
        $request->validate([
            'puan' => 'required|integer|min:1|max:5',
            'yorum' => 'nullable|string|max:1000',
            'siparis_id' => 'nullable|exists:siparisler,id',
            'resimler' => 'nullable|array|max:5',
            'resimler.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $kullaniciId = auth()->id();

        // Bu ürünü satın almış mı kontrol et
        $satinanMi = Odeme::where('alici_id', $kullaniciId)
            ->where('urun_id', $urun->id)
            ->where('durum', 'odendi')
            ->exists();

        if (!$satinanMi) {
            return back()->with('hata', 'Sadece satın aldığınız ürünlere yorum yapabilirsiniz.');
        }

        // Daha önce yorum yapmış mı?
        $mevcutYorum = Yorum::where('kullanici_id', $kullaniciId)
            ->where('urun_id', $urun->id)
            ->first();

        if ($mevcutYorum) {
            return back()->with('hata', 'Bu ürüne zaten yorum yapmışsınız.');
        }

        // Yasakli kelime kontrolu
        if ($request->filled('yorum')) {
            $yorumKontrol = YasakliKelime::metinGecerliMi($request->yorum, 'yorum');
            if (!$yorumKontrol['gecerli']) {
                return back()->withInput()->withErrors(['yorum' => $yorumKontrol['mesaj']]);
            }
        }

        // Resimleri kaydet
        $resimler = [];
        if ($request->hasFile('resimler')) {
            $uploadDir = public_path('uploads/yorumlar');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($request->file('resimler') as $resim) {
                try {
                    if ($resim && $resim->isValid()) {
                        $dosyaAdi = uniqid() . '_' . time() . '.' . $resim->getClientOriginalExtension();
                        $resim->move($uploadDir, $dosyaAdi);
                        $resimler[] = $dosyaAdi;
                    }
                } catch (\Exception $e) {
                    \Log::error('Yorum resim yukleme hatasi: ' . $e->getMessage());
                }
            }
        }

        $yorum = Yorum::create([
            'kullanici_id' => $kullaniciId,
            'urun_id' => $urun->id,
            'siparis_id' => $request->siparis_id,
            'puan' => $request->puan,
            'yorum' => $request->yorum,
            'resimler' => !empty($resimler) ? $resimler : null,
            'onaylandi' => false, // Admin onayı bekleyecek
        ]);

        // Satıcıya bildirim gönder
        if ($urun->satici && $urun->satici_id !== $kullaniciId) {
            try {
                $urun->satici->notify(new YeniYorumBildirimi($yorum, 'yeni'));
            } catch (\Exception $e) {
                \Log::error('Yorum bildirimi gonderilemedi: ' . $e->getMessage());
            }
        }

        return back()->with('basarili', 'Yorumunuz gönderildi. Onaylandıktan sonra yayınlanacaktır.');
    }

    /**
     * Kendi yorumunu güncelle
     */
    public function update(Request $request, Yorum $yorum)
    {
        // Sadece kendi yorumunu düzenleyebilir
        if ($yorum->kullanici_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'puan' => 'required|integer|min:1|max:5',
            'yorum' => 'nullable|string|max:1000',
            'resimler' => 'nullable|array|max:5',
            'resimler.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Yasakli kelime kontrolu
        if ($request->filled('yorum')) {
            $yorumKontrol = YasakliKelime::metinGecerliMi($request->yorum, 'yorum');
            if (!$yorumKontrol['gecerli']) {
                return back()->withInput()->withErrors(['yorum' => $yorumKontrol['mesaj']]);
            }
        }

        // Yeni resimler varsa kaydet
        $resimler = $yorum->resimler ?? [];
        if ($request->hasFile('resimler')) {
            $uploadDir = public_path('uploads/yorumlar');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($request->file('resimler') as $resim) {
                $dosyaAdi = uniqid() . '_' . time() . '.' . $resim->getClientOriginalExtension();
                $resim->move($uploadDir, $dosyaAdi);
                $resimler[] = $dosyaAdi;
            }
        }

        $yorum->update([
            'puan' => $request->puan,
            'yorum' => $request->yorum,
            'resimler' => !empty($resimler) ? $resimler : null,
            'onaylandi' => false, // Düzenleme sonrası tekrar onay beklesin
            'onay_tarihi' => null,
        ]);

        return back()->with('basarili', 'Yorumunuz güncellendi. Onaylandıktan sonra yayınlanacaktır.');
    }

    /**
     * Kendi yorumunu sil
     */
    public function destroy(Yorum $yorum)
    {
        // Sadece kendi yorumunu silebilir
        if ($yorum->kullanici_id !== auth()->id()) {
            abort(403);
        }

        // Resimleri sil
        if ($yorum->resimler && is_array($yorum->resimler)) {
            foreach ($yorum->resimler as $resim) {
                $dosyaYolu = public_path('uploads/yorumlar/' . $resim);
                if (file_exists($dosyaYolu)) {
                    unlink($dosyaYolu);
                }
            }
        }

        $yorum->delete();

        return back()->with('basarili', 'Yorumunuz silindi.');
    }

    /**
     * Ürünün yorumlarını listele (AJAX için)
     */
    public function getProductReviews(Urun $urun)
    {
        $yorumlar = Yorum::with('kullanici')
            ->forUrun($urun->id)
            ->onaylanan()
            ->latest()
            ->paginate(10);

        return response()->json([
            'yorumlar' => $yorumlar,
            'ortalama' => $urun->ortalamaPuan(),
            'toplam' => $urun->yorumSayisi(),
        ]);
    }
}
