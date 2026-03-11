@extends('layouts.app')

@section('title', 'Favorilerim - ZAMASON')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-heart"></i> Favorilerim</h1>
    @if($favoriler->count() > 0)
        <span class="result-count">{{ $favoriler->total() }} ürün</span>
    @endif
</div>

@if($favoriler->count() > 0)
    <div class="row g-3">
        @foreach($favoriler as $favori)
            <div class="col-6 col-md-4 col-lg-3">
                <x-product-card :urun="$favori->urun" />
            </div>
        @endforeach
    </div>

    <div class="pagination-wrapper">
        {{ $favoriler->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-heart"></i>
        <h3>Favori Ürününüz Yok</h3>
        <p>Beğendiğiniz ürünleri favorilere ekleyin.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag" style="color: #fff !important; font-size: 0.85rem;"></i> Ürünleri Keşfet
        </a>
    </div>
@endif

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
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
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 32px;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: var(--bg-color);
    border-radius: 12px;
    border: 1px solid #e8e8e8;
}
[data-theme="dark"] .empty-state { border-color: #3a3a5a; }
.empty-state i {
    font-size: 2.5rem;
    color: var(--text-light);
    opacity: 0.4;
    margin-bottom: 15px;
}
.empty-state h3 {
    margin-bottom: 8px;
    font-size: 1.1rem;
}
.empty-state p {
    color: var(--text-light);
    margin-bottom: 15px;
    font-size: 0.9rem;
}
.empty-state .btn {
    padding: 10px 20px;
    font-size: 0.9rem;
}
.empty-state .btn i {
    font-size: 0.85rem;
    color: #fff;
    opacity: 1;
    margin-bottom: 0;
    margin-right: 6px;
}
</style>
@endpush
@endsection
