@extends('layouts.app')

@section('title', 'Satıcı Paneli - ' . config('app.name'))

@section('content')
<h2 class="section-title">
    <i class="fas fa-tachometer-alt"></i> Satıcı Paneli
</h2>

<!-- Ana İstatistikler -->
<div class="seller-stats">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info" >
            <h4>Toplam Ürün</h4>
            <p>{{ $urunler->count() }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h4>Toplam Satış</h4>
            <p>{{ $toplamSatis }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-lira-sign"></i>
        </div>
        <div class="stat-info">
            <h4>Toplam Ciro</h4>
            <p>{{ number_format($toplamCiro, 2, ',', '.') }} TL</p>
        </div>
    </div>
    <a href="{{ route('chat.index') }}" class="stat-card clickable {{ $bekleyenTeklifler > 0 ? 'highlight' : '' }}">
        <div class="stat-icon {{ $bekleyenTeklifler > 0 ? 'orange' : 'yellow' }}">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <div class="stat-info">
            <h4>Bekleyen Teklifler</h4>
            <p>{{ $bekleyenTeklifler }}</p>
        </div>
        @if($bekleyenTeklifler > 0)
            <span class="badge-notification">{{ $bekleyenTeklifler }}</span>
        @endif
    </a>
</div>

<!-- Detaylı İstatistikler -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <!-- Satış Grafiği -->
        <div class="card" style="padding: 20px; height: 100%;">
            <h4 style="margin-bottom: 20px;"><i class="fas fa-chart-line"></i> Son 6 Ay Satış Grafiği</h4>
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <!-- Performans Göstergeleri -->
        <div class="card" style="padding: 20px; height: 100%;">
            <h4 style="margin-bottom: 20px;"><i class="fas fa-chart-pie"></i> Performans</h4>

            <div class="performance-item">
                <div class="performance-icon" style="background: linear-gradient(135deg, #ff9900, #e68a00);">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="performance-info">
                    <span class="performance-label">Toplam Görüntülenme</span>
                    <span class="performance-value">{{ number_format($toplamGoruntulenme) }}</span>
                </div>
            </div>

            <div class="performance-item">
                <div class="performance-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="performance-info">
                    <span class="performance-label">Favorilere Eklenme</span>
                    <span class="performance-value">{{ number_format($favoriSayisi) }}</span>
                </div>
            </div>

            <div class="performance-item">
                <div class="performance-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="performance-info">
                    <span class="performance-label">Ortalama Puan</span>
                    <span class="performance-value">
                        @if($ortalamaPuan)
                            {{ $ortalamaPuan }} <small style="color: var(--text-light);">({{ $toplamYorum }} yorum)</small>
                        @else
                            <small style="color: var(--text-light);">Henüz yorum yok</small>
                        @endif
                    </span>
                </div>
            </div>

            <div class="performance-item">
                <div class="performance-icon" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="performance-info">
                    <span class="performance-label">Aktif Sohbetler</span>
                    <span class="performance-value">{{ $aktifKonusmalar }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- En Çok Görüntülenen Ürünler -->
    <div class="col-md-6">
        <div class="card" style="padding: 20px;">
            <h4 style="margin-bottom: 15px;"><i class="fas fa-fire"></i> En Çok Görüntülenen Ürünler</h4>
            @if($enCokGoruntulenler->count() > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($enCokGoruntulenler as $urun)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border);">
                            <a href="{{ route('products.show', $urun) }}" style="text-decoration: none; color: var(--text);">
                                {{ Str::limit($urun->urun_adi, 30) }}
                            </a>
                            <span style="color: var(--text-light);">
                                <i class="fas fa-eye"></i> {{ number_format($urun->goruntulenme_sayisi) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="color: var(--text-light); text-align: center; padding: 20px;">
                    Henüz görüntülenme verisi yok
                </p>
            @endif
        </div>
    </div>

    <!-- Son Siparişler -->
    <div class="col-md-6">
        <div class="card" style="padding: 20px;">
            <h4 style="margin-bottom: 15px;"><i class="fas fa-shopping-bag"></i> Son Siparişler</h4>
            @if($sonSiparisler->count() > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($sonSiparisler as $siparis)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border);">
                            <div>
                                <strong>{{ $siparis->alici->ad ?? 'Bilinmiyor' }} {{ Str::limit($siparis->alici->soyad ?? '', 1) }}.</strong>
                                <br>
                                <small style="color: var(--text-light);">{{ Str::limit($siparis->urun->urun_adi ?? '-', 25) }}</small>
                            </div>
                            <div style="text-align: right;">
                                <span style="color: var(--primary); font-weight: 600;">{{ number_format($siparis->toplam_tutar, 2, ',', '.') }} TL</span>
                                <br>
                                <small style="color: var(--text-light);">{{ $siparis->created_at->diffForHumans() }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="color: var(--text-light); text-align: center; padding: 20px;">
                    Henüz sipariş yok
                </p>
            @endif
        </div>
    </div>
</div>

<!-- Hızlı Eylemler -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3><i class="fas fa-list"></i> Ürünlerim</h3>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('chat.index') }}" class="btn btn-success">
            <i class="fas fa-comments"></i> Mesajlarım
            @if($okunmamisMesajlar > 0)
                <span class="btn-badge">{{ $okunmamisMesajlar }}</span>
            @endif
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ürün Ekle
        </a>
    </div>
</div>

<!-- Ürün Listesi -->
@if($urunler->count() == 0)
    <div class="empty-cart">
        <i class="fas fa-box-open"></i>
        <h3>Henüz ürün eklemediniz</h3>
        <p>İlk ürününüzü ekleyerek satışa başlayın.</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Ürün Ekle</a>
    </div>
@else
    <div class="products-table">
        <table>
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Görüntüleme</th>
                    <th>Durum</th>
                    <th>Satış</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($urunler as $urun)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <div class="product-thumb">
                                    @if($urun->resim)
                                        <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}" alt="">
                                    @else
                                        <i class="fas fa-image" style="color: var(--text-light);"></i>
                                    @endif
                                </div>
                                <span>{{ Str::limit($urun->urun_adi, 25) }}</span>
                            </div>
                        </td>
                        <td>{{ $urun->kategori?->kategori_adi ?? 'Genel' }}</td>
                        <td><strong>{{ $urun->formatli_fiyat }}</strong></td>
                        <td>
                            <span class="{{ $urun->stok < 5 ? 'text-danger' : '' }}">
                                {{ $urun->stok }} adet
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-eye" style="color: var(--text-light);"></i>
                            {{ number_format($urun->goruntulenme_sayisi) }}
                        </td>
                        <td>
                            <span class="status-badge {{ $urun->durum }}">
                                {{ $urun->durum === 'aktif' ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('products.toggle-sold', $urun) }}" method="POST" style="display: inline;">
                                @csrf
                                <select name="satildi" onchange="this.form.submit()" class="satis-dropdown {{ $urun->satildi ? 'satildi' : 'satista' }}">
                                    <option value="0" {{ !$urun->satildi ? 'selected' : '' }}>Satışta</option>
                                    <option value="1" {{ $urun->satildi ? 'selected' : '' }}>Satıldı</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('products.show', $urun) }}" target="_blank">
                                    <button class="view-btn" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </a>
                                <a href="{{ route('products.edit', $urun) }}">
                                    <button class="edit-btn" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </a>
                                <form action="{{ route('products.destroy', $urun) }}" method="POST" style="display:inline;" class="urun-sil-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-btn urun-sil-btn" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<style>
.performance-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.performance-item:last-child {
    border-bottom: none;
}
.performance-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}
.performance-info {
    flex: 1;
}
.performance-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-light);
}
.performance-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text);
}
.view-btn {
    background: var(--info);
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
}
.view-btn:hover {
    opacity: 0.8;
}

