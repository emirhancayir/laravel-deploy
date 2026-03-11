<?php

namespace App\Http\Controllers;

use App\Models\Odeme;
use App\Models\SepetItem;
use App\Models\StokHareketi;
use App\Notifications\OdemeBildirimi;
use App\Services\IyzicoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private IyzicoService $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    /**
     * Checkout sayfasi - adres girisi
     */
    public function checkout(): View|RedirectResponse
    {
        $sepetItems = SepetItem::where('kullanici_id', auth()->id())
            ->with(['urun.kategori', 'urun.satici', 'teklif'])
            ->get();

        if ($sepetItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('hata', 'Sepetiniz bos.');
        }

        // Satilmis urunleri kontrol et
        foreach ($sepetItems as $item) {
            if ($item->urun->satildi) {
                $item->delete();
                return redirect()->route('cart.index')
                    ->with('hata', $item->urun->urun_adi . ' urunu artik mevcut degil.');
            }
        }

        $toplamTutar = $sepetItems->sum('fiyat');
        $kargoTutari = config('zamason.varsayilan_kargo_ucreti', 50);
        $genelToplam = $toplamTutar + $kargoTutari;

        // Adres icin iller
        $iller = \App\Models\Il::orderBy('il_adi')->get();

        return view('payment.checkout', compact(
            'sepetItems',
            'toplamTutar',
            'kargoTutari',
            'genelToplam',
            'iller'
        ));
    }

    /**
     * Odeme islemini baslat
     */
    public function initiate(Request $request): View|RedirectResponse
    {
        $request->validate([
            'teslimat_il_id' => 'required|exists:iller,id',
            'teslimat_ilce_id' => 'required|exists:ilceler,id',
            'teslimat_mahalle_id' => 'nullable|exists:mahalleler,id',
            'teslimat_adres_detay' => 'required|string|max:500',
            'teslimat_telefon' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        $sepetItems = SepetItem::where('kullanici_id', $user->id)
            ->with(['urun', 'teklif', 'konusma'])
            ->get();

        if ($sepetItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('hata', 'Sepetiniz bos.');
        }

        // Simdilik tek urun destegi (ilk item)
        $item = $sepetItems->first();
        $urun = $item->urun;
        $teklif = $item->teklif;

        // Urun kontrolu
        if ($urun->satildi) {
            $item->delete();
            return redirect()->route('cart.index')
                ->with('hata', 'Bu urun artik mevcut degil.');
        }

        // Tutarlar
        $urunTutari = $item->fiyat;
        $kargoTutari = config('zamason.varsayilan_kargo_ucreti', 50);
        $toplamTutar = $urunTutari + $kargoTutari;

        // iyzico sandbox siniri kontrolu (100.000 TL)
        $iyzicoSandbox = str_contains(config('services.iyzico.base_url', ''), 'sandbox');
        if ($iyzicoSandbox && $toplamTutar >= 100000) {
            return back()
                ->withInput()
                ->with('hata', 'Test modunda 100.000 TL ve uzerindeki odemeler desteklenmiyor. Canli moda gecince bu sinir kalkar.');
        }

        DB::beginTransaction();

        try {

            // Kategori bazli komisyon oranini al
            $kategoriKomisyonOrani = $urun->kategori?->komisyon_orani ?? config('zamason.komisyon_orani', 5);

            // Komisyon hesapla (sadece urun tutari uzerinden, kargo haric)
            $komisyon = IyzicoService::komisyonHesapla($urunTutari, $kategoriKomisyonOrani);

            // Satici tutari = urun tutari - komisyon + kargo (kargo komisyonsuz)
            $saticiTutari = $komisyon['satici_tutari'] + $kargoTutari;

            // Odeme kaydi olustur
            $odeme = Odeme::create([
                'alici_id' => $user->id,
                'satici_id' => $urun->satici_id,
                'urun_id' => $urun->id,
                'teklif_id' => $teklif->id,
                'konusma_id' => $item->konusma_id,
                'sepet_item_id' => $item->id,
                'urun_tutari' => $urunTutari,
                'kargo_tutari' => $kargoTutari,
                'komisyon_orani' => $kategoriKomisyonOrani,
                'komisyon_tutari' => $komisyon['komisyon_tutari'],
                'toplam_tutar' => $toplamTutar,
                'satici_tutari' => $saticiTutari,
                'teslimat_il_id' => $request->teslimat_il_id,
                'teslimat_ilce_id' => $request->teslimat_ilce_id,
                'teslimat_mahalle_id' => $request->teslimat_mahalle_id,
                'teslimat_adres_detay' => $request->teslimat_adres_detay,
                'teslimat_telefon' => $request->teslimat_telefon,
                'durum' => 'beklemede',
            ]);

            // iyzico checkout form baslat
            $callbackUrl = route('payment.callback');
            $checkoutForm = $this->iyzicoService->initializeCheckoutForm(
                $odeme,
                $user,
                $urun,
                $callbackUrl
            );

            if (!$checkoutForm || $checkoutForm->getStatus() !== 'success') {
                throw new \Exception($checkoutForm?->getErrorMessage() ?? 'iyzico baglanti hatasi');
            }

            // Token kaydet
            $odeme->update([
                'iyzico_token' => $checkoutForm->getToken(),
            ]);

            DB::commit();

            // Checkout form sayfasi
            return view('payment.form', [
                'odeme' => $odeme,
                'checkoutFormContent' => $checkoutForm->getCheckoutFormContent(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('iyzico odeme hatasi', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('hata', 'Odeme baslatilamadi: ' . $e->getMessage());
        }
    }

    /**
     * iyzico callback
     */
    public function callback(Request $request): RedirectResponse
    {
        $token = $request->input('token');

        if (!$token) {
            return redirect()->route('cart.index')
                ->with('hata', 'Odeme bilgisi alinamadi.');
        }

        // Token ile odeme bul
        $odeme = Odeme::where('iyzico_token', $token)->first();

        if (!$odeme) {
            Log::warning('iyzico callback: odeme bulunamadi', ['token' => $token]);
            return redirect()->route('cart.index')
                ->with('hata', 'Odeme kaydi bulunamadi.');
        }

        // iyzico sonucunu al
        $checkoutForm = $this->iyzicoService->retrieveCheckoutForm($token);

        if (!$checkoutForm) {
            $odeme->update([
                'durum' => 'basarisiz',
                'hata_mesaji' => 'iyzico sonucu alinamadi',
            ]);

            return redirect()->route('cart.index')
                ->with('hata', 'Odeme sonucu alinamadi.');
        }

        // Sonucu kaydet
        $odeme->update([
            'iyzico_response' => [
                'status' => $checkoutForm->getStatus(),
                'errorCode' => $checkoutForm->getErrorCode(),
                'errorMessage' => $checkoutForm->getErrorMessage(),
                'paymentId' => $checkoutForm->getPaymentId(),
            ],
        ]);

        if ($checkoutForm->getStatus() === 'success') {
            // Payment transaction ID al
            $paymentItems = $checkoutForm->getPaymentItems();
            $paymentTransactionId = $paymentItems[0]?->getPaymentTransactionId();

            $odeme->update([
                'durum' => 'odendi',
                'iyzico_payment_id' => $checkoutForm->getPaymentId(),
                'payment_transaction_id' => $paymentTransactionId,
                'odeme_tarihi' => now(),
            ]);

            // Urunu satildi olarak isaretle
            $odeme->urun->update(['satildi' => true]);

            // Stok dusur
            $odeme->urun->stokGuncelle(-1, 'satis', 'Odeme #' . $odeme->id);

            // Sepetten sil
            if ($odeme->sepet_item_id) {
                SepetItem::where('id', $odeme->sepet_item_id)->delete();
            }

            // Konusmaya sistem mesaji ekle
            $odeme->konusma->mesajlar()->create([
                'gonderen_id' => $odeme->alici_id,
                'mesaj' => 'Odeme tamamlandi: ' . $odeme->formatli_toplam,
                'tip' => 'sistem',
            ]);

            // Saticiya bildirim gonder
            if ($odeme->satici) {
                $odeme->satici->notify(new OdemeBildirimi($odeme, 'yeni'));
            }

            // Kullaniciyi tekrar giris yaptir (iyzico redirect sonrasi session kaybolabilir)
            if (!Auth::check()) {
                Auth::loginUsingId($odeme->alici_id);
            }

            return redirect()->route('payment.success', $odeme)
                ->with('basarili', 'Odemeniz basariyla tamamlandi!');
        }

        // Odeme basarisiz
        $odeme->update([
            'durum' => 'basarisiz',
            'hata_mesaji' => $checkoutForm->getErrorMessage(),
        ]);

        return redirect()->route('cart.index')
            ->with('hata', 'Odeme basarisiz: ' . $checkoutForm->getErrorMessage());
    }

    /**
     * Basarili odeme sayfasi
     */
    public function success(Odeme $odeme): View|RedirectResponse
    {
        // Kullanici giris yapmamissa ve odeme sahibiyse giris yaptir
        if (!Auth::check()) {
            Auth::loginUsingId($odeme->alici_id);
        }

        // Kullanicinin kendi odemesi mi kontrol et
        if ($odeme->alici_id !== auth()->id()) {
            return redirect()->route('home');
        }

        return view('payment.success', compact('odeme'));
    }

    /**
     * Odeme detay sayfasi
     */
    public function detail(Odeme $odeme): View|RedirectResponse
    {
        // Alici veya satici mi kontrol et
        if ($odeme->alici_id !== auth()->id() && $odeme->satici_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('hata', 'Bu odemeyi goruntuleme yetkiniz yok.');
        }

        return view('payment.detail', compact('odeme'));
    }

    /**
     * Odeme listesi (kullanici)
     */
    public function list(): View
    {
        $user = auth()->user();

        $aliciOdemeleri = Odeme::where('alici_id', $user->id)
            ->with(['urun', 'satici'])
            ->latest()
            ->paginate(10, ['*'], 'alici_page');

        $saticiOdemeleri = Odeme::where('satici_id', $user->id)
            ->with(['urun', 'alici'])
            ->latest()
            ->paginate(10, ['*'], 'satici_page');

        return view('payment.list', compact('aliciOdemeleri', 'saticiOdemeleri'));
    }
}
