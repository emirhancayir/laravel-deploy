@props(['urun', 'showBadge' => true])

<div class="product-card-wrapper">
    <a href="{{ route('products.show', $urun) }}" class="product-card">
        <div class="product-image">
            @if($urun->resim)
                <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}" alt="{{ $urun->urun_adi }}" loading="lazy">
            @else
                <div class="product-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            @endif

            @if($showBadge)
                @if($urun->indirimliMi())
                    <span class="product-badge discount">%{{ $urun->indirim_orani }}</span>
                @elseif($urun->stok < 5 && $urun->stok > 0)
                    <span class="product-badge stock">Son {{ $urun->stok }}</span>
                @endif
            @endif
        </div>

        <div class="product-info">
            <span class="product-category">{{ $urun->kategori?->kategori_adi ?? 'Genel' }}</span>
            <h3 class="product-name">{{ $urun->urun_adi }}</h3>

            @if($urun->yorumSayisi() > 0)
                <div class="product-rating">
                    {!! $urun->yildizlar !!}
                    <span>({{ $urun->yorumSayisi() }})</span>
                </div>
            @endif

            <div class="product-price">
                @if($urun->indirimliMi())
                    <span class="old-price">{{ $urun->formatli_eski_fiyat }}</span>
                @endif
                <span class="current-price">{{ $urun->formatli_fiyat }}</span>
            </div>

            @if($urun->il)
                <div class="product-location">
                    <i class="fas fa-map-marker-alt"></i> {{ $urun->il->il_adi }}
                </div>
            @endif
        </div>
    </a>

    @auth
        @php $favoriMi = auth()->user()->favoriler()->where('urun_id', $urun->id)->exists(); @endphp
        <button type="button"
                onclick="event.stopPropagation(); toggleFavori(this, {{ $urun->id }})"
                class="product-favorite {{ $favoriMi ? 'active' : '' }}"
                title="{{ $favoriMi ? 'Favorilerden Çıkar' : 'Favorilere Ekle' }}">
            <i class="{{ $favoriMi ? 'fas' : 'far' }} fa-heart"></i>
        </button>
    @else
        <a href="{{ route('login') }}" class="product-favorite" onclick="event.stopPropagation();">
            <i class="far fa-heart"></i>
        </a>
    @endauth
</div>
