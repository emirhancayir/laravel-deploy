@extends('admin.layouts.app')

@section('title', 'Ürün Yonetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ürün Yonetimi</h1>
    </div>

    <!-- Istatistikler -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $istatistikler['toplam'] }}</h4>
                    <small>Toplam</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $istatistikler['aktif'] }}</h4>
                    <small>Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $istatistikler['beklemede'] }}</h4>
                    <small>Beklemede</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $istatistikler['pasif'] }}</h4>
                    <small>Pasif</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $istatistikler['satildi'] }}</h4>
                    <small>Satildi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="ara" class="form-control" placeholder="Ürün adi, aciklama veya satici ara..." value="{{ request('ara') }}">
                </div>
                <div class="col-md-2">
                    <select name="durum" class="form-select">
                        <option value="">Tum Durumlar</option>
                        <option value="aktif" {{ request('durum') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="beklemede" {{ request('durum') == 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                        <option value="pasif" {{ request('durum') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                        <option value="reddedildi" {{ request('durum') == 'reddedildi' ? 'selected' : '' }}>Reddedildi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kategori" class="form-select">
                        <option value="">Tum Kategoriler</option>
                        @foreach($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="siralama" class="form-select">
                        <option value="yeni" {{ request('siralama') == 'yeni' ? 'selected' : '' }}>En Yeni</option>
                        <option value="eski" {{ request('siralama') == 'eski' ? 'selected' : '' }}>En Eski</option>
                        <option value="fiyat_artan" {{ request('siralama') == 'fiyat_artan' ? 'selected' : '' }}>Fiyat (Artan)</option>
                        <option value="fiyat_azalan" {{ request('siralama') == 'fiyat_azalan' ? 'selected' : '' }}>Fiyat (Azalan)</option>
                        <option value="goruntulenme" {{ request('siralama') == 'goruntulenme' ? 'selected' : '' }}>En Cok Goruntulenen</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrele</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Temizle</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toplu Islem Formu -->
    <form id="topluIslemForm" action="{{ route('admin.products.bulk') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-box"></i> Ürünler</h5>
                <div class="d-flex gap-2">
                    <select name="islem" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Toplu Islem Sec</option>
                        <option value="onayla">Onayla</option>
                        <option value="pasif">Pasife Al</option>
                        <option value="reddet">Reddet</option>
                        <option value="sil">Sil</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="fas fa-check"></i> Uygula
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="tumunuSec" class="form-check-input">
                                </th>
                                <th style="width: 80px;">Resim</th>
                                <th>Ürün Adi</th>
                                <th>Satici</th>
                                <th>Kategori</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th>Goruntulenme</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th style="width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($urunler as $urun)
                                <tr class="{{ $urun->satildi ? 'table-secondary' : '' }}">
                                    <td>
                                        <input type="checkbox" name="urunler[]" value="{{ $urun->id }}" class="form-check-input urun-checkbox">
                                    </td>
                                    <td>
                                        @if($urun->resim)
                                            <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}" class="rounded" width="60" height="60" style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                                                <i class="fas fa-image text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $urun) }}" class="text-decoration-none">
                                            <strong>{{ Str::limit($urun->urun_adi, 40) }}</strong>
                                        </a>
                                        @if($urun->satildi)
                                            <span class="badge bg-info ms-1">Satildi</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">ID: {{ $urun->id }}</small>
                                    </td>
                                    <td>
                                        @if($urun->satici)
                                            <a href="{{ route('admin.users.show', $urun->satici) }}" class="text-decoration-none">
                                                {{ $urun->satici->ad_soyad }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $urun->kategori->name ?? 'Kategorisiz' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($urun->fiyat, 2, ',', '.') }} TL</strong>
                                    </td>
                                    <td>
                                        @if($urun->stok > 0)
                                            <span class="badge bg-success">{{ $urun->stok }}</span>
                                        @else
                                            <span class="badge bg-danger">Tukendi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-eye text-muted"></i> {{ $urun->goruntulenme_sayisi ?? 0 }}
                                    </td>
                                    <td>
                                        @switch($urun->durum)
                                            @case('aktif')
                                                <span class="badge bg-success">Aktif</span>
                                                @break
                                            @case('beklemede')
                                                <span class="badge bg-warning text-dark">Beklemede</span>
                                                @break
                                            @case('pasif')
                                                <span class="badge bg-secondary">Pasif</span>
                                                @break
                                            @case('reddedildi')
                                                <span class="badge bg-danger">Reddedildi</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $urun->durum }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $urun->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.products.show', $urun) }}" class="btn btn-outline-info" title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $urun) }}" class="btn btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($urun->durum == 'beklemede')
                                                <form action="{{ route('admin.products.approve', $urun) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Onayla">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.products.destroy', $urun) }}" method="POST" class="d-inline admin-urun-sil-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger admin-urun-sil-btn" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        Ürün bulunamadı
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($urunler->hasPages())
                <div class="card-footer">
                    {{ $urunler->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

<script>
document.getElementById('tumunuSec').addEventListener('change', function() {
    document.querySelectorAll('.urun-checkbox').forEach(cb => cb.checked = this.checked);
});

// Form submit kontrolu
document.getElementById('topluIslemForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const islem = this.querySelector('select[name="islem"]').value;
    const seciliUrunler = this.querySelectorAll('input[name="urunler[]"]:checked');

    if (!islem) {
        showToast('Lütfen bir işlem seçin!', 'warning');
        return false;
    }

    if (seciliUrunler.length === 0) {
        showToast('Lütfen en az bir ürün seçin!', 'warning');
        return false;
    }

    const islemAdlari = {
        'onayla': 'Onayla',
        'pasif': 'Pasife Al',
        'reddet': 'Reddet',
        'sil': 'Sil'
    };

    const confirmed = await showConfirm({
        type: islem === 'sil' ? 'danger' : 'warning',
        title: 'Toplu İşlem Onayı',
        message: `Seçili ${seciliUrunler.length} ürüne "${islemAdlari[islem] || islem}" işlemi uygulanacak. Emin misiniz?`,
        confirmText: 'Evet, Uygula',
        cancelText: 'Vazgeç'
    });

    if (!confirmed) return false;

    // Form'u POST olarak gonder
    this.method = 'POST';
    this.submit();
});

// Tekil ürün silme butonları
document.querySelectorAll('.admin-urun-sil-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.admin-urun-sil-form');

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
@endsection
