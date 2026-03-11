@extends('layouts.app')

@section('title', 'Şifre Sıfırla - ' . config('app.name'))

@section('content')
<div class="form-container">
    <div class="form-title">
        <h2><i class="fas fa-lock"></i> Yeni Şifre Belirle</h2>
        <p>Yeni şifrenizi girin</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <label for="email_display">E-posta Adresi</label>
            <input type="email" id="email_display" value="{{ $email }}" disabled
                   style="background: var(--bg); cursor: not-allowed;">
        </div>

        <div class="form-group">
            <label for="password">Yeni Şifre</label>
            <input type="password" id="password" name="password" required
                   placeholder="En az 6 karakter">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Şifre Tekrar</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   placeholder="Şifrenizi tekrar girin">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-save"></i> Şifremi Güncelle
        </button>
    </form>

    <div class="form-footer">
        <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Giriş sayfasına dön</a></p>
    </div>
</div>
@endsection
