@extends('layouts.app')

@section('title', 'Iki Faktorlu Dogrulama - ' . config('app.name'))

@push('styles')
<style>
/* Dark Mode Düzeltmeleri */
[data-theme="dark"] .twofa-card {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .twofa-header h2,
[data-theme="dark"] .twofa-header p {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .twofa-status-text h3,
[data-theme="dark"] .twofa-status-text p {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .twofa-info {
    background: #252541 !important;
}

[data-theme="dark"] .twofa-info h4,
[data-theme="dark"] .twofa-info li {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .backup-codes-section {
    background: #1e3a5a !important;
}

[data-theme="dark"] .backup-codes-section h4,
[data-theme="dark"] .backup-codes-section p {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .disable-form {
    background: #4a1e1e !important;
}

[data-theme="dark"] .disable-form h4 {
    color: #ef4444 !important;
}

[data-theme="dark"] .disable-form p,
[data-theme="dark"] .disable-form label {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .twofa-status.enabled {
    background: #1e4235 !important;
    border-color: #10b981 !important;
}

[data-theme="dark"] .twofa-status.disabled {
    background: #4a3520 !important;
    border-color: #f59e0b !important;
}

/* Inline Style Override */
[data-theme="dark"] [style*="color:black"],
[data-theme="dark"] [style*="color: black"] {
    color: #e0e0e0 !important;
}

[data-theme="dark"] [style*="text-color:black"],
[data-theme="dark"] [style*="text-color: black"] {
    color: #e0e0e0 !important;
}

[data-theme="dark"] ul,
[data-theme="dark"] li {
    color: #e0e0e0 !important;
}

[data-theme="dark"] label {
    color: #e0e0e0 !important;
}

.twofa-container {
    max-width: 600px;
    margin: 0 auto;
}

.twofa-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    color: var(--text);
}

.twofa-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}

.twofa-header i {
    font-size: 2.5rem;
    color: var(--primary);
}

.twofa-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: var(--text);
}

.twofa-header p {
    margin: 5px 0 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.twofa-status {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.twofa-status.enabled {
    background: var(--success-bg, #e8f5e9);
    border: 1px solid var(--success, #4caf50);
}

.twofa-status.disabled {
    background: var(--warning-bg, #fff3e0);
    border: 1px solid var(--warning, #ff9800);
}

.twofa-status i {
    font-size: 2rem;
}

.twofa-status.enabled i {
    color: var(--success, #2e7d32);
}

.twofa-status.disabled i {
    color: var(--warning, #e65100);
}

.twofa-status-text h3 {
    margin: 0 0 5px;
    font-size: 1.1rem;
    color: var(--text);
    color:black;
}

.twofa-status-text p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
    color:black;
}

.twofa-info {
    background: var(--bg-secondary, #f8f9fa);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    color:black;
}

.twofa-info h4 {
    margin: 0 0 10px;
    font-size: 1rem;
    color: var(--text);
    color:black;
}

.twofa-info ul {
    margin: 0;
    padding-left: 20px;
    color:black;
}

.twofa-info li {
    margin-bottom: 8px;
    color: var(--text-secondary);
    font-size: 0.9rem;
    color:black;
}

.disable-form {
    background: var(--danger-bg, #ffebee);
    border-radius: 8px;
    padding: 20px;
}

.disable-form h4 {
    margin: 0 0 15px;
    color: var(--danger, #c62828);
}

.backup-codes-section {
    background: var(--info-bg, #e3f2fd);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.backup-codes-section h4 {
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text);
}

.backup-codes-section p {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 15px;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="twofa-container">
        <div class="twofa-card">
            <div class="twofa-header">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <h2>İki Faktörlu Doğrulama (2FA)</h2>
                    <p>Hesabınızı daha güvenli hale getirin</p>
                </div>
            </div>

            @if(session('başarılı'))
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> {{ session('başarılı') }}
                </div>
            @endif

            @if(session('hata'))
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('hata') }}
                </div>
            @endif

            @if($enabled)
                <div class="twofa-status enabled">
                    <i class="fas fa-check-circle"></i>
                    <div class="twofa-status-text">
                        <h3>2FA Etkin</h3>
                        <p>Hesabınız iki faktörlu doğrulama ile korunuyor.</p>
                    </div>
                </div>

                <!-- Yedek Kodlar -->
                <div class="backup-codes-section">
                    <h4>
                        <i class="fas fa-key" style="color: var(--info, #1976d2);"></i> Yedek Kodlar
                    </h4>
                    <p>
                        Authenticator uygulamanıza erişemezseniz yedek kodlarla giriş yapabilirsiniz.
                    </p>
                    <form method="POST" action="{{ route('profile.2fa.backup.regenerate') }}" id="yedekKodForm" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @csrf
                        <input type="password" name="password" placeholder="Şifreniz" required style="flex: 1; min-width: 150px;">
                        <button type="button" class="btn btn-secondary" onclick="yeniKodlarOlustur()">
                            <i class="fas fa-sync"></i> Yeni Kodlar Oluştur
                        </button>
                    </form>
                </div>

                <div class="disable-form">
                    <h4><i class="fas fa-exclamation-triangle"></i> 2FA'yı Devre Dışı Bırak</h4>
                    <p style="margin-bottom: 15px; font-size: 0.9rem;">2FA'yı devre dışı bırakmak hesabınızın güvenliğini azaltır.</p>
                    <form method="POST" action="{{ route('profile.2fa.disable') }}" id="twoFaDisableForm">
                        @csrf
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="password">Şifrenizi Onaylayın</label>
                            <input type="password" id="password" name="password" required>
                            @error('password')
                                <small style="color: #c62828;">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="twoFaDevreDisi()">
                            <i class="fas fa-times"></i> Devre Dışı Bırak
                        </button>
                    </form>
                </div>
            @else
                <div class="twofa-status disabled" style="color:black;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="twofa-status-text">
                        <h3 style="text-color:black;">2FA Devre Dışı</h3>
                        <p>Hesabınız ek güvenlik katmanı olmadan korunuyor.</p>
                    </div>
                </div>

                <div class="twofa-info">
                    <h4><i class="fas fa-info-circle"></i> Nasıl Çalışır?</h4>
                    <ul style="text-color:black;">
                        <li>Google Authenticator veya benzeri bir uygulama indirin</li>
                        <li>QR kodu tarayın veya kodu manuel girin</li>
                        <li>Her girişte uygulamadaki 6 haneli kodu girin</li>
                        <li>Hesabınız artık daha güvenli!</li>
                    </ul>
                </div>

                <a href="{{ route('profile.2fa.enable') }}" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-shield-alt"></i> 2FA'yi Etkinlestir
                </a>
            @endif

            <div style="margin-top: 25px; text-align: center;">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Profile Dön
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
async function yeniKodlarOlustur() {
    const confirmed = await showConfirm({
        type: 'warning',
        title: 'Yeni Kodlar Oluştur',
        message: 'Mevcut kodlar geçersiz olacak. Devam etmek istiyor musunuz?',
        confirmText: 'Evet, Oluştur',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('yedekKodForm').submit();
    }
}

async function twoFaDevreDisi() {
    const confirmed = await showConfirm({
        type: 'danger',
        title: '2FA Devre Dışı Bırak',
        message: '2FA\'yı devre dışı bırakmak istediğinizden emin misiniz? Bu, hesabınızın güvenliğini azaltır.',
        confirmText: 'Evet, Devre Dışı Bırak',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('twoFaDisableForm').submit();
    }
}
</script>
@endpush
@endsection
