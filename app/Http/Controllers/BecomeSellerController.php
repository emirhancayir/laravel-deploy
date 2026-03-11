<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BecomeSellerController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // Zaten satıcı ise satıcı paneline yönlendir
        if ($user->saticiMi()) {
            return redirect()->route('seller.dashboard')
                ->with('basarili', 'Zaten satıcı hesabınız bulunmaktadır.');
        }

        // Aktif kategorileri komisyon oranlarıyla birlikte getir
        $kategoriler = Kategori::where('aktif', true)
            ->orderBy('kategori_adi')
            ->get();

        return view('become-seller', compact('kategoriler'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Zaten satıcı ise satıcı paneline yönlendir
        if ($user->saticiMi()) {
            return redirect()->route('seller.dashboard')
                ->with('basarili', 'Zaten satıcı hesabınız bulunmaktadır.');
        }

        // IBAN'ı normalize et (boşlukları kaldır, büyük harfe çevir)
        $iban = strtoupper(str_replace(' ', '', $request->input('iban', '')));
        $request->merge(['iban' => $iban]);

        $validated = $request->validate([
            'firma_adi' => ['required', 'string', 'max:255'],
            'vergi_no' => ['nullable', 'string', 'max:50'],
            'telefon' => ['required', 'string', 'max:20'],
            'adres' => ['required', 'string', 'max:500'],
            'iban' => ['required', 'string', 'regex:/^TR\d{24}$/'],
            'sozlesme' => ['accepted'],
            'kvkk' => ['accepted'],
            'komisyon' => ['accepted'],
        ], [
            'firma_adi.required' => 'Firma/Marka adı zorunludur.',
            'telefon.required' => 'Telefon numarası zorunludur.',
            'adres.required' => 'Adres zorunludur.',
            'iban.required' => 'IBAN numarası zorunludur.',
            'iban.regex' => 'Geçerli bir IBAN numarası girin (TR ile başlamalı, 26 karakter). Örnek: TR330006100519786457841326',
            'sozlesme.accepted' => 'Satıcı sözleşmesini kabul etmelisiniz.',
            'kvkk.accepted' => 'KVKK aydınlatma metnini kabul etmelisiniz.',
            'komisyon.accepted' => 'Komisyon oranlarını kabul etmelisiniz.',
        ]);

        // Kullanıcıyı satıcı yap
        $user->update([
            'kullanici_tipi' => 'satici',
            'firma_adi' => $validated['firma_adi'],
            'vergi_no' => $validated['vergi_no'],
            'telefon' => $validated['telefon'],
            'adres' => $validated['adres'],
            'iban' => $validated['iban'],
            'satici_onay_tarihi' => now(),
        ]);

        return redirect()->route('seller.dashboard')
            ->with('basarili', 'Tebrikler! Artık bir satıcısınız. Hemen ürün eklemeye başlayabilirsiniz.');
    }
}
