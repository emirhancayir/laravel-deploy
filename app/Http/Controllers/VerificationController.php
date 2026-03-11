<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function verify(string $token): View
    {
        $sonuc = 'hata';
        $mesaj = '';

        if (empty($token)) {
            $sonuc = 'hata';
            $mesaj = 'Geçersiz doğrulama linki.';
        } else {
            $user = User::where('verification_token', $token)->first();

            if (!$user) {
                $sonuc = 'hata';
                $mesaj = 'Geçersiz veya süresi dolmuş doğrulama linki.';
            } elseif ($user->email_verified == 1) {
                $sonuc = 'zaten_dogrulandi';
                $mesaj = 'Bu e-posta adresi zaten doğrulanmış.';
            } elseif ($user->token_expiry && now()->isAfter($user->token_expiry)) {
                $sonuc = 'suresi_doldu';
                $mesaj = 'Doğrulama linkinin süresi dolmuş. Lütfen yeni bir doğrulama e-postası isteyin.';
            } else {
                // E-postayı doğrula
                $user->update([
                    'email_verified' => 1,
                    'verification_token' => null,
                    'token_expiry' => null,
                ]);

                $sonuc = 'basarili';
                $mesaj = 'E-posta adresiniz başarıyla doğrulandı!';
            }
        }

        return view('auth.verify', compact('sonuc', 'mesaj'));
    }

    public function resend(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('login')
                ->with('hata', 'Geçersiz e-posta adresi.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('hata', 'Bu e-posta adresi kayıtlı değil.');
        }

        if ($user->email_verified == 1) {
            return redirect()->route('login')
                ->with('basarili', 'E-posta adresiniz zaten doğrulanmış. Giriş yapabilirsiniz.');
        }

        // Yeni token oluştur
        $verificationToken = Str::random(64);
        $tokenExpiry = now()->addHours(24);

        $user->update([
            'verification_token' => $verificationToken,
            'token_expiry' => $tokenExpiry,
        ]);

        // E-posta gönder
        try {
            Mail::to($user->email)->send(new EmailVerification($user, $verificationToken));
        } catch (\Exception $e) {
            return view('auth.resend-verification', [
                'email' => $email,
                'sonuc' => 'hata',
                'mesaj' => 'E-posta gönderilemedi. Lütfen daha sonra tekrar deneyin.',
            ]);
        }

        return view('auth.resend-verification', [
            'email' => $email,
            'sonuc' => 'basarili',
            'mesaj' => 'Doğrulama e-postası tekrar gönderildi.',
        ]);
    }
}
