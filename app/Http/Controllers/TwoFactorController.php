<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function index(): View
    {
        $user = auth()->user();

        return view('profile.two-factor', [
            'enabled' => $user->twoFactorEnabled(),
        ]);
    }

    public function enable(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->twoFactorEnabled()) {
            return redirect()->route('profile.2fa')->with('hata', '2FA zaten etkin.');
        }

        // Yeni secret olustur
        $secret = $this->google2fa->generateSecretKey();

        // Secret'i gecici olarak session'a kaydet
        session(['2fa_secret' => $secret]);

        // QR kod olustur
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // SVG QR kod olustur
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('profile.two-factor-enable', [
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Dogrulama kodu gerekli.',
            'code.size' => 'Kod 6 haneli olmali.',
        ]);

        $secret = session('2fa_secret');

        if (!$secret) {
            return redirect()->route('profile.2fa')->with('hata', 'Oturum suresi doldu. Tekrar deneyin.');
        }

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->with('hata', 'Gecersiz kod. Tekrar deneyin.');
        }

        // Backup kodlari olustur
        $backupCodes = $this->generateBackupCodes();

        // 2FA'yi etkinlestir
        $user = auth()->user();
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_backup_codes' => encrypt(json_encode($backupCodes)),
        ]);

        session()->forget('2fa_secret');

        // Backup kodlarini goster
        return redirect()->route('profile.2fa.backup')->with('backup_codes', $backupCodes);
    }

    public function showBackupCodes(): View|RedirectResponse
    {
        $backupCodes = session('backup_codes');

        if (!$backupCodes) {
            return redirect()->route('profile.2fa');
        }

        return view('profile.two-factor-backup', [
            'backupCodes' => $backupCodes,
        ]);
    }

    public function regenerateBackupCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Sifrenizi girin.',
            'password.current_password' => 'Sifre yanlis.',
        ]);

        $user = auth()->user();

        if (!$user->twoFactorEnabled()) {
            return redirect()->route('profile.2fa')->with('hata', '2FA etkin degil.');
        }

        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_backup_codes' => encrypt(json_encode($backupCodes)),
        ]);

        return redirect()->route('profile.2fa.backup')->with('backup_codes', $backupCodes);
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Sifrenizi girin.',
            'password.current_password' => 'Sifre yanlis.',
        ]);

        $user = auth()->user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'two_factor_backup_codes' => null,
        ]);

        return redirect()->route('profile.2fa')->with('basarili', 'Iki faktorlu dogrulama devre disi birakildi.');
    }

    public function verify(): View|RedirectResponse
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        // Kullanıcının 2FA'sı aktif mi kontrol et
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->two_factor_enabled || !$user->two_factor_secret) {
            // 2FA aktif değil, direkt giriş yap
            if ($user) {
                auth()->login($user, session('2fa_remember', false));
                session()->forget(['2fa_user_id', '2fa_remember']);
                session()->regenerate();
                return redirect()->intended(route('home'));
            }
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'code' => ['required', 'string'],
            ]);

            $userId = session('2fa_user_id');

            if (!$userId) {
                return redirect()->route('login')->with('hata', 'Oturum suresi doldu.');
            }

            $user = \App\Models\User::find($userId);

            if (!$user) {
                return redirect()->route('login')->with('hata', 'Kullanici bulunamadi.');
            }

            $code = $request->code;
            $isValid = false;

            // 2FA aktif değilse direkt login yap
            if (!$user->two_factor_enabled || !$user->two_factor_secret) {
                auth()->login($user, session('2fa_remember', false));
                session()->forget(['2fa_user_id', '2fa_remember']);
                session()->regenerate();
                return redirect()->intended(route('home'));
            }

            // Önce TOTP kodunu kontrol et (6 haneli)
            if (strlen($code) === 6 && ctype_digit($code)) {
                try {
                    $secret = decrypt($user->two_factor_secret);
                    $isValid = $this->google2fa->verifyKey($secret, $code);
                } catch (\Exception $e) {
                    \Log::error('2FA decrypt hatasi: ' . $e->getMessage());
                    // 2FA bozuksa sıfırla ve giriş yap
                    $user->update([
                        'two_factor_secret' => null,
                        'two_factor_enabled' => false,
                        'two_factor_confirmed_at' => null,
                        'two_factor_backup_codes' => null,
                    ]);
                    auth()->login($user, session('2fa_remember', false));
                    session()->forget(['2fa_user_id', '2fa_remember']);
                    session()->regenerate();
                    return redirect()->intended(route('home'))->with('uyari', '2FA ayarlarınız sıfırlandı. Lütfen yeniden kurun.');
                }
            }

            // TOTP gecersizse backup kodu kontrol et (8 haneli)
            if (!$isValid && strlen($code) === 8) {
                $isValid = $this->useBackupCode($user, $code);
            }

            if (!$isValid) {
                return back()->with('hata', 'Gecersiz kod. Tekrar deneyin.');
            }

            // Misafir sepetini aktar (hata olursa login'i engellemesin)
            try {
                $this->transferGuestCart($user->id);
            } catch (\Exception $e) {
                \Log::warning('Sepet transferi basarisiz: ' . $e->getMessage());
            }

            // Oturumu tamamla
            auth()->login($user, session('2fa_remember', false));
            session()->forget(['2fa_user_id', '2fa_remember']);
            session()->regenerate();

            // Satıcı kontrolü
            if (method_exists($user, 'saticiMi') && $user->saticiMi()) {
                return redirect()->intended(route('seller.dashboard'));
            }

            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            \Log::error('2FA verifyCode hatasi: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return redirect()->route('login')->with('hata', 'Bir hata olustu. Tekrar deneyin.');
        }
    }

    private function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }
        return $codes;
    }

    private function useBackupCode($user, string $code): bool
    {
        if (!$user->two_factor_backup_codes) {
            return false;
        }

        $backupCodes = json_decode(decrypt($user->two_factor_backup_codes), true);

        if (!is_array($backupCodes)) {
            return false;
        }

        $code = strtoupper($code);
        $index = array_search($code, $backupCodes);

        if ($index === false) {
            return false;
        }

        // Kullanilan kodu kaldir
        unset($backupCodes[$index]);
        $backupCodes = array_values($backupCodes);

        $user->update([
            'two_factor_backup_codes' => encrypt(json_encode($backupCodes)),
        ]);

        return true;
    }

    private function transferGuestCart(int $userId): void
    {
        // Misafir sepet verisi varsa temizle
        // Not: Sepet sistemi SepetItem modeli üzerinden çalışıyor
        // Misafir sepeti şu an desteklenmiyor
        session()->forget('sepet');
    }
}
