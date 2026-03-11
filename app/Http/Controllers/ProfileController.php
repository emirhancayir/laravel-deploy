<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $kullanici = auth()->user();
        return view('profile.index', compact('kullanici'));
    }

    public function update(Request $request): RedirectResponse
    {
        $kullanici = auth()->user();

        $validated = $request->validate([
            'ad' => ['required', 'string', 'max:100'],
            'soyad' => ['required', 'string', 'max:100'],
            'telefon' => ['nullable', 'string', 'max:20'],
            'adres' => ['nullable', 'string', 'max:500'],
            'yeni_sifre' => ['nullable', 'string', 'min:6', 'confirmed'],
            'profil_resmi' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ], [
            'ad.required' => 'Ad alanı zorunludur.',
            'soyad.required' => 'Soyad alanı zorunludur.',
            'yeni_sifre.min' => 'Yeni şifre en az 6 karakter olmalıdır.',
            'yeni_sifre.confirmed' => 'Yeni şifreler eşleşmiyor.',
            'profil_resmi.image' => 'Profil resmi bir görüntü dosyası olmalıdır.',
            'profil_resmi.mimes' => 'Sadece JPG, PNG, GIF ve WEBP formatları desteklenir.',
            'profil_resmi.max' => 'Profil resmi en fazla 5MB olabilir.',
        ]);

        $data = [
            'ad' => $validated['ad'],
            'soyad' => $validated['soyad'],
            'telefon' => $validated['telefon'] ?? null,
            'adres' => $validated['adres'] ?? null,
        ];

        // Profil resmi yükleme
        if ($request->hasFile('profil_resmi')) {
            $resim = $request->file('profil_resmi');

            // Eski resmi sil (hata olursa yoksay)
            if ($kullanici->profil_resmi) {
                try {
                    Storage::disk('public')->delete('profil/' . $kullanici->profil_resmi);
                } catch (\Exception $e) {
                    // Silme hatası yoksayılır
                }
            }

            // Yeni resmi kaydet
            $resimAdi = 'profil_' . $kullanici->id . '_' . time() . '.' . $resim->getClientOriginalExtension();

            // storage/app/public/profil klasörünü kullan (Windows hosting uyumlu)
            $profilDir = storage_path('app/public/profil');
            if (!is_dir($profilDir)) {
                mkdir($profilDir, 0755, true);
            }

            $resim->move($profilDir, $resimAdi);

            $data['profil_resmi'] = $resimAdi;
        }

        // Şifre değişikliği
        if (!empty($validated['yeni_sifre'])) {
            $data['password'] = Hash::make($validated['yeni_sifre']);
        }

        $kullanici->update($data);

        return back()->with('basarili', 'Profil bilgileriniz güncellendi.');
    }
}
