@extends('layouts.app')

@section('title', 'Siparislerim - ZAMASON')

@section('content')
<div class="siparislerim-page">
    <div class="page-header">
        <h1><i class="fas fa-shopping-bag"></i> Siparislerim</h1>
    </div>

    <!-- Tab Buttons -->
    <div class="siparis-tabs">
        <button class="tab-btn active" onclick="switchTab('aldiklarim')">
            <i class="fas fa-download"></i> Satin Aldiklarim
            @if($aliciOdemeleri->total() > 0)
                <span class="tab-badge">{{ $aliciOdemeleri->total() }}</span>
            @endif
        </button>
        <button class="tab-btn" onclick="switchTab('sattiklarim')">
            <i class="fas fa-upload"></i> Sattiklarim
            @if($saticiOdemeleri->total() > 0)
                <span class="tab-badge success">{{ $saticiOdemeleri->total() }}</span>
            @endif
        </button>
    </div>

    <!-- Satin Aldiklarim Tab -->
    <div class="tab-content active" id="tab-aldiklarim">
        @if($aliciOdemeleri->isEmpty())
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>Henuz bir satin alma isleminiz yok</h3>
                <p>Urunleri kesfedip alisverise baslayin!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Urunleri Kesfet
                </a>
            </div>
        @else
            <div class="siparis-list">
                @foreach($aliciOdemeleri as $odeme)
                    <div class="siparis-card">
                        <div class="siparis-header">
                            <div class="siparis-id">
                                <span class="label">Siparis No</span>
                                <span class="value">#{{ $odeme->id }}</span>
                            </div>
                            <div class="siparis-tarih">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $odeme->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <div class="siparis-body">
                            <div class="siparis-urun">
                                @if($odeme->urun->resim)
                                    <img src="{{ asset('serve-image.php?p=urunler/' . $odeme->urun->resim) }}" alt="{{ $odeme->urun->urun_adi }}">
                                @else
                                    <div class="urun-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <div class="urun-info">
                                    <a href="{{ route('products.show', $odeme->urun) }}" class="urun-adi">
                                        {{ Str::limit($odeme->urun->urun_adi, 50) }}
                                    </a>
                                    <div class="satici-info">
                                        <i class="fas fa-store"></i> {{ $odeme->satici->ad_soyad }}
                                    </div>
                                </div>
                            </div>

                            <div class="siparis-detay">
                                <div class="detay-item">
                                    <span class="label">Urun Tutari</span>
                                    <span class="value">{{ number_format($odeme->urun_tutari, 2, ',', '.') }} TL</span>
                                </div>
                                <div class="detay-item">
                                    <span class="label">Kargo</span>
                                    <span class="value">{{ number_format($odeme->kargo_tutari, 2, ',', '.') }} TL</span>
                                </div>
                                <div class="detay-item toplam">
                                    <span class="label">Toplam</span>
                                    <span class="value">{{ $odeme->formatli_toplam }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="siparis-footer">
                            <div class="siparis-durum durum-{{ $odeme->durum }}">
                                @if($odeme->durum === 'odendi')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($odeme->durum === 'beklemede')
                                    <i class="fas fa-clock"></i>
                                @elseif($odeme->durum === 'basarisiz')
                                    <i class="fas fa-times-circle"></i>
                                @else
                                    <i class="fas fa-info-circle"></i>
                                @endif
                                {{ $odeme->durum_metni }}
                            </div>
                            <div class="siparis-actions">
                                @if($odeme->konusma)
                                    <a href="{{ route('chat.show', $odeme->konusma) }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-comments"></i> Mesajlar
                                    </a>
                                @endif
                                @if($odeme->kargo)
                                    <a href="{{ route('shipping.show', $odeme->kargo) }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-truck"></i> Kargo Takip
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($aliciOdemeleri->hasPages())
                <div class="pagination-wrapper">
                    {{ $aliciOdemeleri->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Sattiklarim Tab -->
    <div class="tab-content" id="tab-sattiklarim" style="display: none;">
        @if($saticiOdemeleri->isEmpty())
            <div class="empty-state">
                <i class="fas fa-store"></i>
                <h3>Henuz bir satis isleminiz yok</h3>
                <p>Urunlerinizi satin ve kazanmaya baslayin!</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Urun Ekle
                </a>
            </div>
        @else
            <div class="siparis-list">
                @foreach($saticiOdemeleri as $odeme)
                    <div class="siparis-card satici-card">
                        <div class="siparis-header">
                            <div class="siparis-id">
                                <span class="label">Satis No</span>
                                <span class="value">#{{ $odeme->id }}</span>
                            </div>
                            <div class="siparis-tarih">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $odeme->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <div class="siparis-body">
                            <div class="siparis-urun">
                                @if($odeme->urun->resim)
                                    <img src="{{ asset('serve-image.php?p=urunler/' . $odeme->urun->resim) }}" alt="{{ $odeme->urun->urun_adi }}">
                                @else
                                    <div class="urun-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <div class="urun-info">
                                    <a href="{{ route('products.show', $odeme->urun) }}" class="urun-adi">
                                        {{ Str::limit($odeme->urun->urun_adi, 50) }}
                                    </a>
                                    <div class="alici-info">
                                        <i class="fas fa-user"></i> {{ $odeme->alici->ad_soyad }}
                                    </div>
                                </div>
                            </div>

                            <div class="siparis-detay">
                                <div class="detay-item">
                                    <span class="label">Satis Tutari</span>
                                    <span class="value">{{ $odeme->formatli_toplam }}</span>
                                </div>
                                <div class="detay-item">
                                    <span class="label">Komisyon</span>
                                    <span class="value" style="color: var(--danger);">-{{ number_format($odeme->komisyon_tutari, 2, ',', '.') }} TL</span>
                                </div>
                                <div class="detay-item kazanc">
                                    <span class="label">Net Kazanc</span>
                                    <span class="value">{{ number_format($odeme->satici_tutari, 2, ',', '.') }} TL</span>
                                </div>
                            </div>
                        </div>

                        <div class="siparis-footer">
                            <div class="siparis-durum durum-{{ $odeme->durum }}">
                                @if($odeme->durum === 'odendi')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($odeme->durum === 'beklemede')
                                    <i class="fas fa-clock"></i>
                                @elseif($odeme->durum === 'basarisiz')
                                    <i class="fas fa-times-circle"></i>
                                @else
                                    <i class="fas fa-info-circle"></i>
                                @endif
                                {{ $odeme->durum_metni }}
                            </div>
                            <div class="siparis-actions">
                                @if($odeme->konusma)
                                    <a href="{{ route('chat.show', $odeme->konusma) }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-comments"></i> Mesajlar
                                    </a>
                                @endif
                                @if($odeme->durum === 'odendi' && !$odeme->kargo && $odeme->konusma)
                                    <a href="{{ route('shipping.create', $odeme->konusma->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-truck"></i> Kargo Olustur
                                    </a>
                                @elseif($odeme->kargo)
                                    <a href="{{ route('shipping.show', $odeme->kargo) }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-truck"></i> Kargo Detay
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($saticiOdemeleri->hasPages())
                <div class="pagination-wrapper">
                    {{ $saticiOdemeleri->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<style>
.siparislerim-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header h1 i {
    color: var(--primary);
}

/* Tab Buttons */
.siparis-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid var(--border);
    padding-bottom: 0;
}

.tab-btn {
    background: none;
    border: none;
    padding: 12px 20px;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-light);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.tab-btn:hover {
    color: var(--text);
}

.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-badge {
    background: var(--primary);
    color: white;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
}

.tab-badge.success {
    background: var(--success);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--card-bg);
    border-radius: 16px;
    border: 1px solid var(--border);
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-light);
    opacity: 0.5;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--text);
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.empty-state p {
    color: var(--text-light);
    margin-bottom: 20px;
}

/* Siparis Cards */
.siparis-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.siparis-card {
    background: var(--card-bg);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    transition: all 0.2s;
}

.siparis-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.siparis-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
}

.siparis-id .label {
    font-size: 0.8rem;
    color: var(--text-light);
    display: block;
}

.siparis-id .value {
    font-weight: 700;
    color: var(--primary);
    font-size: 1.1rem;
}

.siparis-tarih {
    color: var(--text-light);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.siparis-body {
    padding: 20px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.siparis-urun {
    display: flex;
    gap: 15px;
    flex: 1;
    min-width: 250px;
}

.siparis-urun img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--border);
}

.urun-placeholder {
    width: 80px;
    height: 80px;
    background: var(--bg);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 1.5rem;
}

.urun-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.urun-adi {
    font-weight: 600;
    color: var(--text);
    text-decoration: none;
    font-size: 1rem;
    line-height: 1.4;
    display: block;
    margin-bottom: 8px;
}

.urun-adi:hover {
    color: var(--primary);
}

.satici-info, .alici-info {
    font-size: 0.85rem;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 6px;
}

.siparis-detay {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 180px;
}

.detay-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
}

