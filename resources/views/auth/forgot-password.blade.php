@extends('layouts.app')

@section('title', 'Şifremi Unuttum - ' . config('app.name'))

@section('content')
<div class="form-container">
    <div class="form-title">
        <h2><i class="fas fa-key"></i> Şifremi Unuttum</h2>
        <p>E-posta adresinize şifre sıfırlama linki göndereceğiz</p>
    </div>

    @if(session('başarılı'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('başarılı') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">E-posta Adresi</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="ornek@email.com">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-paper-plane"></i> Sıfırlama Linki Gönder
        </button>
    </form>

    <div class="form-footer">
        <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Giriş sayfasına dön</a></p>
    </div>
</div>
@endsection
