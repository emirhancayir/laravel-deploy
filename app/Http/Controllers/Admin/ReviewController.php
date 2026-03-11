<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Yorum;
use App\Models\AdminActivityLog;
use App\Notifications\YeniYorumBildirimi;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Tüm yorumları listele
     */
    public function index(Request $request)
    {
        $query = Yorum::with(['kullanici', 'urun']);

        // Durum filtresi
        if ($request->filled('durum')) {
            if ($request->durum === 'bekleyen') {
                $query->bekleyen();
            } elseif ($request->durum === 'onaylanan') {
                $query->onaylanan();
            }
        }

        // Puan filtresi
        if ($request->filled('puan')) {
            $query->where('puan', $request->puan);
        }

        // Arama
        if ($request->filled('ara')) {
            $arama = $request->ara;
            $query->where(function ($q) use ($arama) {
                $q->where('yorum', 'like', "%{$arama}%")
                    ->orWhereHas('kullanici', function ($q2) use ($arama) {
                        $q2->where('ad', 'like', "%{$arama}%")
                            ->orWhere('soyad', 'like', "%{$arama}%");
                    })
                    ->orWhereHas('urun', function ($q2) use ($arama) {
                        $q2->where('baslik', 'like', "%{$arama}%");
                    });
            });
        }

        $yorumlar = $query->latest()->paginate(20)->withQueryString();

        // İstatistikler
        $istatistikler = [
            'toplam' => Yorum::count(),
            'bekleyen' => Yorum::bekleyen()->count(),
            'onaylanan' => Yorum::onaylanan()->count(),
            'ortalama_puan' => round(Yorum::onaylanan()->avg('puan'), 1) ?: 0,
        ];

        return view('admin.reviews.index', compact('yorumlar', 'istatistikler'));
    }

    /**
     * Yorum onayla
     */
    public function approve(Yorum $yorum)
    {
        $yorum->onayla();

        // Satıcıya bildirim gönder
        $urun = $yorum->urun;
        if ($urun && $urun->satici) {
            try {
                $urun->satici->notify(new YeniYorumBildirimi($yorum, 'onaylandi'));
            } catch (\Exception $e) {
                \Log::error('Yorum onay bildirimi gonderilemedi: ' . $e->getMessage());
            }
        }

        AdminActivityLog::log('yorum.approved', $yorum, null, null,
            "Yorum onaylandı: {$yorum->kullanici->ad_soyad} - {$yorum->urun->urun_adi}");

        return back()->with('basarili', 'Yorum onaylandı.');
    }

    /**
     * Yorum reddet/sil
     */
    public function reject(Yorum $yorum)
    {
        $kullaniciAdi = $yorum->kullanici->ad_soyad;
        $urunBaslik = $yorum->urun->baslik;

        AdminActivityLog::log('yorum.rejected', null, [
            'kullanici' => $kullaniciAdi,
            'urun' => $urunBaslik,
            'puan' => $yorum->puan,
        ], null, "Yorum reddedildi: {$kullaniciAdi} - {$urunBaslik}");

        $yorum->delete();

        return back()->with('basarili', 'Yorum silindi.');
    }

    /**
     * Toplu onaylama
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'yorumlar' => 'required|array',
            'yorumlar.*' => 'exists:yorumlar,id',
        ]);

        $yorumlar = Yorum::with(['urun.satici'])->whereIn('id', $request->yorumlar)->get();

        foreach ($yorumlar as $yorum) {
            $yorum->update([
                'onaylandi' => true,
                'onay_tarihi' => now(),
            ]);

            // Satıcıya bildirim gönder
            if ($yorum->urun && $yorum->urun->satici) {
                try {
                    $yorum->urun->satici->notify(new YeniYorumBildirimi($yorum, 'onaylandi'));
                } catch (\Exception $e) {
                    // Devam et
                }
            }
        }

        AdminActivityLog::log('yorum.bulk_approved', null, null, null,
            "{$yorumlar->count()} yorum toplu onaylandı");

        return back()->with('basarili', "{$yorumlar->count()} yorum onaylandı.");
    }

    /**
     * Toplu silme
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'yorumlar' => 'required|array',
            'yorumlar.*' => 'exists:yorumlar,id',
        ]);

        $sayi = Yorum::whereIn('id', $request->yorumlar)->delete();

        AdminActivityLog::log('yorum.bulk_deleted', null, null, null,
            "{$sayi} yorum toplu silindi");

        return back()->with('basarili', "{$sayi} yorum silindi.");
    }
}
