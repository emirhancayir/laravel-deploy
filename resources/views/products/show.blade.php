@extends('layouts.app')

@section('title', $urun->urun_adi . ' - ZAMASON')

@section('content')
<div class="product-detail">
    <div class="row g-4">
        {{-- Ürün Görselleri --}}
        <div class="col-12 col-md-6">
            <div class="product-gallery">
                @if($urun->resim)
                    <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}"
                         alt="{{ $urun->urun_adi }}"
                         id="mainProductImage"
                         class="main-image"
                         onclick="openLightbox(currentImageIndex)"
                         style="cursor: zoom-in;">
                @else
                    <div class="main-image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif

                @if($urun->resimler->count() > 1)
                    <button type="button" onclick="prevImage()" class="gallery-nav prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" onclick="nextImage()" class="gallery-nav next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif

                {{-- Zoom icon --}}
                @if($urun->resim)
                <div class="zoom-hint" onclick="openLightbox(currentImageIndex)">
                    <i class="fas fa-search-plus"></i>
                </div>
                @endif
            </div>

            @if($urun->resimler->count() > 1)
                <div class="thumbnail-list" id="thumbnailContainer">
                    @foreach($urun->resimler as $index => $resim)
                        <img src="{{ asset('serve-image.php?p=urunler/' . $resim->resim) }}"
                             alt="{{ $urun->urun_adi }}"
                             class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                             data-index="{{ $index }}"
                             onclick="changeMainImage(this, {{ $index }})">
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Ürün Bilgileri --}}
        <div class="col-12 col-md-6">
            <div class="product-badges">
                <span class="badge badge-category">{{ $urun->kategori?->kategori_adi ?? 'Genel' }}</span>
                @if($urun->satildi)
                    <span class="badge badge-sold"><i class="fas fa-check-circle"></i> SATILDI</span>
                @endif
            </div>

            <h1 class="product-title">{{ $urun->urun_adi }}</h1>

            @php
                $ortalamaPuan = $urun->ortalamaPuan();
                $yorumSayisi = $urun->yorumSayisi();
            @endphp
            @if($yorumSayisi > 0)
            <div class="product-rating">
                {!! $urun->yildizlar !!}
                <span class="rating-text">{{ $ortalamaPuan }} ({{ $yorumSayisi }} değerlendirme)</span>
            </div>
            @endif

            <div class="product-price-large">{{ $urun->formatli_fiyat }}</div>

            <div class="stock-status {{ $urun->stok > 0 ? 'in-stock' : 'out-of-stock' }}">
                @if($urun->stok > 0)
                    <i class="fas fa-check-circle"></i> Stokta var ({{ $urun->stok }} adet)
                @else
                    <i class="fas fa-times-circle"></i> Stokta yok
                @endif
            </div>

            <div class="seller-info">
                <i class="fas fa-store"></i> Satıcı: {{ $urun->satici->ad_soyad }}
            </div>

            @if($urun->il)
            <div class="location-box">
                <div class="location-main">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $urun->il->il_adi }}@if($urun->ilce) / {{ $urun->ilce->ilce_adi }}@endif @if($urun->mahalle) / {{ $urun->mahalle->mahalle_adi }}@endif</span>
                </div>
                @if($urun->adres_detay)
                <div class="location-detail">
                    <i class="fas fa-road"></i> {{ $urun->adres_detay }}
                </div>
                @endif
            </div>
            @endif

            {{-- Satıcıyla İletişim --}}
            @if($urun->satici_id !== auth()->id())
                @auth
                    <form action="{{ route('chat.store') }}" method="POST" class="contact-form">
                        @csrf
                        <input type="hidden" name="urun_id" value="{{ $urun->id }}">
                        <input type="text" name="mesaj" placeholder="Mesajınızı yazın (opsiyonel)..." class="contact-input">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-comment"></i> Satıcıyla İletişime Geç
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-comment"></i> Mesaj Göndermek İçin Giriş Yapın
                    </a>
                @endauth
            @else
                <div class="own-product-notice">
                    <i class="fas fa-info-circle"></i> Bu sizin ürünüz
                </div>
            @endif

            @auth
                @php $favoride = auth()->user()->favoriler()->where('urun_id', $urun->id)->exists(); @endphp
                <button type="button" onclick="toggleFavori(this, {{ $urun->id }})"
                        class="btn-favorite-large {{ $favoride ? 'active' : '' }}">
                    <i class="{{ $favoride ? 'fas' : 'far' }} fa-heart"></i>
                    <span>{{ $favoride ? 'Favorilerde' : 'Favorilere Ekle' }}</span>
                </button>
            @else
                <a href="{{ route('login') }}" class="btn-favorite-large">
                    <i class="far fa-heart"></i>
                    <span>Favoriler için Giriş Yapın</span>
                </a>
            @endauth

            @if($urun->aciklama)
            <div class="product-description">
                <h3>Ürün Açıklaması</h3>
                <p>{{ $urun->aciklama }}</p>
            </div>
            @endif

            {{-- Kategori Özellikleri --}}
            @if($urun->attributeValues && $urun->attributeValues->count() > 0)
            <div class="product-attributes">
                <h3><i class="fas fa-list-alt"></i> Ürün Özellikleri</h3>
                <div class="attributes-grid">
                    @foreach($urun->attributeValues as $attrValue)
                    <div class="attribute-item">
                        <span class="attr-label">{{ $attrValue->attribute->label ?? 'Özellik' }}</span>
                        <strong class="attr-value">{{ $attrValue->deger }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Değerlendirmeler --}}
