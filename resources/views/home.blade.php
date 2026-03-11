@extends('layouts.app')

@section('title', config('app.name') . ' - Ana Sayfa')

@section('content')
{{-- Hero Slider --}}
@if(isset($sliders) && $sliders->count() > 0)
<div id="heroSlider" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="5000" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div class="carousel-indicators">
        @foreach($sliders as $index => $slider)
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" style="width: 10px; height: 10px; border-radius: 50%;"></button>
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach($sliders as $index => $slider)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                @if($slider->link)<a href="{{ $slider->link }}" class="d-block">@endif
                    @if($slider->resim)
                        <img src="{{ $slider->resim_url }}" alt="{{ $slider->baslik }}" class="d-block w-100" style="max-height: 400px; object-fit: cover;">
                    @endif
                    <div class="carousel-caption" style="background: linear-gradient(transparent, rgba(0,0,0,0.7)); bottom: 0; left: 0; right: 0; padding: 30px;">
                        <h2 style="font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">{{ $slider->baslik }}</h2>
                        @if($slider->alt_baslik)<p class="mb-0">{{ $slider->alt_baslik }}</p>@endif
                    </div>
                @if($slider->link)</a>@endif
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
@endif

{{-- Kategoriler --}}
@if($kategoriler->count() > 0)
<div class="category-nav">
    <a href="{{ route('products.index') }}" class="category-chip active">
        <i class="fas fa-th-large"></i> Tümü
    </a>
    @foreach($kategoriler as $kategori)
        <a href="{{ route('products.index', ['category' => $kategori->slug]) }}" class="category-chip">
            {{ $kategori->kategori_adi }}
        </a>
    @endforeach
</div>
@endif

{{-- Popüler Ürünler --}}
@if(isset($populerUrunler) && $populerUrunler->count() > 0)
<section class="mb-5">
    <div class="section-header">
        <h2><i class="fas fa-fire text-danger"></i> Popüler Ürünler</h2>
        <a href="{{ route('products.index', ['siralama' => 'populer']) }}">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid">
        @foreach($populerUrunler as $urun)
            <x-product-card :urun="$urun" />
        @endforeach
    </div>
</section>
@endif

{{-- İndirimli Ürünler --}}
@if(isset($indirimliUrunler) && $indirimliUrunler->count() > 0)
<section class="mb-5">
    <div class="section-header">
        <h2><i class="fas fa-percent text-success"></i> İndirimli Ürünler</h2>
        <a href="{{ route('products.index') }}">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid">
        @foreach($indirimliUrunler as $urun)
            <x-product-card :urun="$urun" />
        @endforeach
    </div>
</section>
@endif

{{-- Yeni Ürünler --}}
@if(isset($yeniUrunler) && $yeniUrunler->count() > 0)
<section class="mb-5">
    <div class="section-header">
        <h2><i class="fas fa-sparkles text-warning"></i> Yeni Ürünler</h2>
        <a href="{{ route('products.index', ['siralama' => 'yeni']) }}">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid">
        @foreach($yeniUrunler as $urun)
            <x-product-card :urun="$urun" />
        @endforeach
    </div>
</section>
@endif

@push('styles')
<style>
/* Category Navigation */
.category-nav {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 16px 0;
    margin-bottom: 24px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.category-nav::-webkit-scrollbar { display: none; }

.category-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: var(--bg-color);
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}

[data-theme="dark"] .category-chip {
    border-color: #3a3a5a;
}

.category-chip:hover {
    border-color: #ff9900;
    color: #ff9900;
}

.category-chip.active {
    background: #ff9900;
    border-color: #ff9900;
    color: white;
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header a {
    color: #ff9900;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.section-header a:hover {
    text-decoration: underline;
}
</style>
@endpush
@endsection
