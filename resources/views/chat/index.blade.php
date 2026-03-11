@extends('layouts.app')

@section('title', 'Mesajlarım - ZAMASON')

@section('content')
<div class="card" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="margin: 0;">
            <i class="fas fa-comments"></i> Mesajlarım
        </h1>
        <a href="{{ route('chat.archived') }}" class="btn btn-secondary">
            <i class="fas fa-archive"></i> Arşiv
        </a>
    </div>

    @if($konusmalar->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
            <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 10px;">Henüz mesajınız yok</h3>
            <p>Bir ürünle ilgilendiğinizde satıcıyla iletişime geçebilirsiniz.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-search"></i> Ürünlere Göz At
            </a>
        </div>
    @else
        <div class="konusma-listesi">
            @foreach($konusmalar as $konusma)
                @php
                    $karsiTaraf = $konusma->karsiTaraf(auth()->user());
                    $okunmamis = $konusma->okunmamisSayisi(auth()->user());
                @endphp
                <a href="{{ route('chat.show', $konusma) }}" class="konusma-item {{ $okunmamis > 0 ? 'okunmamis' : '' }}">
                    <div class="konusma-avatar">
                        @if($karsiTaraf && $karsiTaraf->profil_resmi)
                            <img src="{{ asset('serve-image.php?p=profil/' . $karsiTaraf->profil_resmi) }}" alt="">
                        @else
                            <span>{{ $karsiTaraf ? strtoupper(substr($karsiTaraf->ad, 0, 1)) : '?' }}</span>
                        @endif
                    </div>
                    <div class="konusma-bilgi">
                        <div class="konusma-baslik">
                            <strong>{{ $karsiTaraf ? $karsiTaraf->ad_soyad : 'Bilinmeyen' }}</strong>
                            <span class="konusma-tarih">
                                {{ $konusma->son_mesaj_tarihi?->diffForHumans() ?? $konusma->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="konusma-urun">
                            <i class="fas fa-tag"></i> {{ Str::limit($konusma->urun->urun_adi, 30) }}
                        </div>
                        @if($konusma->sonMesaj)
                            <p class="konusma-son-mesaj">
                                @if($konusma->sonMesaj->gonderen_id === auth()->id())
                                    <span style="color: var(--text-light);">Siz:</span>
                                @endif
                                {{ Str::limit($konusma->sonMesaj->mesaj, 50) }}
                            </p>
                        @endif
                        @if($konusma->sonTeklif && $konusma->sonTeklif->beklemedeMi())
                            <span class="badge badge-warning" style="margin-top: 5px;">
                                <i class="fas fa-hand-holding-usd"></i> Bekleyen Teklif: {{ $konusma->sonTeklif->formatli_tutar }}
                            </span>
                        @endif
                    </div>
                    @if($okunmamis > 0)
                        <span class="okunmamis-badge">{{ $okunmamis }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $konusmalar->links() }}
        </div>
    @endif
</div>
@push('styles')
<style>
/* Dark Mode Chat Düzeltmeleri */
[data-theme="dark"] .card {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] h1,
[data-theme="dark"] h2,
[data-theme="dark"] h3 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] p {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .konusma-item {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .konusma-item:hover {
    background: #252541 !important;
}

[data-theme="dark"] .konusma-item.okunmamis {
    background: #252541 !important;
    border-left-color: #ff9900 !important;
}

[data-theme="dark"] .konusma-avatar {
    background: #2d2d44 !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .konusma-baslik strong {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .konusma-tarih {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .konusma-urun {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .konusma-son-mesaj {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .okunmamis-badge {
    background: #ef4444 !important;
    color: #fff !important;
}

[data-theme="dark"] .badge {
    color: #fff !important;
}

[data-theme="dark"] .badge-warning {
    background: #f59e0b !important;
}
</style>
@endpush
@endsection
