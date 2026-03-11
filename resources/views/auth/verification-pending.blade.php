@extends('layouts.app')

@section('title', 'E-posta Dogrulama')

@section('content')
<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div class="card">
        <div class="card-body text-center" style="padding: 40px;">
            <div style="font-size: 60px; color: #ff9900; margin-bottom: 20px;">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h2 style="margin-bottom: 15px;">E-postanizi Dogrulayin</h2>
            <p style="color: #666; margin-bottom: 25px;">
                <strong>{{ $email }}</strong> adresine bir dogrulama e-postasi gonderdik.
            </p>
            <p style="color: #888; font-size: 14px;">
                Lutfen e-postanizdaki baglantiya tiklayarak hesabinizi dogrulayin.
                E-posta gelmedi mi? Spam/Gereksiz klasorunu kontrol edin.
            </p>
            <hr style="margin: 30px 0;">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Giris Sayfasina Git
            </a>
        </div>
    </div>
</div>
@endsection