<div class="reviews-section">
    <h2><i class="fas fa-comments"></i> Değerlendirmeler</h2>

    @php
        $yorumlar = $urun->onayliYorumlar()->with('kullanici')->latest()->get();
        $kullanicininYorumu = auth()->check() ?
            \App\Models\Yorum::where('kullanici_id', auth()->id())->where('urun_id', $urun->id)->first() : null;
        $yorumYapabilir = false;

        if (auth()->check() && !$kullanicininYorumu) {
            $yorumYapabilir = \App\Models\Odeme::where('alici_id', auth()->id())
                ->where('urun_id', $urun->id)
                ->where('durum', 'odendi')
                ->exists();
        }
    @endphp

    {{-- Puan Özeti --}}
    @if($yorumlar->count() > 0)
    <div class="rating-summary">
        <div class="rating-score">
            <div class="score-value">{{ $ortalamaPuan }}</div>
            <div class="score-stars">{!! $urun->yildizlar !!}</div>
            <div class="score-count">{{ $yorumlar->count() }} değerlendirme</div>
        </div>
        <div class="rating-bars">
            @php $dagilim = $urun->puanDagilimi(); $toplam = max($yorumlar->count(), 1); @endphp
            @for($i = 5; $i >= 1; $i--)
            <div class="rating-bar-row">
                <span class="bar-label">{{ $i }} yıldız</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ ($dagilim[$i] / $toplam) * 100 }}%"></div>
                </div>
                <span class="bar-count">{{ $dagilim[$i] }}</span>
            </div>
            @endfor
        </div>
    </div>
    @endif

    {{-- Yorum Formu / Mevcut Yorum --}}
    @auth
        @if($kullanicininYorumu)
        <div class="user-review">
            <div class="review-header">
                <div>
                    <strong><i class="fas fa-check-circle"></i> Değerlendirmeniz</strong>
                    <div class="review-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $kullanicininYorumu->puan ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                        <span>{{ $kullanicininYorumu->puan }}/5</span>
                    </div>
                </div>
                <form action="{{ route('review.destroy', $kullanicininYorumu) }}" method="POST" id="yorumSilForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger" onclick="yorumSil()">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </form>
                <script>
                async function yorumSil() {
                    const confirmed = await showConfirm({
                        type: 'danger',
                        title: 'Yorumu Sil',
                        message: 'Yorumunuz silinecek. Emin misiniz?',
                        confirmText: 'Evet, Sil',
                        cancelText: 'Vazgeç'
                    });
                    if (confirmed) {
                        document.getElementById('yorumSilForm').submit();
                    }
                }
                </script>
            </div>
            @if($kullanicininYorumu->yorum)
                <p class="review-text">{{ $kullanicininYorumu->yorum }}</p>
            @endif
            @if($kullanicininYorumu->resimler && count($kullanicininYorumu->resimler) > 0)
            <div class="review-images">
                @foreach($kullanicininYorumu->resimler as $resim)
                    <img src="{{ asset('uploads/yorumlar/' . $resim) }}" alt="Yorum fotoğrafı">
                @endforeach
            </div>
            @endif
            @if(!$kullanicininYorumu->onaylandi)
                <div class="review-pending"><i class="fas fa-clock"></i> Onay bekleniyor</div>
            @else
                <small class="review-date"><i class="fas fa-calendar-alt"></i> {{ $kullanicininYorumu->created_at->format('d.m.Y H:i') }}</small>
            @endif
        </div>
        @elseif($yorumYapabilir)
        <form action="{{ route('review.store', $urun) }}" method="POST" enctype="multipart/form-data" class="review-form">
            @csrf
            <h4><i class="fas fa-edit"></i> Ürünü Değerlendirin</h4>

            <div class="form-group">
                <label>Puanınız:</label>
                <div class="star-rating" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="far fa-star" data-rating="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="puan" id="puanInput" required>
            </div>

            <div class="form-group">
                <label>Yorumunuz (opsiyonel):</label>
                <textarea name="yorum" rows="3" class="form-control" placeholder="Bu ürün hakkında düşüncelerinizi yazın..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-camera"></i> Fotoğraf Ekle (opsiyonel, max 5 adet):</label>
                <input type="file" name="resimler[]" accept="image/*" multiple id="yorumResimler" style="display: none;">
                <div class="upload-area" onclick="document.getElementById('yorumResimler').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Fotoğraf seçmek için tıklayın</p>
                    <small>JPEG, PNG, WebP - Max 10MB</small>
                </div>
                <div id="yorumResimOnizleme" class="upload-preview"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Gönder
            </button>
        </form>
        @elseif($urun->satici_id !== auth()->id())
        <div class="review-notice">
            <i class="fas fa-info-circle"></i> Bu ürünü satın aldıktan sonra değerlendirme yapabilirsiniz.
        </div>
        @endif
    @else
        <div class="review-notice">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Değerlendirme yapmak için giriş yapın
            </a>
        </div>
    @endauth

    {{-- Yorumlar Listesi --}}
    @if($yorumlar->count() > 0)
    <div class="reviews-list">
        @foreach($yorumlar as $yorum)
        <div class="review-item">
            <div class="reviewer-info">
                <div class="reviewer-avatar">
                    {{ strtoupper(substr($yorum->kullanici->ad ?? 'A', 0, 1)) }}
                </div>
                <div class="reviewer-details">
                    <div class="reviewer-header">
                        <div class="reviewer-name">
                            <strong>{{ $yorum->kullanici->ad ?? 'Anonim' }} {{ $yorum->kullanici->soyad ?? '' }}</strong>
                            @if($yorum->kullanici)
                                <span class="verified-badge"><i class="fas fa-check-circle"></i> Doğrulanmış Alıcı</span>
                            @endif
                            <div class="reviewer-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $yorum->puan ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                                <span>{{ $yorum->puan }}/5</span>
                            </div>
                        </div>
                        <div class="review-date-info">
                            <small><i class="fas fa-calendar-alt"></i> {{ $yorum->created_at->format('d.m.Y') }}</small>
                            <small>{{ $yorum->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($yorum->yorum)
                <p class="review-content">{{ $yorum->yorum }}</p>
            @endif

            @if($yorum->resimler && count($yorum->resimler) > 0)
            <div class="review-photos">
                @foreach($yorum->resimler as $resim)
                    <a href="{{ asset('uploads/yorumlar/' . $resim) }}" target="_blank">
                        <img src="{{ asset('uploads/yorumlar/' . $resim) }}" alt="Yorum fotoğrafı">
                    </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="no-reviews">
        <i class="fas fa-comments"></i>
        <p>Bu ürün için henüz değerlendirme yapılmamış.</p>
    </div>
    @endif
</div>

{{-- Benzer Ürünler --}}
@if($benzerUrunler->count() > 0)
<section class="similar-products">
    <div class="section-header">
        <h2><i class="fas fa-th-large"></i> Benzer Ürünler</h2>
    </div>
    <div class="row g-3">
        @foreach($benzerUrunler as $benzer)
            <div class="col-6 col-md-4 col-lg-3">
                <x-product-card :urun="$benzer" />
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Lightbox Modal --}}
<div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)">&times;</button>
    <button class="lightbox-nav lightbox-prev" onclick="lightboxPrev(event)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="lightbox-content">
        <img id="lightboxImage" src="" alt="">
        <div class="lightbox-counter">
            <span id="lightboxCounter">1</span> / <span id="lightboxTotal">1</span>
        </div>
    </div>
    <button class="lightbox-nav lightbox-next" onclick="lightboxNext(event)">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

