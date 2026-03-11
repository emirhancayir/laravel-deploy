@extends('layouts.app')

@section('title', 'Kargolarım - ZAMASON')

@section('content')
<h1 class="section-title"><i class="fas fa-truck"></i> Kargolarım</h1>

<!-- İstatistik Kartları -->
<div class="seller-stats" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h4>Toplam</h4>
            <p>{{ $istatistikler['toplam'] }}</p>
        </div>
    </div>
    <a href="{{ route('shipping.index', ['tip' => 'gelen']) }}" class="stat-card clickable">
        <div class="stat-icon green">
            <i class="fas fa-inbox"></i>
        </div>
        <div class="stat-info">
            <h4>Gelen Kargolar</h4>
            <p>{{ $istatistikler['gelen'] }}</p>
        </div>
    </a>
    <a href="{{ route('shipping.index', ['tip' => 'giden']) }}" class="stat-card clickable">
        <div class="stat-icon purple">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-info">
            <h4>Giden Kargolar</h4>
            <p>{{ $istatistikler['giden'] }}</p>
        </div>
    </a>
    <a href="{{ route('shipping.index', ['durum' => 'kargoda']) }}" class="stat-card clickable {{ $istatistikler['kargoda'] > 0 ? 'highlight' : '' }}">
        <div class="stat-icon orange">
            <i class="fas fa-shipping-fast"></i>
        </div>
        <div class="stat-info">
            <h4>Yolda</h4>
            <p>{{ $istatistikler['kargoda'] }}</p>
        </div>
        @if($istatistikler['kargoda'] > 0)
            <span class="badge-notification">{{ $istatistikler['kargoda'] }}</span>
        @endif
    </a>
</div>

<!-- Filtreler -->
<div class="card" style="padding: 20px; margin-bottom: 20px;">
    <form action="{{ route('shipping.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label"><i class="fas fa-filter"></i> Tip</label>
            <select name="tip" class="form-select">
                <option value="">Tümü</option>
                <option value="gelen" {{ request('tip') == 'gelen' ? 'selected' : '' }}>Gelen Kargolar</option>
                <option value="giden" {{ request('tip') == 'giden' ? 'selected' : '' }}>Giden Kargolar</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label"><i class="fas fa-info-circle"></i> Durum</label>
            <select name="durum" class="form-select">
                <option value="">Tümü</option>
                <option value="beklemede" {{ request('durum') == 'beklemede' ? 'selected' : '' }}>Adres Bekleniyor</option>
                <option value="hazirlaniyor" {{ request('durum') == 'hazirlaniyor' ? 'selected' : '' }}>Hazırlanıyor</option>
                <option value="kargoda" {{ request('durum') == 'kargoda' ? 'selected' : '' }}>Kargoda</option>
                <option value="teslim_edildi" {{ request('durum') == 'teslim_edildi' ? 'selected' : '' }}>Teslim Edildi</option>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-search"></i> Filtrele
            </button>
        </div>
    </form>
</div>

<!-- Aktif Filtreler -->
@if(request('tip') || request('durum'))
<div class="mb-3">
    <span style="color: var(--text-light);">Filtreler: </span>
    @if(request('tip'))
        <span class="badge bg-primary" style="padding: 8px 12px; border-radius: 20px;">
            {{ request('tip') == 'gelen' ? 'Gelen' : 'Giden' }} Kargolar
            <a href="{{ route('shipping.index', request()->except('tip')) }}" style="color: white; margin-left: 5px;"><i class="fas fa-times"></i></a>
        </span>
    @endif
    @if(request('durum'))
        @php
            $durumlar = [
                'beklemede' => 'Adres Bekleniyor',
                'hazirlaniyor' => 'Hazırlanıyor',
                'kargoda' => 'Kargoda',
                'teslim_edildi' => 'Teslim Edildi'
            ];
        @endphp
        <span class="badge bg-info" style="padding: 8px 12px; border-radius: 20px;">
            {{ $durumlar[request('durum')] ?? request('durum') }}
            <a href="{{ route('shipping.index', request()->except('durum')) }}" style="color: white; margin-left: 5px;"><i class="fas fa-times"></i></a>
        </span>
    @endif
    <a href="{{ route('shipping.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; margin-left: 10px;">
        <i class="fas fa-times"></i> Temizle
    </a>
</div>
@endif

<!-- Kargo Listesi -->
@if($kargolar->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            @foreach($kargolar as $kargo)
                <div style="display: flex; align-items: center; gap: 20px; padding: 20px; border-bottom: 1px solid var(--border);">
                    <!-- Ürün Resmi -->
                    <div style="width: 80px; height: 80px; border-radius: 10px; overflow: hidden; flex-shrink: 0;">
                        @if($kargo->urun && $kargo->urun->resim)
                            <img src="{{ asset('serve-image.php?p=urunler/' . $kargo->urun->resim) }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; background: var(--bg); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-box" style="font-size: 1.5rem; color: var(--text-light);"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Kargo Bilgileri -->
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <div>
                                <h4 style="margin: 0; font-size: 1.1rem;">{{ $kargo->urun->urun_adi ?? 'Ürün Silinmiş' }}</h4>
                                <span style="font-size: 0.85rem; color: var(--text-light);">
                                    @if($kargo->alici_id === auth()->id())
                                        <i class="fas fa-inbox text-success"></i> Gelen -
                                        Gönderen: {{ $kargo->gonderen->ad ?? 'Bilinmiyor' }} {{ Str::limit($kargo->gonderen->soyad ?? '', 1) }}.
                                    @else
                                        <i class="fas fa-paper-plane text-primary"></i> Giden -
                                        Alıcı: {{ $kargo->alici->ad ?? 'Bilinmiyor' }} {{ Str::limit($kargo->alici->soyad ?? '', 1) }}.
                                    @endif
                                </span>
                            </div>
                            <span class="badge badge-{{ $kargo->durum_rengi }}">
                                {{ $kargo->durum_metni }}
                            </span>
                        </div>

                        <div style="display: flex; gap: 20px; font-size: 0.9rem; color: var(--text-light);">
                            @if($kargo->kargoFirmasi)
                                <span><i class="fas fa-truck"></i> {{ $kargo->kargoFirmasi->firma_adi }}</span>
                            @endif
                            @if($kargo->takip_no)
                                <span><i class="fas fa-barcode"></i> {{ $kargo->takip_no }}</span>
                            @endif
                            <span><i class="fas fa-calendar"></i> {{ $kargo->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>

                    <!-- Tutar ve Buton -->
                    <div style="text-align: right;">
                        <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-bottom: 10px;">
                            {{ $kargo->formatli_toplam }}
                        </div>
                        <a href="{{ route('shipping.show', $kargo) }}" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-eye"></i> Detay
                        </a>
                        @if($kargo->takip_link)
                            <a href="{{ $kargo->takip_link }}" target="_blank" class="btn btn-primary" style="padding: 8px 15px;">
                                <i class="fas fa-external-link-alt"></i> Takip
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $kargolar->links() }}
    </div>
@else
    <div class="empty-cart">
        <i class="fas fa-truck"></i>
        <h3>Henüz kargo bulunmuyor</h3>
        <p>Bir ürün satın aldığınızda veya sattığınızda kargo bilgileri burada görünecek.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Alışverişe Başla
        </a>
    </div>
@endif

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 5px;
    font-size: 0.9rem;
}
</style>
@endsection
