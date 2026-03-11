@extends('layouts.app')

@section('title', 'Sepetim - ' . config('app.name'))

@section('content')
<h1 class="section-title"><i class="fas fa-shopping-cart"></i> Sepetim</h1>

@if($sepet->count() > 0)
    <div class="cart-page">
        <div class="cart-items">
            @foreach($sepet as $item)
                <div class="cart-item">
                    <div class="cart-item-image">
                        @if($item->urun->resim)
                            <img src="{{ asset('serve-image.php?p=urunler/' . $item->urun->resim) }}" alt="{{ $item->urun->urun_adi }}">
                        @else
                            <i class="fas fa-image"></i>
                        @endif
                    </div>
                    <div class="cart-item-info">
                        <h3 class="cart-item-name">{{ $item->urun->urun_adi }}</h3>
                        <div class="cart-item-price">{{ $item->urun->formatli_fiyat }}</div>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                @if($item->miktar > 1)
                                    <form action="{{ route('cart.guncelle', $item->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="miktar" value="{{ $item->miktar - 1 }}">
                                        <button type="submit" class="minus">−</button>
                                    </form>
                                @else
                                    <button type="button" class="minus" disabled style="opacity: 0.5; cursor: not-allowed;">−</button>
                                @endif

                                <span style="padding: 0 15px; font-weight: 600;">{{ $item->miktar }}</span>

                                @if($item->miktar < $item->urun->stok)
                                    <form action="{{ route('cart.guncelle', $item->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="miktar" value="{{ $item->miktar + 1 }}">
                                        <button type="submit" class="plus">+</button>
                                    </form>
                                @else
                                    <button type="button" class="plus" disabled style="opacity: 0.5; cursor: not-allowed;">+</button>
                                @endif
                            </div>
                            <form action="{{ route('cart.sil', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="remove-item" title="Kaldır">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div style="text-align: right; font-size: 1.2rem; font-weight: 700; color: var(--primary);">
                        {{ number_format($item->urun->fiyat * $item->miktar, 2, ',', '.') }} ₺
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cart-summary">
            <h3>Sipariş Özeti</h3>
            <div class="summary-row">
                <span>Ürün Sayısı</span>
                <span>{{ $sepet->sum('miktar') }} adet</span>
            </div>
            <div class="summary-row">
                <span>Ara Toplam</span>
                <span>{{ number_format($toplam, 2, ',', '.') }} ₺</span>
            </div>
            <div class="summary-row">
                <span>Kargo</span>
                <span style="color: var(--secondary);">Ücretsiz</span>
            </div>
            <div class="summary-row total">
                <span>Toplam</span>
                <span>{{ number_format($toplam, 2, ',', '.') }} ₺</span>
            </div>

            @auth
                <a href="{{ route('payment.index') }}" class="btn btn-secondary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-credit-card"></i> Ödemeye Geç
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-sign-in-alt"></i> Ödeme için Giriş Yapın
                </a>
                <p style="text-align: center; margin-top: 10px; font-size: 0.9rem; color: var(--text-light);">
                    Hesabınız yok mu? <a href="{{ route('register') }}">Kayıt olun</a>
                </p>
            @endauth
        </div>
    </div>
@else
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h3>Sepetiniz Boş</h3>
        <p>Henüz sepetinize ürün eklemediniz.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Alışverişe Başla
        </a>
    </div>
@endif
@endsection