.detay-item .label {
    color: var(--text-light);
}

.detay-item .value {
    color: var(--text);
    font-weight: 500;
}

.detay-item.toplam {
    padding-top: 8px;
    border-top: 1px dashed var(--border);
    margin-top: 4px;
}

.detay-item.toplam .value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary);
}

.detay-item.kazanc .value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--success);
}

.siparis-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: var(--bg);
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 10px;
}

.siparis-durum {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 6px 12px;
    border-radius: 20px;
}

.siparis-durum.durum-odendi {
    background: rgba(46, 204, 113, 0.15);
    color: #27ae60;
}

.siparis-durum.durum-beklemede {
    background: rgba(241, 196, 15, 0.15);
    color: #f39c12;
}

.siparis-durum.durum-basarisiz {
    background: rgba(231, 76, 60, 0.15);
    color: #e74c3c;
}

.siparis-durum.durum-iptal {
    background: rgba(149, 165, 166, 0.15);
    color: #7f8c8d;
}

.siparis-actions {
    display: flex;
    gap: 10px;
}

.siparis-actions .btn {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Satici Card Special */
.satici-card .siparis-header {
    background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.05));
}

/* Pagination */
.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .siparislerim-page {
        padding: 15px;
    }

    .siparis-tabs {
        flex-direction: column;
        gap: 0;
    }

    .tab-btn {
        width: 100%;
        justify-content: center;
        border-bottom: none;
        border-left: 3px solid transparent;
        margin-bottom: 0;
        margin-left: -1px;
    }

    .tab-btn.active {
        border-bottom: none;
        border-left-color: var(--primary);
        background: var(--bg);
    }

    .siparis-body {
        flex-direction: column;
    }

    .siparis-urun {
        min-width: 100%;
    }

    .siparis-detay {
        width: 100%;
        padding-top: 15px;
        border-top: 1px solid var(--border);
    }

    .siparis-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .siparis-actions {
        justify-content: stretch;
    }

    .siparis-actions .btn {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script>
function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active');
    });

    // Show selected tab
    document.getElementById('tab-' + tab).style.display = 'block';

    // Add active to clicked button
    event.currentTarget.classList.add('active');
}
</script>
@endsection
