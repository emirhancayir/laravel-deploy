<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Şifremi unuttum formu
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // E-posta gönder
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Bu e-posta adresi sistemde kayıtlı değil.',
        ]);

        $email = $request->email;
        $token = Str::random(64);

        // Eski tokenları sil
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Yeni token ekle
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Sıfırlama linki
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        // E-posta gönder
        $user = User::where('email', $email)->first();

        Mail::send('emails.password-reset', [
            'user' => $user,
            'resetLink' => $resetLink,
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Şifre Sıfırlama - ' . config('app.name'));
        });

        return back()->with('basarili', 'Şifre sıfırlama linki e-posta adresinize gönderildi.');
    }

    // Şifre sıfırlama formu
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Şifreyi güncelle
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
        ]);

        // Token kontrolü
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Geçersiz şifre sıfırlama isteği.']);
        }

        // Token süresi kontrolü (60 dakika)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Şifre sıfırlama linki süresi dolmuş. Lütfen tekrar deneyin.']);
        }

        // Token doğrulama
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Geçersiz şifre sıfırlama tokeni.']);
        }

        // Şifreyi güncelle
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Token'ı sil
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('basarili', 'Şifreniz başarıyla güncellendi. Giriş yapabilirsiniz.');
    }
}
