@extends('layouts.app')

@section('title', 'E-posta Tekrar Gönder - ' . config('app.name'))

@push('styles')
<style>
.resend-result {
    text-align: center;
    padding: 40px 20px;
    max-width: 500px;
    margin: 0 auto;
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.result-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.result-icon.success {
    color: var(--success);
}

.result-icon.error {
    color: var(--error);
}

.resend-result h2 {
    margin-bottom: 15px;
}

.resend-result p {
    color: var(--text-secondary);
    margin-bottom: 10px;
}

.email-info {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.resend-result .btn {
    margin-top: 15px;
}
</style>
@endpush

@section('content')
<div class="resend-result">
    @if($sonuc === 'başarılı')
        <div class="result-icon success">
            <i class="fas fa-envelope"></i>
        </div>
        <h2>E-posta Gönderildi!</h2>
        <div class="email-info">
            <p><strong>{{ $email }}</strong></p>
        </div>
        <p>{{ $mesaj }}</p>
        <p>Lütfen gelen kutunuzu kontrol edin.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Giriş Sayfasına Git
        </a>
    @else
        <div class="result-icon error">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h2>Gönderim Başarısız</h2>
        <p>{{ $mesaj }}</p>
        <a href="{{ route('resend-verification', ['email' => $email]) }}" class="btn btn-primary">
            <i class="fas fa-redo"></i> Tekrar Dene
        </a>
    @endif
</div>
@endsection
