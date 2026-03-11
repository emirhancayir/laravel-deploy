<?php

namespace App\Http\Controllers;

use App\Models\Konusma;
use App\Models\Urun;
use App\Events\MesajGonderildi;
use App\Notifications\YeniMesajBildirimi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChatController extends Controller
{
    /**
     * Tum konusmalari listele (inbox)
     */
    public function index(): View
    {
        $konusmalar = Konusma::where(function ($query) {
                $query->where('alici_id', auth()->id())
                      ->orWhere('satici_id', auth()->id());
            })
            ->where('durum', 'aktif')
            ->with(['urun.resimler', 'alici', 'satici', 'sonMesaj', 'sonTeklif'])
            ->orderBy('son_mesaj_tarihi', 'desc')
            ->paginate(20);

        return view('chat.index', compact('konusmalar'));
    }

    /**
     * Konusma detay sayfasi (chat window)
     */
    public function show(Konusma $konusma): View
    {
        // Yetki kontrolu
        $this->authorize('view', $konusma);

        // Mesajlari okundu olarak isaretle
        $this->mesajlariOkunduIsaretle($konusma);

        // Mesajlari ve teklifleri yukle
        $konusma->load([
            'mesajlar.gonderen',
            'mesajlar.teklif',
            'urun.satici',
            'urun.resimler',
            'alici',
            'satici',
            'teklifler', // Tum teklifleri yukle (beklemede, kabul_edildi, reddedildi)
        ]);

        return view('chat.show', compact('konusma'));
    }

    /**
     * Yeni konusma baslat (urun sayfasindan "Mesaj Gonder" butonu)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'urun_id' => 'required|exists:urunler,id',
            'mesaj' => 'nullable|string|max:1000',
        ]);

        $urun = Urun::findOrFail($request->urun_id);

        // Kendi urunune mesaj gonderemez
        if ($urun->satici_id === auth()->id()) {
            return back()->with('hata', 'Kendi urunuz icin konusma baslatamazsiniz.');
        }

        // Mevcut konusma var mi kontrol et
        $konusma = Konusma::where('urun_id', $urun->id)
            ->where('alici_id', auth()->id())
            ->where('satici_id', $urun->satici_id)
            ->first();

        if (!$konusma) {
            $konusma = Konusma::create([
                'urun_id' => $urun->id,
                'alici_id' => auth()->id(),
                'satici_id' => $urun->satici_id,
                'son_mesaj_tarihi' => now(),
            ]);
        }

        // Eger mesaj varsa gonder
        if ($request->filled('mesaj')) {
            $mesaj = $konusma->mesajlar()->create([
                'gonderen_id' => auth()->id(),
                'mesaj' => $request->mesaj,
                'tip' => 'metin',
            ]);

            $konusma->update([
                'son_mesaj_tarihi' => now(),
                'okunmamis_satici' => $konusma->okunmamis_satici + 1,
            ]);

            // Saticiya bildirim gonder
            $satici = $urun->satici;
            if ($satici && $satici->id !== auth()->id()) {
                $satici->notify(new YeniMesajBildirimi($mesaj, auth()->user()));
            }

            // Broadcast event (skip if Reverb not running)
            try {
                broadcast(new MesajGonderildi($mesaj))->toOthers();
            } catch (\Exception $e) {
                // Continue without real-time
            }
        }

        return redirect()->route('chat.show', $konusma)
            ->with('basarili', 'Konusma baslatildi.');
    }

    /**
     * Arsivlenmis konusmalari listele
     */
    public function archived(): View
    {
        $konusmalar = Konusma::where(function ($query) {
                $query->where('alici_id', auth()->id())
                      ->orWhere('satici_id', auth()->id());
            })
            ->where('durum', 'arsivlendi')
            ->with(['urun.resimler', 'alici', 'satici', 'sonMesaj'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('chat.archived', compact('konusmalar'));
    }

    /**
     * Konusmayi arsivle
     */
    public function archive(Konusma $konusma): RedirectResponse
    {
        $this->authorize('update', $konusma);

        $konusma->update(['durum' => 'arsivlendi']);

        return redirect()->route('chat.index')
            ->with('basarili', 'Konusma arsivlendi.');
    }

    /**
     * Konusmayi arsivden cikar
     */
    public function unarchive(Konusma $konusma): RedirectResponse
    {
        $this->authorize('update', $konusma);

        $konusma->update(['durum' => 'aktif']);

        return redirect()->route('chat.archived')
            ->with('basarili', 'Konusma arsivden cikarildi.');
    }

    /**
     * Mesajlari okundu olarak isaretle
     */
    private function mesajlariOkunduIsaretle(Konusma $konusma): void
    {
        $user = auth()->user();
        $rol = $konusma->kullaniciRolu($user);

        if ($rol === 'alici') {
            $konusma->update(['okunmamis_alici' => 0]);
        } elseif ($rol === 'satici') {
            $konusma->update(['okunmamis_satici' => 0]);
        }

        // Mesajlari okundu isaretle
        $konusma->mesajlar()
            ->where('gonderen_id', '!=', $user->id)
            ->where('okundu', false)
            ->update([
                'okundu' => true,
                'okunma_tarihi' => now(),
            ]);

        // Bu konusmaya ait bildirimleri okundu isaretle
        $user->unreadNotifications()
            ->where('type', 'App\\Notifications\\YeniMesajBildirimi')
            ->get()
            ->filter(function ($notification) use ($konusma) {
                return isset($notification->data['konusma_id'])
                    && $notification->data['konusma_id'] == $konusma->id;
            })
            ->each(function ($notification) {
                $notification->markAsRead();
            });
    }

    /**
     * Sohbetteki tum mesajlari temizle
     */
    public function clear(Konusma $konusma)
    {
        $this->authorize('update', $konusma);

        // Sadece normal mesajları sil (teklif ve sistem mesajları kalacak)
        $konusma->mesajlar()
            ->where('tip', 'metin')
            ->delete();

        return redirect()->route('chat.show', $konusma)
            ->with('basarili', 'Sohbet temizlendi.');
    }
}
