<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpListesi;
use App\Models\YasakliKelime;
use App\Models\EmailDomainListesi;
use App\Models\KullaniciEngeli;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListManagementController extends Controller
{
    /**
     * Ana liste yönetim sayfası
     */
    public function index(): View
    {
        $ipSayisi = IpListesi::aktif()->count();
        $kelimeSayisi = YasakliKelime::aktif()->count();
        $domainSayisi = EmailDomainListesi::aktif()->count();
        $engelSayisi = KullaniciEngeli::count();

        return view('admin.lists.index', compact(
            'ipSayisi', 'kelimeSayisi', 'domainSayisi', 'engelSayisi'
        ));
    }

    // ==================== IP LİSTESİ ====================

    public function ipList(Request $request): View
    {
        $query = IpListesi::with('ekleyen')->latest();

        if ($request->filled('tip')) {
            $query->where('tip', $request->tip);
        }

        if ($request->filled('ara')) {
            $query->where('ip_adresi', 'like', '%' . $request->ara . '%');
        }

        $ipListesi = $query->paginate(20);

        return view('admin.lists.ip', compact('ipListesi'));
    }

    public function ipAdd(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ip_adresi' => 'required|string|max:45',
            'tip' => 'required|in:blacklist,whitelist',
            'sebep' => 'nullable|string|max:255',
            'bitis_tarihi' => 'nullable|date|after:now',
        ]);

        $validated['ekleyen_id'] = auth()->id();
        $validated['aktif'] = true;

        $ip = IpListesi::create($validated);

        AdminActivityLog::log('ip_listesi.created', $ip, null, $validated,
            'IP ' . $validated['tip'] . ' eklendi: ' . $validated['ip_adresi']);

        return back()->with('basarili', 'IP adresi listeye eklendi.');
    }

    public function ipDelete(IpListesi $ip): RedirectResponse
    {
        AdminActivityLog::log('ip_listesi.deleted', $ip, $ip->toArray(), null,
            'IP listeden silindi: ' . $ip->ip_adresi);

        $ip->delete();

        return back()->with('basarili', 'IP adresi listeden silindi.');
    }

    public function ipToggle(IpListesi $ip): RedirectResponse
    {
        $ip->update(['aktif' => !$ip->aktif]);

        return back()->with('basarili', 'IP durumu güncellendi.');
    }

    // ==================== YASAKLI KELİMELER ====================

    public function keywordList(Request $request): View
    {
        $query = YasakliKelime::latest();

        if ($request->filled('ara')) {
            $query->where('kelime', 'like', '%' . $request->ara . '%');
        }

        if ($request->filled('aksiyon')) {
            $query->where('aksiyon', $request->aksiyon);
        }

        $kelimeler = $query->paginate(20);

        return view('admin.lists.keywords', compact('kelimeler'));
    }

    public function keywordAdd(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelime' => 'required|string|max:100',
            'tip' => 'required|in:tam_eslesme,icerir',
            'uygulanacak_alanlar' => 'required|array|min:1',
            'uygulanacak_alanlar.*' => 'in:urun_adi,urun_aciklama,mesaj,kullanici_adi,yorum',
            'yerine' => 'nullable|string|max:100',
            'aksiyon' => 'required|in:engelle,sansurle,uyar',
        ]);

        $validated['aktif'] = true;

        $kelime = YasakliKelime::create($validated);

        AdminActivityLog::log('yasakli_kelime.created', $kelime, null, $validated,
            'Yasaklı kelime eklendi: ' . $validated['kelime']);

        return back()->with('basarili', 'Yasaklı kelime eklendi.');
    }

    public function keywordDelete(YasakliKelime $kelime): RedirectResponse
    {
        AdminActivityLog::log('yasakli_kelime.deleted', $kelime, $kelime->toArray(), null,
            'Yasaklı kelime silindi: ' . $kelime->kelime);

        $kelime->delete();

        return back()->with('basarili', 'Yasaklı kelime silindi.');
    }

    public function keywordToggle(YasakliKelime $kelime): RedirectResponse
    {
        $kelime->update(['aktif' => !$kelime->aktif]);

        return back()->with('basarili', 'Kelime durumu güncellendi.');
    }

    public function keywordEdit(YasakliKelime $kelime): View
    {
        return view('admin.lists.keyword-edit', compact('kelime'));
    }

    public function keywordUpdate(Request $request, YasakliKelime $kelime): RedirectResponse
    {
        $validated = $request->validate([
            'kelime' => 'required|string|max:100',
            'tip' => 'required|in:tam_eslesme,icerir',
            'uygulanacak_alanlar' => 'required|array|min:1',
            'uygulanacak_alanlar.*' => 'in:urun_adi,urun_aciklama,mesaj,kullanici_adi,yorum',
            'yerine' => 'nullable|string|max:100',
            'aksiyon' => 'required|in:engelle,sansurle,uyar',
        ]);

        $eskiDeger = $kelime->toArray();
        $kelime->update($validated);

        AdminActivityLog::log('yasakli_kelime.updated', $kelime, $eskiDeger, $validated,
            'Yasaklı kelime güncellendi: ' . $validated['kelime']);

        return redirect()->route('admin.lists.keywords')->with('basarili', 'Yasaklı kelime güncellendi.');
    }

    // ==================== E-POSTA DOMAİN LİSTESİ ====================

    public function domainList(Request $request): View
    {
        $query = EmailDomainListesi::latest();

        if ($request->filled('tip')) {
            $query->where('tip', $request->tip);
        }

        if ($request->filled('ara')) {
            $query->where('domain', 'like', '%' . $request->ara . '%');
        }

        $domainler = $query->paginate(20);

        return view('admin.lists.domains', compact('domainler'));
    }

    public function domainAdd(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
            'tip' => 'required|in:blacklist,whitelist',
            'sebep' => 'nullable|string|max:255',
        ]);

        // Domain'i küçük harfe çevir ve temizle
        $validated['domain'] = strtolower(trim($validated['domain']));
        $validated['aktif'] = true;

        // Zaten var mı kontrol et
        $var = EmailDomainListesi::where('domain', $validated['domain'])
            ->where('tip', $validated['tip'])
            ->exists();

        if ($var) {
            return back()->with('hata', 'Bu domain zaten listede mevcut.');
        }

        $domain = EmailDomainListesi::create($validated);

        AdminActivityLog::log('email_domain.created', $domain, null, $validated,
            'E-posta domain ' . $validated['tip'] . ' eklendi: ' . $validated['domain']);

        return back()->with('basarili', 'E-posta domain\'i listeye eklendi.');
    }

    public function domainDelete(EmailDomainListesi $domain): RedirectResponse
    {
        AdminActivityLog::log('email_domain.deleted', $domain, $domain->toArray(), null,
            'E-posta domain silindi: ' . $domain->domain);

        $domain->delete();

        return back()->with('basarili', 'E-posta domain\'i listeden silindi.');
    }

    public function domainToggle(EmailDomainListesi $domain): RedirectResponse
    {
        $domain->update(['aktif' => !$domain->aktif]);

        return back()->with('basarili', 'Domain durumu güncellendi.');
    }

    public function addTemporaryDomains(): RedirectResponse
    {
        $eklenen = EmailDomainListesi::geciciDomainleriEkle();

        if ($eklenen > 0) {
            return back()->with('basarili', $eklenen . ' geçici e-posta domain\'i blacklist\'e eklendi.');
        }

        return back()->with('bilgi', 'Tüm geçici domainler zaten listede mevcut.');
    }

    // ==================== KULLANICI ENGELLERİ ====================

    public function blockList(Request $request): View
    {
        $query = KullaniciEngeli::with(['engelleyen', 'engellenen'])->latest();

        if ($request->filled('ara')) {
            $ara = $request->ara;
            $query->whereHas('engelleyen', function ($q) use ($ara) {
                $q->where('ad', 'like', "%$ara%")
                  ->orWhere('soyad', 'like', "%$ara%")
                  ->orWhere('email', 'like', "%$ara%");
            })->orWhereHas('engellenen', function ($q) use ($ara) {
                $q->where('ad', 'like', "%$ara%")
                  ->orWhere('soyad', 'like', "%$ara%")
                  ->orWhere('email', 'like', "%$ara%");
            });
        }

        $engeller = $query->paginate(20);

        return view('admin.lists.blocks', compact('engeller'));
    }

    public function blockRemove(KullaniciEngeli $engel): RedirectResponse
    {
        AdminActivityLog::log('kullanici_engeli.deleted', $engel, $engel->toArray(), null,
            'Kullanıcı engeli kaldırıldı: ' . $engel->engelleyen->ad_soyad . ' -> ' . $engel->engellenen->ad_soyad);

        $engel->delete();

        return back()->with('basarili', 'Kullanıcı engeli kaldırıldı.');
    }
}
