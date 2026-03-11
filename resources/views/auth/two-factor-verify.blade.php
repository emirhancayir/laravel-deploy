@extends('layouts.app')

@section('title', '2FA Dogrulama - ' . config('app.name'))

@push('styles')
<style>
.verify-container {
    max-width: 400px;
    margin: 50px auto;
}

.verify-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 40px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-align: center;
    color: var(--text);
}

.verify-icon {
    width: 80px;
    height: 80px;
    background: var(--info-bg, linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.verify-icon i {
    font-size: 2.5rem;
    color: var(--primary);
}

.verify-card h2 {
    margin-bottom: 10px;
    font-size: 1.4rem;
    color: var(--text);
}

.verify-card > p {
    color: var(--text-secondary);
    margin-bottom: 30px;
    font-size: 0.95rem;
}

.code-inputs {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 25px;
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

.help-text {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-top: 20px;
}

.help-text a {
    color: var(--primary);
}

.backup-section {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.backup-toggle {
    color: var(--primary);
    cursor: pointer;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.backup-toggle:hover {
    text-decoration: underline;
}

.backup-input-wrapper {
    display: none;
    margin-top: 15px;
}

.backup-input-wrapper.active {
    display: block;
}

.backup-input-wrapper input {
    width: 100%;
    padding: 15px;
    font-size: 1.2rem;
    text-align: center;
    letter-spacing: 3px;
    text-transform: uppercase;
    border: 2px solid var(--border);
    border-radius: 8px;
    background: var(--input-bg, #fff);
    color: var(--text);
}

.backup-input-wrapper input:focus {
    border-color: var(--primary);
    outline: none;
}

.totp-section.hidden {
    display: none;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h2>2FA Dogrulama</h2>
            <p>Authenticator uygulamanizdan 6 haneli kodu veya 8 haneli yedek kodu girin</p>

            @if(session('hata'))
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    {{ session('hata') }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.code') }}" id="verifyForm">
                @csrf

                <!-- TOTP Kod Girişi -->
                <div class="totp-section" id="totpSection">
                    <div class="code-inputs">
                        <input type="text" maxlength="1" class="code-digit" data-index="0" autofocus>
                        <input type="text" maxlength="1" class="code-digit" data-index="1">
                        <input type="text" maxlength="1" class="code-digit" data-index="2">
                        <input type="text" maxlength="1" class="code-digit" data-index="3">
                        <input type="text" maxlength="1" class="code-digit" data-index="4">
                        <input type="text" maxlength="1" class="code-digit" data-index="5">
                    </div>
                </div>

                <!-- Yedek Kod Girişi -->
                <div class="backup-input-wrapper" id="backupSection">
                    <input type="text" id="backupCodeInput" maxlength="8" placeholder="8 haneli yedek kod" autocomplete="off">
                </div>

                <input type="hidden" name="code" id="fullCode">

                @error('code')
                    <div class="alert alert-error" style="margin-bottom: 15px;">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-check"></i> Dogrula
                </button>
            </form>

            <div class="backup-section">
                <span class="backup-toggle" id="toggleBackup">
                    <i class="fas fa-key"></i> Yedek kod kullan
                </span>
            </div>

            <p class="help-text">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Farkli bir hesapla giris yap</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-digit');
    const fullCodeInput = document.getElementById('fullCode');
    const form = document.getElementById('verifyForm');
    const totpSection = document.getElementById('totpSection');
    const backupSection = document.getElementById('backupSection');
    const backupCodeInput = document.getElementById('backupCodeInput');
    const toggleBackup = document.getElementById('toggleBackup');

    let isBackupMode = false;

    // Toggle between TOTP and Backup code
    toggleBackup.addEventListener('click', function() {
        isBackupMode = !isBackupMode;

        if (isBackupMode) {
            totpSection.classList.add('hidden');
            backupSection.classList.add('active');
            toggleBackup.innerHTML = '<i class="fas fa-mobile-alt"></i> Authenticator kodu kullan';
            backupCodeInput.focus();
            // Clear TOTP inputs
            inputs.forEach(input => input.value = '');
        } else {
            totpSection.classList.remove('hidden');
            backupSection.classList.remove('active');
            toggleBackup.innerHTML = '<i class="fas fa-key"></i> Yedek kod kullan';
            inputs[0].focus();
            // Clear backup input
            backupCodeInput.value = '';
        }
        fullCodeInput.value = '';
    });

    // Backup code input handler
    backupCodeInput.addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        fullCodeInput.value = this.value;
    });

    backupCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.length === 8) {
            form.submit();
        }
    });

    // TOTP code handlers
    function updateFullCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        fullCodeInput.value = code;

        // Auto submit when all 6 digits entered
        if (code.length === 6) {
            setTimeout(() => form.submit(), 300);
        }
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
        });
    });
});
</script>
@endsection
