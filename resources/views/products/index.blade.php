@extends('layouts.app')

@section('title', 'Ürünler - ' . config('app.name'))

@section('content')
<div class="page-header">
    <h1>Ürünler</h1>
    <span class="result-count">{{ $urunler->total() }} ürün</span>
</div>

{{-- Kategoriler --}}
<div class="category-nav">
    <a href="{{ route('products.index', request()->except('category')) }}" class="category-chip {{ !request('category') ? 'active' : '' }}">
        Tümü
    </a>
    @foreach($kategoriler as $kategori)
        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $kategori->slug])) }}"
           class="category-chip {{ request('category') == $kategori->slug ? 'active' : '' }}">
            {{ $kategori->kategori_adi }}
        </a>
    @endforeach
</div>

{{-- Filtreler --}}
<div class="filter-bar">
    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
        @if(request('category'))
            <input type="hidden" name="kategori" value="{{ request('category') }}">
        @endif

        <div class="filter-row">
            <div class="filter-item search">
                <input type="text" name="arama" placeholder="Ürün ara..." value="{{ request('arama') }}">
            </div>

            <div class="filter-item">
                <input type="number" name="min_fiyat" placeholder="Min ₺" value="{{ request('min_fiyat') }}" min="0">
            </div>

            <div class="filter-item">
                <input type="number" name="max_fiyat" placeholder="Max ₺" value="{{ request('max_fiyat') }}" min="0">
            </div>

            <div class="filter-item">
                <select name="il">
                    <option value="">Tüm Türkiye</option>
                    @foreach($iller as $il)
                        <option value="{{ $il->id }}" {{ request('il') == $il->id ? 'selected' : '' }}>{{ $il->il_adi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <select name="siralama" onchange="document.getElementById('filterForm').submit()">
                    <option value="yeni" {{ request('siralama', 'yeni') == 'yeni' ? 'selected' : '' }}>En Yeni</option>
                    <option value="fiyat_artan" {{ request('siralama') == 'fiyat_artan' ? 'selected' : '' }}>Fiyat ↑</option>
                    <option value="fiyat_azalan" {{ request('siralama') == 'fiyat_azalan' ? 'selected' : '' }}>Fiyat ↓</option>
                    <option value="populer" {{ request('siralama') == 'populer' ? 'selected' : '' }}>Popüler</option>
                </select>
            </div>

            <button type="submit" class="filter-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        {{-- Aktif Filtreler --}}
        @if(request('arama') || request('min_fiyat') || request('max_fiyat') || request('il'))
        <div class="active-filters">
            @if(request('arama'))
                <span class="filter-tag">"{{ request('arama') }}" <a href="{{ route('products.index', request()->except('arama')) }}">×</a></span>
            @endif
            @if(request('min_fiyat') || request('max_fiyat'))
                <span class="filter-tag">
                    {{ request('min_fiyat') ?: '0' }}₺ - {{ request('max_fiyat') ?: '∞' }}₺
                    <a href="{{ route('products.index', request()->except(['min_fiyat', 'max_fiyat'])) }}">×</a>
                </span>
            @endif
            @if(request('il'))
                @php $secilenIl = $iller->firstWhere('id', request('il')); @endphp
                <span class="filter-tag"><i class="fas fa-map-marker-alt"></i> {{ $secilenIl?->il_adi }} <a href="{{ route('products.index', request()->except('il')) }}">×</a></span>
            @endif
            <a href="{{ route('products.index', request()->only('category')) }}" class="clear-all">Temizle</a>
        </div>
        @endif
    </form>
</div>

{{-- Ürünler --}}
@if($urunler->count() > 0)
    <div class="products-grid">
        @foreach($urunler as $urun)
            <div class="product-card-wrapper">
                <x-product-card :urun="$urun" />
            </div>
        @endforeach
    </div>

    <div class="pagination-wrapper">
        {{ $urunler->withQueryString()->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>Ürün Bulunamadı</h3>
        <p>Arama kriterlerinize uygun ürün yok.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">Tüm Ürünleri Gör</a>
    </div>
@endif

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}
.result-count {
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Category Navigation */
.category-nav {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 8px 0 16px;
    margin-bottom: 16px;
    scrollbar-width: none;
}
.category-nav::-webkit-scrollbar { display: none; }

.category-chip {
    padding: 8px 16px;
    background: var(--bg-color);
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s;
}
[data-theme="dark"] .category-chip { border-color: #3a3a5a; }
.category-chip:hover { border-color: #ff9900; color: #ff9900; }
.category-chip.active { background: #ff9900; border-color: #ff9900; color: white; }

/* Filter Bar */
.filter-bar {
    background: var(--bg-color);
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}
[data-theme="dark"] .filter-bar { border-color: #3a3a5a; }

.filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-item {
    flex: 1;
    min-width: 120px;
}
.filter-item.search {
    flex: 2;
    min-width: 200px;
}

.filter-item input,
.filter-item select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9rem;
    background: var(--bg-color);
    color: var(--text-primary);
}
[data-theme="dark"] .filter-item input,
[data-theme="dark"] .filter-item select {
    border-color: #3a3a5a;
    background: var(--bg-dark);
}

.filter-item input:focus,
.filter-item select:focus {
    outline: none;
    border-color: #ff9900;
}

.filter-btn {
    padding: 10px 20px;
    background: #ff9900;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}
.filter-btn:hover { background: #e68a00; }

/* Active Filters */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e8e8e8;
    align-items: center;
}
[data-theme="dark"] .active-filters { border-color: #3a3a5a; }

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f0f4ff;
    border-radius: 6px;
    font-size: 0.85rem;
    color: #ff9900;
}
[data-theme="dark"] .filter-tag { background: #2a2a4a; }

.filter-tag a {
    color: #ff9900;
    text-decoration: none;
    font-weight: 600;
    margin-left: 4px;
}

.clear-all {
    color: #e74c3c;
    text-decoration: none;
    font-size: 0.85rem;
    margin-left: auto;
}
.clear-all:hover { text-decoration: underline; }

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 32px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-light);
}
.empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 20px;
}
.empty-state h3 {
    margin-bottom: 10px;
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .filter-row { flex-direction: column; }
    .filter-item, .filter-item.search { min-width: 100%; flex: none; }
}
</style>
@endpush
@endsection