/* Dark Mode Fixes */
[data-theme="dark"] .card,
[data-theme="dark"] .stat-card {
    background: var(--card-bg, #1e1e2e) !important;
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] .card h4,
[data-theme="dark"] .stat-card h4,
[data-theme="dark"] .stat-info h4,
[data-theme="dark"] .stat-info p,
[data-theme="dark"] .section-title {
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] .performance-value {
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] .performance-label {
    color: var(--text-light, #a0a0a0) !important;
}
[data-theme="dark"] .products-table table {
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] .products-table th {
    color: var(--text, #e0e0e0) !important;
    background: var(--card-bg, #1e1e2e) !important;
}
[data-theme="dark"] .products-table td {
    color: var(--text, #e0e0e0) !important;
    border-color: var(--border, #333) !important;
}
[data-theme="dark"] .products-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05) !important;
}
[data-theme="dark"] .product-cell span,
[data-theme="dark"] .products-table a {
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] .satis-dropdown {
    background: var(--card-bg, #1e1e2e) !important;
    color: var(--text, #e0e0e0) !important;
    border-color: var(--border, #333) !important;
}
[data-theme="dark"] ul li {
    border-color: var(--border, #333) !important;
}
[data-theme="dark"] ul li a,
[data-theme="dark"] ul li strong {
    color: var(--text, #e0e0e0) !important;
}
[data-theme="dark"] h3 {
    color: var(--text, #e0e0e0) !important;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Satış grafiği
const grafikVerileri = @json($grafikVerileri);
const ctx = document.getElementById('salesChart');

if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grafikVerileri.map(item => item.ay),
            datasets: [
                {
                    label: 'Satış Adedi',
                    data: grafikVerileri.map(item => item.adet),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                    yAxisID: 'y',
                },
                {
                    label: 'Ciro (TL)',
                    data: grafikVerileri.map(item => item.tutar),
                    type: 'line',
                    borderColor: 'rgba(118, 75, 162, 1)',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Satış Adedi'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Ciro (TL)'
                    }
                },
            }
        }
    });
}

async function deleteProduct(id) {
    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Ürünü Sil',
        message: 'Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
        confirmText: 'Evet, Sil',
        cancelText: 'Vazgeç'
    });

    if (!confirmed) return;

    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`{{ url('seller/product') }}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Ürün silindi!', 'success');
            location.reload();
        } else {
            showToast(data.error || 'Silme işlemi başarısız oldu.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Bir hata oluştu.', 'error');
    });
}

// Ürün silme butonları için event listener
document.querySelectorAll('.urun-sil-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.urun-sil-form');

        const confirmed = await showConfirm({
            type: 'danger',
            title: 'Ürünü Sil',
            message: 'Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
            confirmText: 'Evet, Sil',
            cancelText: 'Vazgeç'
        });

        if (confirmed) {
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
