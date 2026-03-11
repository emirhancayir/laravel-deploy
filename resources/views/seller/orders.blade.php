@extends('layouts.app')

@section('title', 'Siparişleri Yönet - ZAMASON')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1><i class="fas fa-receipt"></i> Siparişleri Yönet</h1>
    <a href="{{ route('seller.dashboard') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Panele Dön
    </a>
</div>

@if($siparisler->count() > 0)
    @foreach($siparisler as $siparis)
        <div class="card" style="padding: 25px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div>
                    <h3>Sipariş #{{ $siparis->id }}</h3>
                    <small style="color: var(--text-light);">{{ $siparis->created_at->format('d.m.Y H:i') }}</small>
                    <div style="margin-top: 10px;">
                        <strong>Müşteri:</strong> {{ $siparis->kullanici->ad_soyad }}<br>
                        <strong>E-posta:</strong> {{ $siparis->kullanici->email }}
                    </div>
                </div>
                <div style="text-align: right;">
                    <span class="badge badge-{{ $siparis->durum_badge }}" style="font-size: 1rem; padding: 8px 15px;">
                        {{ $siparis->durum_metni }}
                    </span>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-top: 10px;">
                        {{ number_format($siparis->toplam_tutar, 2, ',', '.') }} ₺
                    </div>
                </div>
            </div>

            <div style="background: var(--bg); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Teslimat Adresi:</strong><br>
                {{ $siparis->adres }}
            </div>

            <h4 style="margin-bottom: 15px;">Ürünler</h4>
            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
                @foreach($siparis->detaylar as $detay)
                    <div style="display: flex; gap: 10px; align-items: center; background: var(--bg); padding: 10px 15px; border-radius: 8px;">
                        @if($detay->urun->resim)
                            <img src="{{ asset('serve-image.php?p=urunler/' . $detay->urun->resim) }}"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                        @endif
                        <div>
                            <div style="font-weight: 500;">{{ $detay->urun->urun_adi }}</div>
                            <small style="color: var(--text-light);">
                                {{ $detay->miktar }} adet x {{ number_format($detay->birim_fiyat, 2, ',', '.') }} ₺
                                = <strong>{{ number_format($detay->miktar * $detay->birim_fiyat, 2, ',', '.') }} ₺</strong>
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($siparis->durum !== 'teslim_edildi' && $siparis->durum !== 'iptal')
                <div style="border-top: 1px solid var(--border); padding-top: 20px;">
                    <form action="{{ route('satici.siparis.onayla', $siparis) }}" method="POST" class="order-status-form">
                        @csrf
                        <label><i class="fas fa-sync-alt"></i> Durumu Güncelle:</label>
                        <select name="durum" class="order-status-select">
                            <option value="onaylandi" {{ $siparis->durum === 'onaylandi' ? 'selected' : '' }}>✓ Onaylandı</option>
                            <option value="kargoda" {{ $siparis->durum === 'kargoda' ? 'selected' : '' }}>📦 Kargoya Verildi</option>
                            <option value="teslim_edildi" {{ $siparis->durum === 'teslim_edildi' ? 'selected' : '' }}>✅ Teslim Edildi</option>
                            <option value="iptal">❌ İptal Et</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endforeach

    <div style="margin-top: 30px;">
        {{ $siparisler->links() }}
    </div>
@else
    <div class="card" style="padding: 60px; text-align: center;">
        <i class="fas fa-inbox" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
        <h3>Henüz Sipariş Yok</h3>
        <p style="color: var(--text-light);">Ürünlerinize sipariş geldiğinde burada görünecek.</p>
    </div>
@endif
@endsection