@push('styles')
<style>
/* Lightbox Styles */
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 10000;
    align-items: center;
    justify-content: center;
}
.lightbox.active {
    display: flex;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    position: relative;
}

.lightbox-content img {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 8px;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 30px;
    font-size: 40px;
    color: white;
    background: none;
    border: none;
    cursor: pointer;
    z-index: 10001;
    transition: transform 0.2s;
}
.lightbox-close:hover {
    transform: scale(1.2);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    font-size: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-50%) scale(1.1);
}
.lightbox-prev { left: 30px; }
.lightbox-next { right: 30px; }

.lightbox-counter {
    text-align: center;
    color: white;
    margin-top: 15px;
    font-size: 1rem;
}

.zoom-hint {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}
.zoom-hint:hover {
    background: rgba(102, 126, 234, 0.8);
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .lightbox-nav {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
    .lightbox-prev { left: 10px; }
    .lightbox-next { right: 10px; }
    .lightbox-close {
        top: 15px;
        right: 15px;
        font-size: 30px;
    }
}

/* Product Detail Page */
.product-detail {
    background: var(--bg-color);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e8e8e8;
}
[data-theme="dark"] .product-detail { border-color: #3a3a5a; }

/* Product Gallery */
.product-gallery {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #f5f5f5;
}
[data-theme="dark"] .product-gallery { background: #2a2a4a; }

.product-gallery .main-image {
    width: 100%;
    height: 450px;
    object-fit: contain;
    object-position: center;
}

.main-image-placeholder {
    height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 4rem;
}

.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.2s;
}
.gallery-nav:hover { background: rgba(0,0,0,0.8); }
.gallery-nav.prev { left: 12px; }
.gallery-nav.next { right: 12px; }

.thumbnail-list {
    display: flex;
    gap: 10px;
    margin-top: 12px;
    overflow-x: auto;
    padding-bottom: 8px;
}

.thumbnail {
    width: 80px;
    height: 80px;
    object-fit: contain;
    background: #f5f5f5;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    flex-shrink: 0;
    transition: border-color 0.2s;
}
[data-theme="dark"] .thumbnail { background: #2a2a4a; }
.thumbnail:hover { border-color: #ff9900; }
.thumbnail.active { border-color: #ff9900; }

/* Product Info */
.product-badges {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
}

.badge-category {
    background: #ff9900;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge-sold {
    background: #e74c3c;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}

.product-title {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 12px;
    line-height: 1.3;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.product-rating .rating-text {
    color: var(--text-light);
    font-size: 0.9rem;
}

.product-price-large {
    font-size: 2rem;
    font-weight: 700;
    color: #ff9900;
    margin-bottom: 16px;
}

.stock-status {
    margin-bottom: 12px;
    font-size: 0.95rem;
}
.stock-status.in-stock { color: #38ef7d; }
.stock-status.out-of-stock { color: #e74c3c; }

.seller-info {
    color: var(--text-light);
    margin-bottom: 16px;
}

.location-box {
    background: rgba(102, 126, 234, 0.1);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 20px;
}

.location-main {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}
.location-main i { color: #ff9900; font-size: 1.1rem; }

.location-detail {
    margin-top: 8px;
    padding-left: 28px;
    color: var(--text-light);
    font-size: 0.9rem;
}

.contact-form {
    margin-bottom: 16px;
}

.contact-input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 0.95rem;
    background: var(--bg-color);
    color: var(--text-primary);
}
[data-theme="dark"] .contact-input { border-color: #3a3a5a; }

.btn-block {
    width: 100%;
    justify-content: center;
}

.own-product-notice {
    padding: 14px;
    background: var(--bg-dark);
    border-radius: 8px;
    text-align: center;
    color: var(--text-light);
    margin-bottom: 16px;
}

.btn-favorite-large {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: transparent;
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
[data-theme="dark"] .btn-favorite-large { border-color: #3a3a5a; }
.btn-favorite-large:hover { border-color: #e74c3c; color: #e74c3c; }
.btn-favorite-large.active { border-color: #e74c3c; color: #e74c3c; background: rgba(231,76,60,0.1); }

.product-description {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e8e8e8;
}
[data-theme="dark"] .product-description { border-color: #3a3a5a; }
.product-description h3 { margin-bottom: 12px; font-size: 1.1rem; }
.product-description p { color: var(--text-light); line-height: 1.7; margin: 0; }

.product-attributes {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e8e8e8;
}
[data-theme="dark"] .product-attributes { border-color: #3a3a5a; }
.product-attributes h3 { margin-bottom: 16px; font-size: 1.1rem; }

.attributes-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.attribute-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    background: var(--bg-dark);
    border-radius: 8px;
}

.attr-label {
    color: var(--text-light);
    font-size: 0.9rem;
}

.attr-value {
    text-align: right;
}

/* Reviews Section */
.reviews-section {
    background: var(--bg-color);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e8e8e8;
}
[data-theme="dark"] .reviews-section { border-color: #3a3a5a; }
.reviews-section > h2 { margin-bottom: 20px; font-size: 1.3rem; }

.rating-summary {
    display: flex;
    gap: 32px;
    padding: 20px;
    background: var(--bg-dark);
    border-radius: 12px;
    margin-bottom: 24px;
}

.rating-score {
    text-align: center;
    min-width: 120px;
}

.score-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ff9900;
}

.score-stars { margin: 6px 0; }
.score-count { color: var(--text-light); font-size: 0.9rem; }

.rating-bars { flex: 1; }

.rating-bar-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}

.bar-label {
    width: 60px;
    font-size: 0.85rem;
    color: var(--text-light);
}

.bar-track {
    flex: 1;
    height: 8px;
    background: var(--border);
    border-radius: 4px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background: #f7971e;
    border-radius: 4px;
}

.bar-count {
    width: 30px;
    text-align: right;
    color: var(--text-light);
    font-size: 0.85rem;
}

/* User Review */
.user-review {
    padding: 20px;
    background: rgba(102, 126, 234, 0.08);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    margin-bottom: 20px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.review-stars {
    margin-top: 8px;
    color: #f7971e;
}
.review-stars span { margin-left: 8px; font-weight: 600; color: var(--text-primary); }

.review-text {
    margin: 12px 0;
    line-height: 1.6;
}

.review-images {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
}
.review-images img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border);
}

.review-pending {
    margin-top: 12px;
    padding: 8px 12px;
    background: rgba(247, 151, 30, 0.15);
    border-radius: 6px;
    display: inline-block;
    color: #f7971e;
    font-size: 0.85rem;
}

.review-date {
    color: var(--text-light);
}

/* Review Form */
.review-form {
    padding: 20px;
    background: var(--bg-dark);
    border-radius: 12px;
    margin-bottom: 20px;
}
.review-form h4 { margin-bottom: 16px; }
.review-form .form-group { margin-bottom: 16px; }
.review-form label { display: block; margin-bottom: 8px; }

.star-rating i {
    font-size: 1.5rem;
    cursor: pointer;
    color: #f7971e;
    transition: transform 0.1s;
}
.star-rating i:hover { transform: scale(1.2); }

.upload-area {
    border: 2px dashed var(--border);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s;
}
.upload-area:hover { border-color: #ff9900; }
.upload-area i { font-size: 2rem; color: var(--text-light); margin-bottom: 8px; }
.upload-area p { margin: 0; color: var(--text-light); }
.upload-area small { color: var(--text-light); }

.upload-preview {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.review-notice {
    padding: 16px;
    background: var(--bg-dark);
    border-radius: 8px;
    text-align: center;
    color: var(--text-light);
    margin-bottom: 20px;
}

/* Reviews List */
.reviews-list {
    border-top: 1px solid var(--border);
}

.review-item {
    padding: 20px 0;
    border-bottom: 1px solid var(--border);
}
.review-item:last-child { border-bottom: none; }

.reviewer-info {
    display: flex;
    gap: 14px;
    margin-bottom: 12px;
}

.reviewer-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff9900, #e68a00);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.reviewer-details { flex: 1; }

.reviewer-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 10px;
}

.reviewer-name strong { display: block; margin-bottom: 4px; }

.verified-badge {
    display: inline-block;
    background: var(--bg-dark);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    color: var(--text-light);
    margin-left: 8px;
}
.verified-badge i { color: #38ef7d; }

.reviewer-stars {
    margin-top: 4px;
    color: #f7971e;
    font-size: 0.9rem;
}
.reviewer-stars span { margin-left: 8px; font-weight: 600; color: var(--text-primary); }

.review-date-info {
    text-align: right;
    color: var(--text-light);
}
.review-date-info small { display: block; }

.review-content {
    margin: 0 0 12px 62px;
    line-height: 1.7;
    font-size: 0.95rem;
}

.review-photos {
    margin-left: 62px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.review-photos img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border);
    cursor: pointer;
    transition: transform 0.2s;
}
.review-photos img:hover { transform: scale(1.05); }

.no-reviews {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-light);
}
.no-reviews i {
    font-size: 3rem;
    opacity: 0.4;
    margin-bottom: 12px;
}
.no-reviews p { margin: 0; }

/* Similar Products */
.similar-products {
    margin-bottom: 24px;
}
.similar-products .section-header {
    margin-bottom: 20px;
}
.similar-products h2 {
    font-size: 1.3rem;
    margin: 0;
}

@media (max-width: 768px) {
    .product-detail { padding: 16px; }
    .product-gallery .main-image { height: 300px; }
    .product-title { font-size: 1.4rem; }
    .product-price-large { font-size: 1.6rem; }
    .attributes-grid { grid-template-columns: 1fr; }
    .rating-summary { flex-direction: column; gap: 20px; }
    .rating-score { min-width: auto; }
    .review-content { margin-left: 0; margin-top: 12px; }
    .review-photos { margin-left: 0; }
}
</style>
@endpush

@push('scripts')
<script>
let currentImageIndex = 0;
let lightboxIndex = 0;
const thumbnails = document.querySelectorAll('.thumbnail');
const totalImages = thumbnails.length || 1;

// Tüm resim URL'lerini topla
const imageUrls = [];
@if($urun->resimler->count() > 0)
    @foreach($urun->resimler as $resim)
        imageUrls.push("{{ asset('serve-image.php?p=urunler/' . $resim->resim) }}");
    @endforeach
@elseif($urun->resim)
    imageUrls.push("{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}");
@endif

function changeMainImage(thumbnail, index) {
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        mainImage.src = thumbnail.src;
    }
    currentImageIndex = index;
    updateThumbnailActive(index);
}

function updateThumbnailActive(index) {
    thumbnails.forEach(img => img.classList.remove('active'));
    if (thumbnails[index]) {
        thumbnails[index].classList.add('active');
    }
}

function nextImage() {
    if (totalImages === 0) return;
    currentImageIndex = (currentImageIndex + 1) % totalImages;
    const thumbnail = thumbnails[currentImageIndex];
    if (thumbnail) changeMainImage(thumbnail, currentImageIndex);
}

function prevImage() {
    if (totalImages === 0) return;
    currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
    const thumbnail = thumbnails[currentImageIndex];
    if (thumbnail) changeMainImage(thumbnail, currentImageIndex);
}

// Lightbox functions
function openLightbox(index) {
    if (imageUrls.length === 0) return;
    lightboxIndex = index || 0;
    updateLightboxImage();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event && event.target !== event.currentTarget && !event.target.classList.contains('lightbox-close')) {
        return;
    }
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}

function updateLightboxImage() {
    const lightboxImg = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    const total = document.getElementById('lightboxTotal');

    lightboxImg.src = imageUrls[lightboxIndex];
    counter.textContent = lightboxIndex + 1;
    total.textContent = imageUrls.length;
}

function lightboxNext(event) {
    event.stopPropagation();
    if (imageUrls.length <= 1) return;
    lightboxIndex = (lightboxIndex + 1) % imageUrls.length;
    updateLightboxImage();
}

function lightboxPrev(event) {
    event.stopPropagation();
    if (imageUrls.length <= 1) return;
    lightboxIndex = (lightboxIndex - 1 + imageUrls.length) % imageUrls.length;
    updateLightboxImage();
}

document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox');
    const isLightboxOpen = lightbox.classList.contains('active');

    if (isLightboxOpen) {
        if (e.key === 'ArrowRight') lightboxNext(e);
        else if (e.key === 'ArrowLeft') lightboxPrev(e);
        else if (e.key === 'Escape') closeLightbox({ target: lightbox, currentTarget: lightbox });
    } else {
        if (e.key === 'ArrowRight') nextImage();
        else if (e.key === 'ArrowLeft') prevImage();
    }
});

// Star Rating
document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('starRating');
    const puanInput = document.getElementById('puanInput');

    if (starRating) {
        const stars = starRating.querySelectorAll('i');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                puanInput.value = rating;
                stars.forEach((s, index) => {
                    s.classList.toggle('fas', index < rating);
                    s.classList.toggle('far', index >= rating);
                });
            });

            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, index) => {
                    s.classList.toggle('fas', index < rating);
                    s.classList.toggle('far', index >= rating);
                });
            });
        });

        starRating.addEventListener('mouseleave', function() {
            const currentRating = puanInput.value || 0;
            stars.forEach((s, index) => {
                s.classList.toggle('fas', index < currentRating);
                s.classList.toggle('far', index >= currentRating);
            });
        });
    }

    // Image preview for reviews
    const yorumResimler = document.getElementById('yorumResimler');
    const yorumOnizleme = document.getElementById('yorumResimOnizleme');

    if (yorumResimler && yorumOnizleme) {
        yorumResimler.addEventListener('change', function() {
            yorumOnizleme.innerHTML = '';

            if (this.files.length > 5) {
                showToast('En fazla 5 fotoğraf yükleyebilirsiniz.', 'warning');
                this.value = '';
                return;
            }

            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.style.cssText = 'position: relative; width: 70px; height: 70px;';
                    div.innerHTML = `
                        <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        <span style="position: absolute; top: -6px; right: -6px; background: #e74c3c; color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; cursor: pointer;" onclick="this.parentElement.remove()">×</span>
                    `;
                    yorumOnizleme.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endpush
@endsection
