@extends('layouts.app')

@section('title', 'E-posta Doğrulama - ' . config('app.name'))

@push('styles')
<style>
.verification-result {
    text-align: center;
    padding: 40px 20px;
    max-width: 500px;
    margin: 0 auto;
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.result-icon {
    font-size: 5rem;
    margin-bottom: 20px;
}

.result-icon.success {
    color: var(--success);
}

.result-icon.error {
    color: var(--error);
}

.result-icon.warning {
    color: var(--warning);
}

.result-icon.info {
    color: var(--primary);
}

.verification-result h2 {
    margin-bottom: 15px;
}

.verification-result p {
    color: var(--text-secondary);
    margin-bottom: 10px;
}

.verification-result .btn {
    margin-top: 20px;
}
</style>
@endpush

@section('content')
<div class="verification-result">
    @if($sonuc === 'basarili')
        <div class="result-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Doğrulama Başarılı!</h2>
        <p>E-posta adresiniz başarıyla doğrulandı.</p>
        <p>Artık hesabınıza giriş yapabilirsiniz.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Giriş Yap
        </a>

    @elseif($sonuc === 'zaten_dogrulandi')
        <div class="result-icon info">
            <i class="fas fa-info-circle"></i>
        </div>
        <h2>Zaten Doğrulanmış</h2>
        <p>{{ $mesaj }}</p>
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Giriş Yap
        </a>

    @elseif($sonuc === 'suresi_doldu')
        <div class="result-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <h2>Link Süresi Doldu</h2>
        <p>{{ $mesaj }}</p>
        <a href="{{ route('register') }}" class="btn btn-primary">
            <i class="fas fa-envelope"></i> Yeniden Kayıt Ol
        </a>

    @else
        <div class="result-icon error">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2>Doğrulama Başarısız</h2>
        <p>{{ $mesaj }}</p>
        <a href="{{ route('register') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Yeni Kayıt Ol
        </a>
    @endif
</div>
@endsection
