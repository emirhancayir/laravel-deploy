<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Urun;
use App\Models\Kategori;
use App\Models\Konusma;
use App\Models\Teklif;
use App\Models\Odeme;
use App\Models\IpLog;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin dashboard ana sayfasi
     */
    public function index()
    {
        // Kullanici istatistikleri
        $kullaniciStats = [
            'toplam' => User::count(),
            'alici' => User::where('kullanici_tipi', 'alici')->count(),
            'satici' => User::where('kullanici_tipi', 'satici')->count(),
            'admin' => User::whereIn('kullanici_tipi', ['admin', 'super_admin'])->count(),
            'banli' => User::where('is_banned', true)->count(),
            'bugun_kayit' => User::whereDate('created_at', today())->count(),
            'bu_hafta_kayit' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Urun istatistikleri
        $urunStats = [
            'toplam' => Urun::count(),
            'aktif' => Urun::where('durum', 'aktif')->count(),
            'pasif' => Urun::where('durum', 'pasif')->count(),
            'satildi' => Urun::where('satildi', true)->count(),
            'stokta' => Urun::where('stok', '>', 0)->count(),
            'bugun_eklenen' => Urun::whereDate('created_at', today())->count(),
        ];

        // Teklif istatistikleri
        $teklifStats = [
            'toplam' => Teklif::count(),
            'beklemede' => Teklif::where('durum', 'beklemede')->count(),
            'kabul_edildi' => Teklif::where('durum', 'kabul_edildi')->count(),
            'reddedildi' => Teklif::where('durum', 'reddedildi')->count(),
            'bugun' => Teklif::whereDate('created_at', today())->count(),
        ];

        // Konusma istatistikleri
        $konusmaStats = [
            'toplam' => Konusma::count(),
            'aktif' => Konusma::where('durum', 'aktif')->count(),
            'bugun' => Konusma::whereDate('created_at', today())->count(),
        ];

        // Kategori istatistikleri
        $kategoriStats = Kategori::withCount('urunler')->get();

        // Odeme istatistikleri
        $odemeStats = [
            'toplam' => Odeme::count(),
            'beklemede' => Odeme::where('durum', 'beklemede')->count(),
            'odendi' => Odeme::where('durum', 'odendi')->count(),
            'iptal' => Odeme::whereIn('durum', ['iptal', 'iade', 'basarisiz'])->count(),
            'toplam_gelir' => Odeme::where('durum', 'odendi')->sum('toplam_tutar'),
            'bu_ay_gelir' => Odeme::where('durum', 'odendi')
                ->whereMonth('odeme_tarihi', now()->month)
                ->whereYear('odeme_tarihi', now()->year)
                ->sum('toplam_tutar'),
            'bugun_gelir' => Odeme::where('durum', 'odendi')
                ->whereDate('odeme_tarihi', today())
                ->sum('toplam_tutar'),
            'komisyon_geliri' => Odeme::where('durum', 'odendi')->sum('komisyon_tutari'),
        ];

        // En cok satan saticilar (son 30 gun)
        $enCokSatanSaticilar = User::select('users.id', 'users.ad', 'users.soyad', 'users.email', 'users.firma_adi')
            ->selectRaw('COUNT(odemeler.id) as satis_sayisi')
            ->selectRaw('SUM(odemeler.toplam_tutar) as toplam_satis')
            ->join('odemeler', 'users.id', '=', 'odemeler.satici_id')
            ->where('odemeler.durum', 'odendi')
            ->where('odemeler.odeme_tarihi', '>=', now()->subDays(30))
            ->groupBy('users.id', 'users.ad', 'users.soyad', 'users.email', 'users.firma_adi')
            ->orderByDesc('satis_sayisi')
            ->limit(5)
            ->get();

        // En populer urunler (goruntulenme)
        $enPopulerUrunler = Urun::with(['satici', 'kategori'])
            ->where('durum', 'aktif')
            ->orderByDesc('goruntulenme_sayisi')
            ->limit(5)
            ->get();

        // Supheli IP aktiviteleri (cok fazla kayit veya islem yapan IP'ler)
        $supheliIpler = IpLog::select('ip_address')
            ->selectRaw('COUNT(*) as islem_sayisi')
            ->selectRaw('COUNT(DISTINCT user_id) as kullanici_sayisi')
            ->whereDate('created_at', today())
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) > 20 OR COUNT(DISTINCT user_id) > 3')
            ->orderByDesc('islem_sayisi')
            ->limit(10)
            ->get();

        // Gunluk gelir grafigi (son 7 gun)
        $gunlukGelir = Odeme::select(DB::raw('DATE(odeme_tarihi) as tarih'), DB::raw('SUM(toplam_tutar) as toplam'))
            ->where('durum', 'odendi')
            ->whereBetween('odeme_tarihi', [now()->subDays(7), now()])
            ->groupBy('tarih')
            ->orderBy('tarih')
            ->get()
            ->pluck('toplam', 'tarih')
            ->toArray();

        // Son aktiviteler
        $sonAktiviteler = AdminActivityLog::with('admin')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Son kullanicilar
        $sonKullanicilar = User::latest()
            ->take(5)
            ->get();

        // Son urunler
        $sonUrunler = Urun::with(['satici', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        // Grafik verileri (son 7 gun)
        $gunlukKayitlar = User::select(DB::raw('DATE(created_at) as tarih'), DB::raw('count(*) as sayi'))
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('tarih')
            ->orderBy('tarih')
            ->get()
            ->pluck('sayi', 'tarih')
            ->toArray();

        $gunlukUrunler = Urun::select(DB::raw('DATE(created_at) as tarih'), DB::raw('count(*) as sayi'))
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('tarih')
            ->orderBy('tarih')
            ->get()
            ->pluck('sayi', 'tarih')
            ->toArray();

        return view('admin.dashboard.index', compact(
            'kullaniciStats',
            'urunStats',
            'teklifStats',
            'konusmaStats',
            'kategoriStats',
            'odemeStats',
            'enCokSatanSaticilar',
            'enPopulerUrunler',
            'supheliIpler',
            'sonAktiviteler',
            'sonKullanicilar',
            'sonUrunler',
            'gunlukKayitlar',
            'gunlukUrunler',
            'gunlukGelir'
        ));
    }

    /**
     * Admin bildirim sayılarını döndür (AJAX polling için)
     */
    public function notifications()
    {
        $counts = [
            'pending_products' => Urun::where('onay_durumu', 'beklemede')->count(),
            'pending_reviews' => \App\Models\Yorum::where('onay_durumu', 'beklemede')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_orders_today' => Odeme::whereDate('created_at', today())->count(),
        ];

        // Session'dan önceki değerleri al
        $previous = session('admin_notification_counts', [
            'pending_products' => 0,
            'pending_reviews' => 0,
            'new_users_today' => 0,
            'new_orders_today' => 0,
        ]);

        // Yeni bildirimleri hesapla
        $notifications = [];

        if ($counts['pending_products'] > $previous['pending_products']) {
            $diff = $counts['pending_products'] - $previous['pending_products'];
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fa-box',
                'title' => 'Yeni Ürün',
                'message' => $diff . ' yeni ürün onay bekliyor',
                'url' => route('admin.products.pending')
            ];
        }

        if ($counts['pending_reviews'] > $previous['pending_reviews']) {
            $diff = $counts['pending_reviews'] - $previous['pending_reviews'];
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fa-comment',
                'title' => 'Yeni Yorum',
                'message' => $diff . ' yeni yorum onay bekliyor',
                'url' => route('admin.reviews.index')
            ];
        }

        if ($counts['new_users_today'] > $previous['new_users_today']) {
            $diff = $counts['new_users_today'] - $previous['new_users_today'];
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fa-user-plus',
                'title' => 'Yeni Kullanıcı',
                'message' => $diff . ' yeni kullanıcı kaydoldu',
                'url' => route('admin.users.index')
            ];
        }

        if ($counts['new_orders_today'] > $previous['new_orders_today']) {
            $diff = $counts['new_orders_today'] - $previous['new_orders_today'];
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fa-shopping-cart',
                'title' => 'Yeni Sipariş',
                'message' => $diff . ' yeni sipariş geldi',
                'url' => route('admin.dashboard')
            ];
        }

        // Session'a kaydet
        session(['admin_notification_counts' => $counts]);

        return response()->json([
            'counts' => $counts,
            'notifications' => $notifications
        ]);
    }
}
