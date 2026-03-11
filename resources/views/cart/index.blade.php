@extends('layouts.app')

@section('title', 'Sepetim - ZAMASON')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Sepetim</h2>

            @if($sepetItems->isEmpty())
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h4>Sepetiniz Boş</h4>
                        <p class="text-muted">Henüz sepetinize ürün eklememişsiniz.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    <!-- Sepet Ürünleri -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><strong>{{ $sepetItems->count() }}</strong> Ürün</span>
                                <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" id="sepetiTemizleForm">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="sepetiTemizle()">
                                        <i class="fas fa-trash"></i> Sepeti Temizle
                                    </button>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                @foreach($sepetItems as $item)
                                    <div class="sepet-item p-3 border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                @if($item->urun->resim)
                                                    <img src="{{ asset('serve-image.php?p=urunler/' . $item->urun->resim) }}"
                                                         class="img-fluid rounded"
                                                         alt="{{ $item->urun->urun_adi }}"
                                                         style="max-height: 100px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                                                        <i class="fas fa-image fa-2x text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-5">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('products.show', $item->urun) }}" class="text-decoration-none">
                                                        {{ $item->urun->urun_adi }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    Satıcı: {{ $item->urun->satici->ad_soyad }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <a href="{{ route('chat.show', $item->konusma) }}">
                                                        <i class="fas fa-comments"></i> Konuşmayı Gör
                                                    </a>
                                                </small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <span class="badge bg-success fs-6">{{ $item->formatli_tutar }}</span>
                                                <br>
                                                <small class="text-muted">Anlaşılan fiyat</small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <form action="{{ route('cart.remove', $item) }}" method="POST" class="sepet-cikar-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger sepet-cikar-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sepet Özeti -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <strong>Sipariş Özeti</strong>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ürün Toplamı:</span>
                                    <span>{{ number_format($toplamTutar, 2, ',', '.') }} TL</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tahmini Kargo:</span>
                                    <span>{{ number_format(config('zamason.varsayilan_kargo_ucreti', 50), 2, ',', '.') }} TL</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Toplam:</strong>
                                    <strong class="text-success fs-5">
                                        {{ number_format($toplamTutar + config('zamason.varsayilan_kargo_ucreti', 50), 2, ',', '.') }} TL
                                    </strong>
                                </div>
                                <a href="{{ route('payment.checkout') }}" class="btn btn-success w-100">
                                    <i class="fas fa-credit-card"></i> Ödemeye Geç
                                </a>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h6><i class="fas fa-shield-alt text-success"></i> Güvenli Ödeme</h6>
                                <small class="text-muted">
                                    Ödemeleriniz iyzico altyapısı ile güvenli bir şekilde işlenir.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
// Sepeti temizle
async function sepetiTemizle() {
    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Sepeti Temizle',
        message: 'Sepeti temizlemek istediğinizden emin misiniz? Tüm ürünler kaldırılacak.',
        confirmText: 'Evet, Temizle',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('sepetiTemizleForm').submit();
    }
}

// Sepetten ürün çıkarma
document.querySelectorAll('.sepet-cikar-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.sepet-cikar-form');

        const confirmed = await showConfirm({
            type: 'warning',
            title: 'Ürünü Çıkar',
            message: 'Ürünü sepetten çıkarmak istediğinizden emin misiniz?',
            confirmText: 'Çıkar',
            cancelText: 'Vazgeç'
        });

        if (confirmed) {
            form.submit();
        }
    });
});
</script>
@endpush
@push('styles')
<style>
/* Dark Mode Sepet Düzeltmeleri */
[data-theme="dark"] .card {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .card-header {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .card-body {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sepet-item {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .sepet-item h6,
[data-theme="dark"] .sepet-item h6 a {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sepet-item a {
    color: #ffa726 !important;
}

[data-theme="dark"] .sepet-item a:hover {
    color: #ffb74d !important;
}

[data-theme="dark"] .sepet-item .text-muted,
[data-theme="dark"] .sepet-item small.text-muted {
    color: #a0a0a0 !important;
}

[data-theme="dark"] h2,
[data-theme="dark"] h4,
[data-theme="dark"] h6 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] span,
[data-theme="dark"] strong {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .text-muted {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .border-bottom {
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .bg-secondary {
    background: #2d2d44 !important;
}
</style>
@endpush
@endsection
