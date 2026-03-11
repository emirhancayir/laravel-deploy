<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\IpLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): View|RedirectResponse
    {
        // IP bazli gunluk kayit limiti kontrolu (Bot korumasi)
        $ip = $request->ip();
        $gunlukLimit = config('zamason.gunluk_kayit_limiti', 1);

        $bugunKayitSayisi = IpLog::where('ip_address', $ip)
            ->where('action', 'registration')
            ->whereDate('created_at', today())
            ->count();

        if ($bugunKayitSayisi >= $gunlukLimit) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Bu IP adresinden bugun zaten bir hesap olusturulmus. Lutfen yarin tekrar deneyin.']);
        }

        $validated = $request->validate([
            'ad' => ['required', 'string', 'max:100'],
            'soyad' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'telefon' => ['nullable', 'string', 'max:20'],
            'kvkk' => ['accepted'],
            'kullanim_sartlari' => ['accepted'],
        ], [
            'ad.required' => 'Ad zorunludur.',
            'soyad.required' => 'Soyad zorunludur.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre zorunludur.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'kvkk.accepted' => 'KVKK Aydınlatma Metni\'ni kabul etmelisiniz.',
            'kullanim_sartlari.accepted' => 'Kullanım Şartları\'nı kabul etmelisiniz.',
        ]);

        // E-posta domain blacklist kontrolu
        $emailKontrol = \App\Models\EmailDomainListesi::emailKontrol($validated['email']);
        if (!$emailKontrol['izinli']) {
            return back()
                ->withInput()
                ->withErrors(['email' => $emailKontrol['sebep']]);
        }

        // Doğrulama token'ı oluştur
        $verificationToken = Str::random(64);
        $tokenExpiry = now()->addHours(24);

        $user = User::create([
            'ad' => $validated['ad'],
            'soyad' => $validated['soyad'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'telefon' => $validated['telefon'],
            'kullanici_tipi' => 'alici',
            'email_verified' => 0,
            'verification_token' => $verificationToken,
            'token_expiry' => $tokenExpiry,
            'registration_ip' => $ip,
        ]);

        // IP log kaydet
        IpLog::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'action' => 'registration',
            'user_agent' => $request->userAgent(),
        ]);

        // Doğrulama e-postası gönder
        try {
            Mail::to($user->email)->send(new EmailVerification($user, $verificationToken));
        } catch (\Exception $e) {
            \Log::error('Mail gonderilemedi: ' . $e->getMessage());
            $user->delete();
            return back()
                ->withInput()
                ->withErrors(['email' => 'Doğrulama e-postası gönderilemedi. Lütfen tekrar deneyin.']);
        }

        // Kayıt başarılı - doğrulama sayfasına yönlendir
        return view('auth.verification-pending', ['email' => $user->email]);
    }
}
