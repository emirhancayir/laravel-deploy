@extends('layouts.app')

@section('title', 'Giriş Yap - ' . config('app.name'))

@push('styles')
<style>
.auth-page {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}
.auth-card {
    width: 100%;
    max-width: 450px;
    animation: fadeInUp 0.6s ease-out;
}
.auth-logo {
    text-align: center;
    margin-bottom: 30px;
}
.auth-logo i {
    font-size: 3rem;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 15px;
    display: block;
}
.auth-logo h2 {
    font-size: 1.8rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}
.auth-logo p {
    color: var(--text-secondary);
    font-size: 0.95rem;
}
.remember-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
}
.remember-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}
.remember-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: var(--primary);
}
.forgot-link {
    color: var(--primary);
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
}
.forgot-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}
.divider {
    display: flex;
    align-items: center;
    margin: 25px 0;
    color: var(--text-light);
}
.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--bg-dark);
}
.divider span {
    padding: 0 15px;
    font-size: 0.85rem;
}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="form-container neu-card">
            <div class="auth-logo">
                <i class="fas fa-sign-in-alt"></i>
                <h2>Hoş Geldiniz</h2>
                <p>Hesabınıza giriş yapın</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <span>{!! $error !!}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" data-validate>
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> E-posta Adresi
                    </label>
                    <input type="email" id="email" name="email" class="neu-input" value="{{ old('email') }}" placeholder="ornek@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Şifre
                    </label>
                    <input type="password" id="password" name="password" class="neu-input" placeholder="••••••••" required>
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" id="remember" name="remember">
                        <span>Beni hatırla</span>
                    </label>
                    <a href="{{ route('password.forgot') }}" class="forgot-link">Şifremi Unuttum</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Giriş Yap
                </button>
            </form>

            <div class="divider">
                <span>veya</span>
            </div>

            <div class="form-footer">
                <p>Hesabınız yok mu? <a href="{{ route('register') }}" class="text-gradient">Kayıt Olun</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
