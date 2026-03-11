@extends('layouts.app')

@section('title', '2FA Etkinlestir - ' . config('app.name'))

@push('styles')
<style>
/* Dark Mode Düzeltmeleri */
[data-theme="dark"] .setup-card {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .setup-card h2,
[data-theme="dark"] .setup-card p,
[data-theme="dark"] .setup-card label {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .qr-container {
    background: #252541 !important;
}

[data-theme="dark"] .secret-key {
    background: #252541 !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .secret-key label {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .secret-key code {
    color: #ffa726 !important;
}

[data-theme="dark"] .code-inputs input {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .code-inputs input:focus {
    border-color: #ff9900 !important;
}

[data-theme="dark"] .apps-info {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .apps-info strong {
    color: #e0e0e0 !important;
}

[data-theme="dark"] [style*="color:black"],
[data-theme="dark"] [style*="color: black"] {
    color: #e0e0e0 !important;
}

.setup-container {
    max-width: 500px;
    margin: 0 auto;
}

.setup-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    text-align: center;
    color: var(--text);
}

.setup-card h2 {
    margin-bottom: 10px;
    color: var(--text);
}

.setup-card > p {
    color: var(--text-secondary);
    margin-bottom: 25px;
}

.qr-container {
    background: var(--bg-secondary, #f8f9fa);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
}

.qr-code {
    display: inline-block;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    margin-bottom: 15px;
}

.qr-code svg {
    display: block;
}

.secret-key {
    background: var(--card-bg, #fff);
    border: 1px dashed var(--border);
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.secret-key label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: 8px;
}

.secret-key code {
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: 2px;
    color: var(--primary);
    word-break: break-all;
}

.verify-form {
    text-align: left;
}

.verify-form label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
    color: var(--text);
}

.code-inputs {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 20px;
}

.code-inputs input {
    width: 45px;
    height: 55px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    border: 2px solid var(--border);
    border-radius: 8px;
    background: var(--input-bg, #fff);
    color: var(--text);
}

.code-inputs input:focus {
    border-color: var(--primary);
    outline: none;
}

.apps-info {
    background: var(--info-bg, #e3f2fd);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: var(--text);
}

.apps-info strong {
    display: block;
    margin-bottom: 5px;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="setup-container">
        <div class="setup-card">
            <h2><i class="fas fa-qrcode"></i> 2FA Kurulumu</h2>
            <p>Asağıdaki QR kodu authenticator uygulamanızla tarayın.</p>

            @if(session('hata'))
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    {{ session('hata') }}
                </div>
            @endif

            <div class="apps-info" style="color:black;">
                <strong><i class="fas fa-mobile-alt"></i> Önerilen Uygulamalar:</strong>
                Google Authenticator, Microsoft Authenticator, Authy
            </div>

            <div class="qr-container">
                <div class="qr-code">
                    {!! $qrCodeSvg !!}
                </div>

                <div class="secret-key">
                    <label>QR kod calışmazsa bu kodu manuel girin:</label>
                    <code>{{ $secret }}</code>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.2fa.confirm') }}" class="verify-form">
                @csrf
                <label>Uygulamadaki 6 haneli kodu girin:</label>

                <div class="code-inputs">
                    <input type="text" maxlength="1" class="code-digit" data-index="0" autofocus>
                    <input type="text" maxlength="1" class="code-digit" data-index="1">
                    <input type="text" maxlength="1" class="code-digit" data-index="2">
                    <input type="text" maxlength="1" class="code-digit" data-index="3">
                    <input type="text" maxlength="1" class="code-digit" data-index="4">
                    <input type="text" maxlength="1" class="code-digit" data-index="5">
                </div>
                <input type="hidden" name="code" id="fullCode">

                @error('code')
                    <div class="alert alert-error" style="margin-bottom: 15px;">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-check"></i> Doğrula ve Etkinleştir
                </button>
            </form>

            <div style="margin-top: 20px;">
                <a href="{{ route('profile.2fa') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-digit');
    const fullCodeInput = document.getElementById('fullCode');

    function updateFullCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        fullCodeInput.value = code;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            if (value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateFullCode();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('keypress', (e) => {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pastedData.split('').forEach((char, i) => {
                if (inputs[i]) inputs[i].value = char;
            });
            updateFullCode();
            if (pastedData.length === 6) {
                inputs[5].focus();
            }
        });
    });
});
</script>
@endsection
