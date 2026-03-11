@extends('admin.layouts.app')

@section('title', 'Kontrol Paneli')
@section('page-title', 'Kontrol Paneli')

@section('content')
<!-- İstatistik Kartları -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value">{{ $kullaniciStats['toplam'] }}</div>
                    <div class="label">Toplam Kullanıcı</div>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <hr>
            <small class="text-muted">
                <i class="fas fa-user-plus me-1"></i> Bugün: {{ $kullaniciStats['bugun_kayit'] }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value">{{ $urunStats['toplam'] }}</div>
                    <div class="label">Toplam Ürün</div>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <hr>
            <small class="text-muted">
                <i class="fas fa-check-circle me-1"></i> Aktif: {{ $urunStats['aktif'] }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value">{{ $teklifStats['toplam'] }}</div>
                    <div class="label">Toplam Teklif</div>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <hr>
            <small class="text-muted">
                <i class="fas fa-clock me-1"></i> Bekleyen: {{ $teklifStats['beklemede'] }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card red">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value">{{ $konusmaStats['toplam'] }}</div>
                    <div class="label">Toplam Konuşma</div>
                </div>
                <div class="icon">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <hr>
            <small class="text-muted">
                <i class="fas fa-comment me-1"></i> Aktif: {{ $konusmaStats['aktif'] }}
            </small>
        </div>
    </div>
</div>

<!-- Gelir ve Ödeme İstatistikleri -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-white">{{ number_format($odemeStats['toplam_gelir'], 0, ',', '.') }} ₺</div>
                    <div class="label text-white-50">Toplam Gelir</div>
                </div>
                <div class="icon text-white-50">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <small class="text-white-50">
                <i class="fas fa-calendar me-1"></i> Bu Ay: {{ number_format($odemeStats['bu_ay_gelir'], 0, ',', '.') }} ₺
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: #ff9900;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-white">{{ number_format($odemeStats['komisyon_geliri'], 0, ',', '.') }} ₺</div>
                    <div class="label text-white-50">Komisyon Geliri</div>
                </div>
                <div class="icon text-white-50">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <small class="text-white-50">
                <i class="fas fa-chart-pie me-1"></i> %{{ config('zamason.komisyon_orani', 5) }} oran
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-white">{{ $odemeStats['odendi'] }}</div>
                    <div class="label text-white-50">Başarılı Ödeme</div>
                </div>
                <div class="icon text-white-50">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <small class="text-white-50">
                <i class="fas fa-clock me-1"></i> Bekleyen: {{ $odemeStats['beklemede'] }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-white">{{ number_format($odemeStats['bugun_gelir'], 0, ',', '.') }} ₺</div>
                    <div class="label text-white-50">Bugünkü Gelir</div>
                </div>
                <div class="icon text-white-50">
                    <i class="fas fa-sun"></i>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <small class="text-white-50">
                <i class="fas fa-times-circle me-1"></i> İptal/İade: {{ $odemeStats['iptal'] }}
            </small>
        </div>
    </div>
</div>

<!-- Hızlı Erişim -->
<div class="card card-custom mb-4">
    <div class="card-header">
        <i class="fas fa-th-large me-2"></i>Hızlı Erişim
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Kullanıcılar -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.users.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="qa-label">Kullanıcılar</span>
                </a>
            </div>
            <!-- Ürünler -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.products.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-success">
                        <i class="fas fa-box"></i>
                    </div>
                    <span class="qa-label">Ürünler</span>
                </a>
            </div>
            <!-- Yorumlar -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.reviews.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-warning">
                        <i class="fas fa-comments"></i>
                    </div>
                    <span class="qa-label">Yorumlar</span>
                </a>
            </div>
            <!-- Slaytlar -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.sliders.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-info">
                        <i class="fas fa-images"></i>
                    </div>
                    <span class="qa-label">Slaytlar</span>
                </a>
            </div>
            <!-- IP Yönetimi -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.ip.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-danger">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span class="qa-label">IP Yönetimi</span>
                </a>
            </div>
            <!-- Ayarlar -->
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.settings.index') }}" class="quick-access-card text-decoration-none">
                    <div class="qa-icon bg-secondary">
                        <i class="fas fa-cog"></i>
                    </div>
                    <span class="qa-label">Ayarlar</span>
                </a>
            </div>
        </div>

        <!-- Liste Yönetimi (Dropdown tarzı) -->
        <hr class="my-3">
        <div class="row g-3">
            <div class="col-12">
                <h6 class="text-muted mb-3"><i class="fas fa-list me-2"></i>Liste Yönetimi</h6>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.lists.ip') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-network-wired me-2"></i>IP Listesi
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.lists.keywords') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-ban me-2"></i>Yasaklı Kelimeler
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.lists.domains') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-at me-2"></i>E-posta Domainleri
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.lists.blocks') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-user-slash me-2"></i>Kullanıcı Engelleri
                </a>
            </div>
        </div>

        <!-- Diğer Yönetim -->
        <hr class="my-3">
        <div class="row g-3">
            @can('manage_roles')
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.roles.index') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-user-tag me-2"></i>Rol & Yetkiler
                </a>
            </div>
            @endcan
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.activities.index') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-history me-2"></i>Aktivite Logları
                </a>
            </div>
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.ip.bans') }}" class="quick-access-card-sm text-decoration-none">
                    <i class="fas fa-gavel me-2"></i>IP Banları
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Detaylı İstatistikler -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-2"></i>Kullanıcı Dağılımı</span>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary">{{ $kullaniciStats['alici'] }}</h4>
                        <small class="text-muted">Alıcı</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ $kullaniciStats['satici'] }}</h4>
                        <small class="text-muted">Satıcı</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger">{{ $kullaniciStats['banli'] }}</h4>
                        <small class="text-muted">Banlı</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Bu Hafta Kayıt</span>
                    <strong>{{ $kullaniciStats['bu_hafta_kayit'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box me-2"></i>Ürün Durumu</span>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-success">{{ $urunStats['aktif'] }}</h4>
                        <small class="text-muted">Aktif</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ $urunStats['pasif'] }}</h4>
                        <small class="text-muted">Pasif</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info">{{ $urunStats['satildi'] }}</h4>
                        <small class="text-muted">Satıldı</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Stokta</span>
                    <strong>{{ $urunStats['stokta'] }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- En Çok Satanlar ve Popüler Ürünler -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-trophy text-warning me-2"></i>En Çok Satan Satıcılar (30 Gün)</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Satıcı</th>
                            <th class="text-center">Satış</th>
                            <th class="text-end">Ciro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enCokSatanSaticilar as $index => $satici)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger') }} me-1">{{ $index + 1 }}</span>
                                    @else
                                        <span class="text-muted me-2">{{ $index + 1 }}.</span>
                                    @endif
                                    <strong>{{ $satici->ad }} {{ $satici->soyad }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $satici->satis_sayisi }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ number_format($satici->toplam_satis, 0, ',', '.') }} ₺</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Henüz satış yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-fire text-danger me-2"></i>En Popüler Ürünler</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th class="text-center">Görüntülenme</th>
                            <th class="text-end">Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enPopulerUrunler as $index => $urun)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge bg-{{ $index == 0 ? 'danger' : ($index == 1 ? 'warning' : 'info') }} me-1">{{ $index + 1 }}</span>
                                    @else
                                        <span class="text-muted me-2">{{ $index + 1 }}.</span>
                                    @endif
                                    <strong>{{ Str::limit($urun->urun_adi, 25) }}</strong>
                                    <br><small class="text-muted">{{ $urun->satici->ad ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><i class="fas fa-eye me-1"></i>{{ number_format($urun->goruntulenme_sayisi) }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($urun->fiyat, 0, ',', '.') }} ₺</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Henüz ürün yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Şüpheli IP Aktiviteleri (varsa) -->
@if($supheliIpler->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-custom border-danger">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>Şüpheli IP Aktiviteleri (Bugün)
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>IP Adresi</th>
                            <th class="text-center">İşlem Sayısı</th>
                            <th class="text-center">Farklı Kullanıcı</th>
                            <th class="text-end">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supheliIpler as $ip)
                            <tr>
                                <td><code>{{ $ip->ip_address }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $ip->islem_sayisi }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $ip->kullanici_sayisi > 3 ? 'danger' : 'info' }}">{{ $ip->kullanici_sayisi }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.ip.show', ['ip' => $ip->ip_address]) }}" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-search"></i> İncele
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Grafikler -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>Son 7 Gün İstatistikleri
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-tags me-2"></i>Kategoriler
            </div>
            <div class="card-body">
                @foreach($kategoriStats as $kategori)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $kategori->kategori_adi }}</span>
                        <span class="badge bg-primary">{{ $kategori->urunler_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Son Aktiviteler ve Kullanıcılar -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-plus me-2"></i>Son Kullanıcılar</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">Tümü</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kullanıcı</th>
                            <th>Tip</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sonKullanicilar as $kullanici)
                            <tr>
                                <td>
                                    <strong>{{ $kullanici->ad }} {{ $kullanici->soyad }}</strong>
                                    <br><small class="text-muted">{{ $kullanici->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-status bg-{{ $kullanici->kullanici_tipi == 'satici' ? 'success' : 'info' }}">
                                        {{ ucfirst($kullanici->kullanici_tipi) }}
                                    </span>
                                </td>
                                <td>{{ $kullanici->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Kullanıcı yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box me-2"></i>Son Ürünler</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Satıcı</th>
                            <th>Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sonUrunler as $urun)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($urun->urun_adi, 25) }}</strong>
                                    <br><small class="text-muted">{{ $urun->kategori->kategori_adi ?? '-' }}</small>
                                </td>
                                <td>{{ $urun->satici->ad ?? '-' }}</td>
                                <td><strong>{{ number_format($urun->fiyat, 2) }} TL</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Ürün yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Grafik verileri
    const labels = {!! json_encode(array_keys($gunlukKayitlar)) !!};
    const kayitData = {!! json_encode(array_values($gunlukKayitlar)) !!};
    const urunData = {!! json_encode(array_values($gunlukUrunler)) !!};
    const gelirLabels = {!! json_encode(array_keys($gunlukGelir)) !!};
    const gelirData = {!! json_encode(array_values($gunlukGelir)) !!};

    // Tum tarihleri birlestir
    const allDates = [...new Set([...labels, ...gelirLabels])].sort();

    // Verileri tum tarihlere gore doldur
    const filledKayitData = allDates.map(date => {
        const index = labels.indexOf(date);
        return index >= 0 ? kayitData[index] : 0;
    });
    const filledUrunData = allDates.map(date => {
        const index = labels.indexOf(date);
        return index >= 0 ? urunData[index] : 0;
    });
    const filledGelirData = allDates.map(date => {
        const index = gelirLabels.indexOf(date);
        return index >= 0 ? gelirData[index] : 0;
    });

    const ctx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allDates,
            datasets: [
                {
                    label: 'Yeni Kayıtlar',
                    data: filledKayitData,
                    borderColor: '#ff9900',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Yeni Ürünler',
                    data: filledUrunData,
                    borderColor: '#11998e',
                    backgroundColor: 'rgba(17, 153, 142, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Gelir (₺)',
                    data: filledGelirData,
                    borderColor: '#f5576c',
                    backgroundColor: 'rgba(245, 87, 108, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.yAxisID === 'y1') {
                                label += new Intl.NumberFormat('tr-TR').format(context.raw) + ' ₺';
                            } else {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
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
                        text: 'Kayıt / Ürün'
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
                        text: 'Gelir (₺)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('tr-TR').format(value) + ' ₺';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
