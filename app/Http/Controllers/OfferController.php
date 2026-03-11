<?php

namespace App\Http\Controllers;

use App\Models\Konusma;
use App\Models\Teklif;
use App\Events\TeklifGonderildi;
use App\Notifications\YeniTeklifBildirimi;
use App\Models\YasakliKelime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class OfferController extends Controller
{
    /**
     * Teklif gonder
     */
    public function store(Request $request, Konusma $konusma): JsonResponse|RedirectResponse
    {
        $this->authorize('view', $konusma);

        $request->validate([
            'tutar' => 'required|numeric|min:1|max:99999',
            'not' => 'nullable|string|max:500',
        ]);

        // Yasakli kelime kontrolu (not alani icin)
        if ($request->filled('not')) {
            $notKontrol = YasakliKelime::metinGecerliMi($request->not, 'mesaj');
            if (!$notKontrol['gecerli']) {
                $error = $notKontrol['mesaj'];
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => $error], 400);
                }
                return back()->withErrors(['not' => $error]);
            }
        }

        $user = auth()->user();

        // Gunluk teklif limiti kontrolu
        if ($user->teklifLimitineUlastiMi()) {
            $limit = config('zamason.gunluk_teklif_limiti', 5);
            $error = "Gunluk teklif limitinize ulastiniz ({$limit}/gun). Yarin tekrar deneyebilirsiniz.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 429);
            }
            return back()->with('hata', $error);
        }

        // Beklemede baska teklif var mi kontrol et
        $bekleyenTeklif = $konusma->teklifler()
            ->where('durum', 'beklemede')
            ->first();

        if ($bekleyenTeklif) {
            $error = 'Bu konusmada beklemede bir teklif var. Once onu sonuclandirin.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 400);
            }
            return back()->with('hata', $error);
        }

        // Teklif mesaji olustur
        $mesajMetni = "Teklif: " . number_format($request->tutar, 2, ',', '.') . " TL";
        if ($request->filled('not')) {
            $mesajMetni .= "\nNot: " . $request->not;
        }

        $mesaj = $konusma->mesajlar()->create([
            'gonderen_id' => $user->id,
            'mesaj' => $mesajMetni,
            'tip' => 'teklif',
        ]);

        // Teklifi olustur
        $teklif = Teklif::create([
            'konusma_id' => $konusma->id,
            'mesaj_id' => $mesaj->id,
            'teklif_eden_id' => $user->id,
            'tutar' => $request->tutar,
            'not' => $request->not,
            'gecerlilik_tarihi' => now()->addDays(2), // 2 gun gecerli
        ]);

        // Okunmamis sayisini guncelle
        $rol = $konusma->kullaniciRolu($user);
        if ($rol === 'alici') {
            $konusma->increment('okunmamis_satici');
        } else {
            $konusma->increment('okunmamis_alici');
        }

        $konusma->update(['son_mesaj_tarihi' => now()]);

        // Urun sahibine bildirim gonder
        $urunSahibi = $konusma->urun->satici;
        if ($urunSahibi && $urunSahibi->id !== $user->id) {
            $urunSahibi->notify(new YeniTeklifBildirimi($teklif, $user, $konusma->urun));
        }

        // Broadcast event (skip if Reverb not running)
        try {
            broadcast(new TeklifGonderildi($teklif, $mesaj))->toOthers();
        } catch (\Exception $e) {
            // Continue without real-time
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'teklif' => [
                    'id' => $teklif->id,
                    'tutar' => $teklif->formatli_tutar,
                    'durum' => $teklif->durum,
                ],
                'mesaj' => [
                    'id' => $mesaj->id,
                    'mesaj' => $mesaj->mesaj,
                    'gonderen_id' => $mesaj->gonderen_id,
                    'tip' => $mesaj->tip,
                    'tarih' => $mesaj->formatli_tarih,
                ],
            ]);
        }

        return back()->with('basarili', 'Teklifiniz gonderildi.');
    }

    /**
     * Teklifi kabul et
     */
    public function accept(Teklif $teklif): JsonResponse|RedirectResponse
    {
        $konusma = $teklif->konusma;
        $this->authorize('view', $konusma);

        // Teklifi sadece karsi taraf kabul edebilir
        if ($teklif->teklif_eden_id === auth()->id()) {
            $error = 'Kendi teklifinizi kabul edemezsiniz.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 403);
            }
            return back()->with('hata', $error);
        }

        if (!$teklif->beklemedeMi()) {
            $error = 'Bu teklif artik gecerli degil.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 400);
            }
            return back()->with('hata', $error);
        }

        $teklif->kabulEt();

        // Sistem mesaji ekle
        $konusma->mesajlar()->create([
            'gonderen_id' => auth()->id(),
            'mesaj' => 'Teklif kabul edildi: ' . $teklif->formatli_tutar,
            'tip' => 'sistem',
        ]);

        $konusma->update(['son_mesaj_tarihi' => now()]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Teklif kabul edildi.',
            ]);
        }

        return back()->with('basarili', 'Teklifi kabul ettiniz!');
    }

    /**
     * Teklifi reddet
     */
    public function reject(Teklif $teklif): JsonResponse|RedirectResponse
    {
        $konusma = $teklif->konusma;
        $this->authorize('view', $konusma);

        if ($teklif->teklif_eden_id === auth()->id()) {
            $error = 'Kendi teklifinizi reddedemezsiniz. Iptal edebilirsiniz.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 403);
            }
            return back()->with('hata', $error);
        }

        if (!$teklif->beklemedeMi()) {
            $error = 'Bu teklif artik gecerli degil.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 400);
            }
            return back()->with('hata', $error);
        }

        $teklif->reddet();

        // Sistem mesaji ekle
        $konusma->mesajlar()->create([
            'gonderen_id' => auth()->id(),
            'mesaj' => 'Teklif reddedildi: ' . $teklif->formatli_tutar,
            'tip' => 'sistem',
        ]);

        $konusma->update(['son_mesaj_tarihi' => now()]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Teklif reddedildi.',
            ]);
        }

        return back()->with('basarili', 'Teklifi reddettiniz.');
    }

    /**
     * Teklifi iptal et (sadece teklif eden)
     */
    public function cancel(Teklif $teklif): JsonResponse|RedirectResponse
    {
        $konusma = $teklif->konusma;
        $this->authorize('view', $konusma);

        if ($teklif->teklif_eden_id !== auth()->id()) {
            $error = 'Sadece kendi teklifinizi iptal edebilirsiniz.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 403);
            }
            return back()->with('hata', $error);
        }

        if (!$teklif->beklemedeMi()) {
            $error = 'Bu teklif artik gecerli degil.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $error], 400);
            }
            return back()->with('hata', $error);
        }

        $teklif->iptalEt();

        // Sistem mesaji ekle
        $konusma->mesajlar()->create([
            'gonderen_id' => auth()->id(),
            'mesaj' => 'Teklif iptal edildi: ' . $teklif->formatli_tutar,
            'tip' => 'sistem',
        ]);

        $konusma->update(['son_mesaj_tarihi' => now()]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Teklif iptal edildi.',
            ]);
        }

        return back()->with('basarili', 'Teklifinizi iptal ettiniz.');
    }
}
