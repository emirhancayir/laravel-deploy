<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sepet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'password.required' => 'Şifre zorunludur.',
        ]);

        // Email doğrulanmış mı kontrol et
        $user = User::where('email', $credentials['email'])->first();
        if ($user && !$user->emailDogrulandiMi()) {
            return back()->withErrors([
                'email' => 'E-posta adresiniz henüz doğrulanmamış. <a href="' . route('resend-verification', ['email' => $user->email]) . '">Doğrulama mailini tekrar gönder</a>',
            ])->onlyInput('email');
        }

        // Kimlik bilgilerini kontrol et (henuz giris yapma)
        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();

            // 2FA etkin mi kontrol et
            if ($user->twoFactorEnabled()) {
                // 2FA dogrulama sayfasina yonlendir
                session([
                    '2fa_user_id' => $user->id,
                    '2fa_remember' => $request->boolean('remember'),
                ]);
                return redirect()->route('2fa.verify');
            }

            // 2FA yok, normal giris yap
            Auth::login($user, $request->boolean('remember'));

            // Misafir sepetini kullanici hesabina aktar
            $this->transferGuestCart($user->id);

            $request->session()->regenerate();

            if ($user->saticiMi()) {
                return redirect()->intended(route('seller.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'E-posta veya şifre hatalı.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function transferGuestCart(int $userId): void
    {
        $misafirSepet = session('sepet', []);
        if (!empty($misafirSepet)) {
            foreach ($misafirSepet as $urunId => $miktar) {
                $mevcutItem = Sepet::where('kullanici_id', $userId)
                    ->where('urun_id', $urunId)
                    ->first();

                if ($mevcutItem) {
                    $mevcutItem->increment('miktar', $miktar);
                } else {
                    Sepet::create([
                        'kullanici_id' => $userId,
                        'urun_id' => $urunId,
                        'miktar' => $miktar,
                    ]);
                }
            }
            session()->forget('sepet');
        }
    }
}
