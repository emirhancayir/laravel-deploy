@extends('layouts.app')

@section('title', 'Yedek Kodlar - ' . config('app.name'))

@push('styles')
<style>
/* Dark Mode Düzeltmeleri */
[data-theme="dark"] .backup-card {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .backup-icon {
    background: #1e4235 !important;
}

[data-theme="dark"] .backup-card h2,
[data-theme="dark"] .backup-card p {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .backup-code {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .warning-box {
    background: #4a3520 !important;
    border-color: #f59e0b !important;
}

[data-theme="dark"] .warning-box h4 {
    color: #f59e0b !important;
}

[data-theme="dark"] .warning-box ul,
[data-theme="dark"] .warning-box li {
    color: #e0e0e0 !important;
}

.backup-container {
    max-width: 500px;
    margin: 0 auto;
}

.backup-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    text-align: center;
    color: var(--text);
}

.backup-icon {
    width: 80px;
    height: 80px;
    background: var(--success-bg, linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.backup-icon i {
    font-size: 2.5rem;
    color: var(--success, #2e7d32);
}

.backup-card h2 {
    margin-bottom: 10px;
    color: var(--text);
}

.backup-card > p {
    color: var(--text-secondary);
    margin-bottom: 25px;
}

.backup-codes {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 25px;
}

.backup-code {
    background: var(--bg-secondary, #f8f9fa);
    border: 1px dashed var(--border);
    border-radius: 8px;
    padding: 12px;
    font-family: monospace;
    font-size: 1.1rem;
    font-weight: bold;
    letter-spacing: 1px;
    color: var(--text);
}

.warning-box {
    background: var(--warning-bg, #fff3e0);
    border: 1px solid var(--warning, #ff9800);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: left;
}

.warning-box h4 {
    color: var(--warning, #e65100);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.warning-box ul {
    margin: 0;
    padding-left: 20px;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.warning-box li {
    margin-bottom: 5px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.print-btn {
    background: var(--bg-secondary, #f5f5f5);
    border: 1px solid var(--border);
    color: var(--text);
}

@media print {
    .no-print {
        display: none !important;
    }
    .backup-card {
        box-shadow: none;
    }
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="backup-container">
        <div class="backup-card">
            <div class="backup-icon">
                <i class="fas fa-key"></i>
            </div>

            <h2>Yedek Kodlariniz</h2>
            <p>Bu kodlari güvenli bir yerde saklayin. Her kod sadece bir kez kullanilabilir.</p>

            <div class="warning-box no-print">
                <h4><i class="fas fa-exclamation-triangle"></i> Onemli!</h4>
                <ul>
                    <li>Bu kodlari simdi kaydedin - tekrar gosterilmeyecek</li>
                    <li>Authenticator uygulamaniza erisemezseniz bu kodlari kullanin</li>
                    <li>Her kod sadece bir kez kullanilabilir</li>
                    <li>Kodlar biterse yenilerini oluşturabilirsiniz</li>
                </ul>
            </div>

            <div class="backup-codes">
                @foreach($backupCodes as $code)
                    <div class="backup-code">{{ $code }}</div>
                @endforeach
            </div>

            <div class="action-buttons no-print">
                <button onclick="window.print()" class="btn print-btn">
                    <i class="fas fa-print"></i> Yazdir
                </button>
                <button onclick="copyBackupCodes()" class="btn btn-secondary">
                    <i class="fas fa-copy"></i> Kopyala
                </button>
                <a href="{{ route('profile.2fa') }}" class="btn btn-primary">
                    <i class="fas fa-check"></i> Tamamla
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyBackupCodes() {
    const codes = @json($backupCodes);
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        showToast('Kodlar panoya kopyalandı!', 'success');
    });
}
</script>
@endsection
