@extends('layouts.app')

@section('title', 'Kayıt Başarılı - ' . config('app.name'))

@push('styles')
<style>
.registration-success {
    text-align: center;
    padding: 40px 20px;
    max-width: 500px;
    margin: 0 auto;
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.registration-success .success-icon {
    font-size: 4rem;
    color: var(--success);
    margin-bottom: 20px;
}

.registration-success h2 {
    color: var(--success);
    margin-bottom: 15px;
}

.registration-success p {
    color: var(--text-secondary);
    margin-bottom: 10px;
}

.email-tips {
    background: #e3f2fd;
    padding: 20px;
    border-radius: 8px;
    margin: 25px 0;
    text-align: left;
}

.email-tips p {
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 10px;
}

.email-tips ul {
    margin-left: 20px;
    margin-bottom: 15px;
    color: var(--text-secondary);
}

.email-tips li {
    margin-bottom: 5px;
}

.email-tips .btn {
    display: block;
    text-align: center;
}

.registration-success > .btn {
    margin-top: 15px;
}
</style>
@endpush

@section('content')
<div class="registration-success">
    <div class="success-icon">
        <i class="fas fa-envelope-open-text"></i>
    </div>
    <h2>Kayıt Başarılı!</h2>
    <p><strong>{{ $email }}</strong> adresine bir doğrulama e-postası gönderdik.</p>
    <p>Hesabınızı aktifleştirmek için lütfen e-postanızdaki linke tıklayın.</p>

    <div class="email-tips">
        <p><i class="fas fa-info-circle"></i> E-posta gelmedi mi?</p>
        <ul>
            <li>Spam/Gereksiz klasörünü kontrol edin</li>
            <li>Birkaç dakika bekleyin</li>
        </ul>
        <a href="{{ route('resend-verification', ['email' => $email]) }}" class="btn btn-secondary">
            <i class="fas fa-redo"></i> Tekrar Gönder
        </a>
    </div>

    <a href="{{ route('login') }}" class="btn btn-primary">
        <i class="fas fa-sign-in-alt"></i> Giriş Sayfasına Git
    </a>
</div>
@endsection
