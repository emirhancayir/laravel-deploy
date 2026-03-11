<?php

namespace App\Http\Controllers;

use App\Models\Konusma;
use App\Models\Mesaj;
use App\Events\MesajGonderildi;
use App\Events\MesajlarOkundu;
use App\Notifications\YeniMesajBildirimi;
use App\Models\YasakliKelime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Gelismis icerik kontrolu
     */
    private function icerikKontrol(string $mesaj): ?string
    {
        // Telefon numarasi (Turkiye formatlari)
        if (preg_match('/(\+90|0)?\s*[5][0-9]{2}[\s\-\.]?[0-9]{3}[\s\-\.]?[0-9]{2}[\s\-\.]?[0-9]{2}/', $mesaj)) {
            return 'Mesajda telefon numarası paylaşamazsınız. Güvenliğiniz için tüm iletişim site üzerinden yapılmalıdır.';
        }

        // Sabit hat numarasi
        if (preg_match('/(\+90|0)?\s*[2-4][0-9]{2}[\s\-\.]?[0-9]{3}[\s\-\.]?[0-9]{2}[\s\-\.]?[0-9]{2}/', $mesaj)) {
            return 'Mesajda telefon numarası paylaşamazsınız.';
        }

        // Email adresi
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $mesaj)) {
            return 'Mesajda e-posta adresi paylaşamazsınız. Güvenliğiniz için tüm iletişim site üzerinden yapılmalıdır.';
        }

        // IBAN numarasi (TR ile baslayan)
        if (preg_match('/TR\s*[0-9]{2}\s*[0-9]{4}\s*[0-9]{4}\s*[0-9]{4}\s*[0-9]{4}\s*[0-9]{4}\s*[0-9]{2}/i', $mesaj)) {
            return 'Mesajda IBAN numarası paylaşamazsınız. Ödemeler site üzerinden güvenle yapılmalıdır.';
        }

        // URL/Link kontrolu
        if (preg_match('/(https?:\/\/|www\.)[^\s]+/i', $mesaj)) {
            return 'Mesajda link paylaşamazsınız.';
        }

        // Kisa link servisleri
        if (preg_match('/(bit\.ly|goo\.gl|tinyurl|t\.co|wa\.me|t\.me)/i', $mesaj)) {
            return 'Mesajda link paylaşamazsınız.';
        }

        // Spam kontrolu - cok fazla buyuk harf
        $buyukHarfSayisi = preg_match_all('/[A-ZÇĞİÖŞÜ]/u', $mesaj);
        $toplamHarf = preg_match_all('/[a-zA-ZçğıöşüÇĞİÖŞÜ]/u', $mesaj);
        if ($toplamHarf > 10 && ($buyukHarfSayisi / $toplamHarf) > 0.7) {
            return 'Lütfen sürekli büyük harf kullanmayın.';
        }

        // Spam kontrolu - tekrar eden karakterler
        if (preg_match('/(.)\1{4,}/u', $mesaj)) {
            return 'Mesajınız spam olarak algılandı.';
        }

        return null; // Sorun yok
    }

    /**
     * Mesaj gonder (AJAX veya normal form)
     */
    public function store(Request $request, Konusma $konusma)
    {
        $this->authorize('view', $konusma);

        $request->validate([
            'mesaj' => 'required|string|max:2000',
        ]);

        // Mesaj metni direkt kullanilacak (filtre yok)
        $mesajMetni = $request->mesaj;

        $user = auth()->user();
        $rol = $konusma->kullaniciRolu($user);

        $mesaj = $konusma->mesajlar()->create([
            'gonderen_id' => $user->id,
            'mesaj' => $mesajMetni,
            'tip' => 'metin',
        ]);

        // Okunmamis sayisini artir
        if ($rol === 'alici') {
            $konusma->increment('okunmamis_satici');
        } else {
            $konusma->increment('okunmamis_alici');
        }

        $konusma->update(['son_mesaj_tarihi' => now()]);

        // Karsi tarafa bildirim gonder
        $alici = $rol === 'alici' ? $konusma->satici : $konusma->alici;
        if ($alici) {
            $alici->notify(new YeniMesajBildirimi($mesaj, $user));
        }

        // Broadcast event for real-time (skip if Reverb not running)
        try {
            broadcast(new MesajGonderildi($mesaj))->toOthers();
        } catch (\Exception $e) {
            // Reverb not running, continue without real-time
        }

        // AJAX ise JSON dön, değilse redirect
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mesaj' => [
                    'id' => $mesaj->id,
                    'mesaj' => $mesaj->mesaj,
                    'gonderen_id' => $mesaj->gonderen_id,
                    'gonderen_ad' => $mesaj->gonderen->ad,
                    'tip' => $mesaj->tip,
                    'tarih' => $mesaj->formatli_tarih,
                    'created_at' => $mesaj->created_at->toISOString(),
                ],
            ]);
        }

        return redirect()->route('chat.show', $konusma)->with('basarili', 'Mesaj gönderildi');
    }

    /**
     * Yeni mesajlari getir (polling fallback)
     */
    public function getNewMessages(Request $request, Konusma $konusma): JsonResponse
    {
        $this->authorize('view', $konusma);

        $sonMesajId = $request->input('son_mesaj_id', 0);

        $yeniMesajlar = $konusma->mesajlar()
            ->where('id', '>', $sonMesajId)
            ->with('gonderen', 'teklif')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'mesaj' => $m->mesaj,
                'gonderen_id' => $m->gonderen_id,
                'gonderen_ad' => $m->gonderen->ad,
                'tip' => $m->tip,
                'tarih' => $m->formatli_tarih,
                'okundu' => $m->okundu,
                'created_at' => $m->created_at->toISOString(),
                'teklif' => $m->teklif ? [
                    'id' => $m->teklif->id,
                    'tutar' => $m->teklif->formatli_tutar,
                    'durum' => $m->teklif->durum,
                ] : null,
            ]);

        // Kendi gönderdiğim mesajların okundu durumunu da döndür
        $okunanMesajlar = $konusma->mesajlar()
            ->where('gonderen_id', auth()->id())
            ->where('okundu', true)
            ->pluck('id')
            ->toArray();

        return response()->json([
            'success' => true,
            'mesajlar' => $yeniMesajlar,
            'okunan_mesajlar' => $okunanMesajlar,
        ]);
    }

    /**
     * Mesajlari okundu olarak isaretle (AJAX)
     */
    public function markAsRead(Konusma $konusma): JsonResponse
    {
        $this->authorize('view', $konusma);

        $user = auth()->user();
        $rol = $konusma->kullaniciRolu($user);

        if ($rol === 'alici') {
            $konusma->update(['okunmamis_alici' => 0]);
        } else {
            $konusma->update(['okunmamis_satici' => 0]);
        }

        $okunanlar = $konusma->mesajlar()
            ->where('gonderen_id', '!=', $user->id)
            ->where('okundu', false)
            ->pluck('id');

        $konusma->mesajlar()
            ->whereIn('id', $okunanlar)
            ->update([
                'okundu' => true,
                'okunma_tarihi' => now(),
            ]);

        // Broadcast okundu event
        if ($okunanlar->isNotEmpty()) {
            try {
                broadcast(new MesajlarOkundu($konusma, $okunanlar->toArray()))->toOthers();
            } catch (\Exception $e) {
                // Reverb not running, continue without real-time
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mesaj sil
     */
    public function destroy($id)
    {
        $mesaj = Mesaj::find($id);

        if (!$mesaj) {
            return back()->with('hata', 'Mesaj bulunamadı.');
        }

        // Sadece kendi mesajını silebilir
        if ($mesaj->gonderen_id !== auth()->id()) {
            return back()->with('hata', 'Bu mesajı silemezsiniz.');
        }

        // Teklif mesajı silinemez
        if ($mesaj->tip === 'teklif' || $mesaj->tip === 'sistem') {
            return back()->with('hata', 'Bu tip mesajlar silinemez.');
        }

        $konusmaId = $mesaj->konusma_id;
        $mesaj->delete();

        return redirect()->route('chat.show', $konusmaId)->with('basarili', 'Mesaj silindi');
    }

    /**
     * Mesaj düzenle
     */
    public function update(Request $request, $id)
    {
        $mesaj = Mesaj::find($id);

        if (!$mesaj) {
            return back()->with('hata', 'Mesaj bulunamadı.');
        }

        // Sadece kendi mesajını düzenleyebilir
        if ($mesaj->gonderen_id !== auth()->id()) {
            return back()->with('hata', 'Bu mesajı düzenleyemezsiniz.');
        }

        // Teklif mesajı düzenlenemez
        if ($mesaj->tip === 'teklif' || $mesaj->tip === 'sistem') {
            return back()->with('hata', 'Bu tip mesajlar düzenlenemez.');
        }

        $request->validate([
            'mesaj' => 'required|string|max:2000',
        ]);

        // Yasakli kelime kontrolu
        $mesajKontrol = YasakliKelime::metinGecerliMi($request->mesaj, 'mesaj');
        if (!$mesajKontrol['gecerli']) {
            return back()->withErrors(['mesaj' => $mesajKontrol['mesaj']]);
        }

        // Gelismis icerik kontrolu
        $icerikHatasi = $this->icerikKontrol($request->mesaj);
        if ($icerikHatasi) {
            return back()->withErrors(['mesaj' => $icerikHatasi]);
        }

        // Sansurlenecek kelimeler varsa sansurle
        $mesajMetni = YasakliKelime::metniSansurle($request->mesaj, 'mesaj');

        $mesaj->update([
            'mesaj' => $mesajMetni,
        ]);

        return redirect()->route('chat.show', $mesaj->konusma_id)->with('basarili', 'Mesaj güncellendi');
    }
}
