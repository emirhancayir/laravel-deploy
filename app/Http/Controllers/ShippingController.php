<?php

namespace App\Http\Controllers;

use App\Models\Il;
use App\Models\Kargo;
use App\Models\KargoFirmasi;
use App\Models\Konusma;
use App\Models\Teklif;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    /**
     * Kargo oluşturma formu (satıcı için)
     */
    public function create(Konusma $konusma)
    {
        // Sadece satıcı kargo oluşturabilir
        if ($konusma->satici_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Kabul edilmiş teklif var mı?
        $teklif = $konusma->teklifler()->where('durum', 'kabul')->latest()->first();

        if (!$teklif) {
            return back()->with('hata', 'Önce bir teklifin kabul edilmesi gerekiyor.');
        }

        // Zaten kargo var mı?
        $mevcutKargo = Kargo::where('konusma_id', $konusma->id)->first();
        if ($mevcutKargo) {
            return redirect()->route('shipping.show', $mevcutKargo)->with('hata', 'Bu konuşma için zaten kargo oluşturulmuş.');
        }

        $kargoFirmalari = KargoFirmasi::aktif()->get();
        $iller = Il::orderBy('il_adi')->get();

        return view('shipping.create', compact('konusma', 'teklif', 'kargoFirmalari', 'iller'));
    }

    /**
     * Kargo oluştur
     */
    public function store(Request $request, Konusma $konusma)
    {
        if ($konusma->satici_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'kargo_firmasi_id' => 'required|exists:kargo_firmalari,id',
            'kargo_ucreti' => 'required|numeric|min:0',
            'notlar' => 'nullable|string|max:500',
        ], [
            'kargo_firmasi_id.required' => 'Lütfen kargo firması seçin.',
            'kargo_ucreti.required' => 'Kargo ücreti zorunludur.',
            'kargo_ucreti.numeric' => 'Geçerli bir kargo ücreti girin.',
        ]);

        $teklif = $konusma->teklifler()->where('durum', 'kabul')->latest()->first();

        if (!$teklif) {
            return back()->with('hata', 'Kabul edilmiş teklif bulunamadı.');
        }

        $kargo = Kargo::create([
            'konusma_id' => $konusma->id,
            'teklif_id' => $teklif->id,
            'gonderen_id' => auth()->id(),
            'alici_id' => $konusma->alici_id,
            'urun_id' => $konusma->urun_id,
            'kargo_firmasi_id' => $validated['kargo_firmasi_id'],
            'urun_fiyati' => $teklif->tutar,
            'kargo_ucreti' => $validated['kargo_ucreti'],
            'durum' => 'beklemede',
            'notlar' => $validated['notlar'],
        ]);

        return redirect()->route('chat.show', $konusma)->with('basarili', 'Kargo oluşturuldu. Alıcının adres bilgisi girmesi bekleniyor.');
    }

    /**
     * Kargo detayı
     */
    public function show(Kargo $kargo)
    {
        // Sadece gönderen veya alıcı görebilir
        if ($kargo->gonderen_id !== auth()->id() && $kargo->alici_id !== auth()->id()) {
            abort(403, 'Bu kargoya erişim yetkiniz yok.');
        }

        $kargo->load(['konusma', 'teklif', 'gonderen', 'alici', 'urun', 'kargoFirmasi', 'aliciIl', 'aliciIlce', 'aliciMahalle']);
        $iller = Il::orderBy('il_adi')->get();

        return view('shipping.show', compact('kargo', 'iller'));
    }

    /**
     * Alıcı adresini gir
     */
    public function saveAddress(Request $request, Kargo $kargo)
    {
        if ($kargo->alici_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        if ($kargo->durum !== 'beklemede') {
            return back()->with('hata', 'Adres bilgisi zaten girilmiş.');
        }

        $validated = $request->validate([
            'alici_il_id' => 'required|exists:iller,id',
            'alici_ilce_id' => 'required|exists:ilceler,id',
            'alici_mahalle_id' => 'required|exists:mahalleler,id',
            'alici_adres_detay' => 'required|string|max:500',
            'alici_telefon' => 'required|string|max:20',
        ], [
            'alici_il_id.required' => 'Lütfen il seçin.',
            'alici_ilce_id.required' => 'Lütfen ilçe seçin.',
            'alici_mahalle_id.required' => 'Lütfen mahalle seçin.',
            'alici_adres_detay.required' => 'Adres detayı zorunludur.',
            'alici_telefon.required' => 'Telefon numarası zorunludur.',
        ]);

        $kargo->update([
            'alici_il_id' => $validated['alici_il_id'],
            'alici_ilce_id' => $validated['alici_ilce_id'],
            'alici_mahalle_id' => $validated['alici_mahalle_id'],
            'alici_adres_detay' => $validated['alici_adres_detay'],
            'alici_telefon' => $validated['alici_telefon'],
            'durum' => 'hazirlaniyor',
        ]);

        return back()->with('basarili', 'Adres bilgileri kaydedildi. Satıcı ürünü hazırlıyor.');
    }

    /**
     * Takip numarası gir (satıcı için)
     */
    public function saveTrackingNumber(Request $request, Kargo $kargo)
    {
        if ($kargo->gonderen_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'takip_no' => 'required|string|max:50',
        ], [
            'takip_no.required' => 'Takip numarası zorunludur.',
        ]);

        $kargo->update([
            'takip_no' => $validated['takip_no'],
            'durum' => 'kargoda',
        ]);

        return back()->with('basarili', 'Takip numarası kaydedildi. Kargo yola çıktı!');
    }

    /**
     * Teslim edildi olarak işaretle
     */
    public function markDelivered(Kargo $kargo)
    {
        // Hem satıcı hem alıcı teslim edildi diyebilir
        if ($kargo->gonderen_id !== auth()->id() && $kargo->alici_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $kargo->update(['durum' => 'teslim_edildi']);

        return back()->with('basarili', 'Kargo teslim edildi olarak işaretlendi.');
    }

    /**
     * Kargo ücreti hesapla (AJAX)
     */
    public function calculateFee(Request $request): JsonResponse
    {
        $gonderenIl = $request->input('gonderen_il');
        $aliciIl = $request->input('alici_il');

        // Basit hesaplama: Aynı il 30₺, farklı il 50₺
        if ($gonderenIl && $aliciIl) {
            $ucret = ($gonderenIl == $aliciIl) ? 30 : 50;
        } else {
            $ucret = 40; // Varsayılan
        }

        return response()->json(['ucret' => $ucret]);
    }

    /**
     * Kullanıcının tüm kargolarını listele
     */
    public function myShipments(Request $request)
    {
        $user = auth()->user();

        $query = Kargo::with(['gonderen', 'alici', 'urun', 'kargoFirmasi'])
            ->where(function ($q) use ($user) {
                $q->where('gonderen_id', $user->id)
                    ->orWhere('alici_id', $user->id);
            });

        // Durum filtresi
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }

        // Tip filtresi (gelen/giden)
        if ($request->filled('tip')) {
            if ($request->tip === 'gelen') {
                $query->where('alici_id', $user->id);
            } elseif ($request->tip === 'giden') {
                $query->where('gonderen_id', $user->id);
            }
        }

        $kargolar = $query->latest()->paginate(10)->withQueryString();

        // İstatistikler
        $istatistikler = [
            'toplam' => Kargo::where('gonderen_id', $user->id)->orWhere('alici_id', $user->id)->count(),
            'gelen' => Kargo::where('alici_id', $user->id)->count(),
            'giden' => Kargo::where('gonderen_id', $user->id)->count(),
            'kargoda' => Kargo::where(function ($q) use ($user) {
                $q->where('gonderen_id', $user->id)->orWhere('alici_id', $user->id);
            })->where('durum', 'kargoda')->count(),
            'bekleyen' => Kargo::where(function ($q) use ($user) {
                $q->where('gonderen_id', $user->id)->orWhere('alici_id', $user->id);
            })->whereIn('durum', ['beklemede', 'hazirlaniyor'])->count(),
        ];

        return view('shipping.my-shipments', compact('kargolar', 'istatistikler'));
    }
}
