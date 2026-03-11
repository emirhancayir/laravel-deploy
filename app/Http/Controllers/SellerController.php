<?php

namespace App\Http\Controllers;

use App\Models\Konusma;
use App\Models\Odeme;
use App\Models\Siparis;
use App\Models\Teklif;
use App\Models\Urun;
use App\Models\Yorum;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();
        $urunler = $user->urunler()->with('kategori')->latest()->get();
        $urunIds = $urunler->pluck('id');

        // Satici olarak konusmalar
        $aktifKonusmalar = Konusma::where('satici_id', $user->id)
            ->where('durum', 'aktif')
            ->count();

        // Bekleyen teklifler (saticiya gelen)
        $bekleyenTeklifler = Teklif::whereHas('konusma', function ($q) use ($user) {
                $q->where('satici_id', $user->id);
            })
            ->where('durum', 'beklemede')
            ->where('teklif_eden_id', '!=', $user->id)
            ->count();

        // Kabul edilen teklifler
        $kabulEdilenTeklifler = Teklif::whereHas('konusma', function ($q) use ($user) {
                $q->where('satici_id', $user->id);
            })
            ->where('durum', 'kabul_edildi')
            ->count();

        // Okunmamis mesajlar
        $okunmamisMesajlar = Konusma::where('satici_id', $user->id)
            ->sum('okunmamis_satici');

        // ==========================================
        // YENİ İSTATİSTİKLER
        // ==========================================

        // Toplam satış adedi ve ciro
        $satisBilgileri = Odeme::where('satici_id', $user->id)
            ->where('durum', 'odendi')
            ->selectRaw('COUNT(*) as toplam_satis, COALESCE(SUM(toplam_tutar), 0) as toplam_ciro')
            ->first();

        $toplamSatis = $satisBilgileri->toplam_satis ?? 0;
        $toplamCiro = $satisBilgileri->toplam_ciro ?? 0;

        // Bekleyen ödemeler
        $bekleyenOdemeler = Odeme::where('satici_id', $user->id)
            ->where('durum', 'beklemede')
            ->count();

        // Toplam ürün görüntülenme
        $toplamGoruntulenme = $urunler->sum('goruntulenme_sayisi');

        // En çok görüntülenen ürünler
        $enCokGoruntulenler = $user->urunler()
            ->orderByDesc('goruntulenme_sayisi')
            ->take(5)
            ->get();

        // Ortalama puan
        $ortalamaPuan = Yorum::whereIn('urun_id', $urunIds)
            ->where('onaylandi', true)
            ->avg('puan');
        $ortalamaPuan = $ortalamaPuan ? round($ortalamaPuan, 1) : null;

        // Toplam yorum sayısı
        $toplamYorum = Yorum::whereIn('urun_id', $urunIds)
            ->where('onaylandi', true)
            ->count();

        // Favorilere eklenme sayısı
        $favoriSayisi = DB::table('favoriler')
            ->whereIn('urun_id', $urunIds)
            ->count();

        // Son 6 aylık satış grafiği verileri
        $aylikSatislar = Odeme::where('satici_id', $user->id)
            ->where('durum', 'odendi')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ay, COUNT(*) as adet, SUM(toplam_tutar) as tutar")
            ->groupBy('ay')
            ->orderBy('ay')
            ->get()
            ->keyBy('ay');

        // Son 6 ay için boş veriler dahil grafiği hazırla
        $grafikVerileri = [];
        for ($i = 5; $i >= 0; $i--) {
            $ay = now()->subMonths($i)->format('Y-m');
            $ayAdi = now()->subMonths($i)->translatedFormat('M Y');
            $grafikVerileri[] = [
                'ay' => $ayAdi,
                'adet' => $aylikSatislar[$ay]->adet ?? 0,
                'tutar' => $aylikSatislar[$ay]->tutar ?? 0,
            ];
        }

        // Son siparişler
        $sonSiparisler = Odeme::where('satici_id', $user->id)
            ->with(['alici', 'urun'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'urunler',
            'aktifKonusmalar',
            'bekleyenTeklifler',
            'kabulEdilenTeklifler',
            'okunmamisMesajlar',
            // Yeni istatistikler
            'toplamSatis',
            'toplamCiro',
            'bekleyenOdemeler',
            'toplamGoruntulenme',
            'enCokGoruntulenler',
            'ortalamaPuan',
            'toplamYorum',
            'favoriSayisi',
            'grafikVerileri',
            'sonSiparisler'
        ));
    }
}
