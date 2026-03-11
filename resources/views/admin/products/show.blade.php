@extends('admin.layouts.app')

@section('title', 'Ürün Detayı - ' . $urun->urun_adi)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ürün Detayı</h1>
        <div>
            <a href="{{ route('admin.products.edit', $urun) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Düzenle
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sol Kolon - Ürün Bilgileri -->
        <div class="col-lg-8">
            <!-- Resimler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-images"></i> Ürün Resimleri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($urun->resim)
                            <div class="col-md-4 mb-3">
                                <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                <small class="text-muted d-block">Ana Resim</small>
                            </div>
                        @endif
                        @foreach($urun->resimler as $resim)
                            <div class="col-md-4 mb-3">
                                <img src="{{ asset('serve-image.php?p=urunler/' . $resim->resim) }}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>
                        @endforeach
                        @if(!$urun->resim && $urun->resimler->isEmpty())
                            <div class="col-12 text-center text-muted py-4">
                                <i class="fas fa-image fa-3x mb-2"></i>
                                <p>Resim yok</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ürün Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Ürün Bilgileri</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $urun->urun_adi }}</h4>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fiyat:</strong>
                            <span class="fs-4 text-success">{{ number_format($urun->fiyat, 2, ',', '.') }} TL</span>
                            @if($urun->eski_fiyat)
                                <br><small class="text-muted text-decoration-line-through">{{ number_format($urun->eski_fiyat, 2, ',', '.') }} TL</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Stok:</strong>
                            @if($urun->stok > 0)
                                <span class="badge bg-success fs-6">{{ $urun->stok }} adet</span>
                            @else
                                <span class="badge bg-danger fs-6">Tukendi</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Aciklama:</strong>
                        <p class="mt-2">{!! nl2br(e($urun->aciklama)) !!}</p>
                    </div>
                </div>
            </div>

            <!-- Kategori Ozellikleri -->
            @if($urun->attributeValues->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Kategori Ozellikleri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($urun->attributeValues as $attrValue)
                                <div class="col-md-6 mb-2">
                                    <strong>{{ $attrValue->attribute->label ?? $attrValue->attribute->attribute_adi }}:</strong>
                                    {{ $attrValue->deger }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stok Hareketleri -->
            @if($urun->stokHareketleri && $urun->stokHareketleri->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Stok Hareketleri</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Hareket</th>
                                    <th>Miktar</th>
                                    <th>Onceki</th>
                                    <th>Sonraki</th>
                                    <th>Aciklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($urun->stokHareketleri->take(10) as $hareket)
                                    <tr>
                                        <td>{{ $hareket->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $hareket->hareket_tipi == 'giris' ? 'success' : ($hareket->hareket_tipi == 'cikis' || $hareket->hareket_tipi == 'satis' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($hareket->hareket_tipi) }}
                                            </span>
                                        </td>
                                        <td>{{ $hareket->miktar > 0 ? '+' : '' }}{{ $hareket->miktar }}</td>
                                        <td>{{ $hareket->onceki_stok }}</td>
                                        <td>{{ $hareket->sonraki_stok }}</td>
                                        <td>{{ $hareket->aciklama ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sag Kolon - Durum ve Satıcı -->
        <div class="col-lg-4">
            <!-- Durum -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Durum</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        @switch($urun->durum)
                            @case('aktif')
                                <span class="badge bg-success fs-5 px-4 py-2">Aktif</span>
                                @break
                            @case('beklemede')
                                <span class="badge bg-warning text-dark fs-5 px-4 py-2">Beklemede</span>
                                @break
                            @case('pasif')
                                <span class="badge bg-secondary fs-5 px-4 py-2">Pasif</span>
                                @break
                            @case('reddedildi')
                                <span class="badge bg-danger fs-5 px-4 py-2">Reddedildi</span>
                                @break
                        @endswitch
                        @if($urun->satildi)
                            <span class="badge bg-info fs-5 px-4 py-2 ms-2">Satildi</span>
                        @endif
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        @if($urun->durum != 'aktif')
                            <form action="{{ route('admin.products.approve', $urun) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> Onayla
                                </button>
                            </form>
                        @endif
                        @if($urun->durum != 'pasif')
                            <form action="{{ route('admin.products.deactivate', $urun) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-pause"></i> Pasife Al
                                </button>
                            </form>
                        @endif
                        @if($urun->durum != 'reddedildi')
                            <form action="{{ route('admin.products.reject', $urun) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-times"></i> Reddet
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.products.destroy', $urun) }}" method="POST" id="urunSilForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger w-100" onclick="adminUrunSil()">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </form>
                        <script>
                        async function adminUrunSil() {
                            const confirmed = await showConfirm({
                                type: 'danger',
                                title: 'Ürünü Sil',
                                message: 'Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
                                confirmText: 'Evet, Sil',
                                cancelText: 'Vazgeç'
                            });
                            if (confirmed) {
                                document.getElementById('urunSilForm').submit();
                            }
                        }
                        </script>
                    </div>
                </div>
            </div>

            <!-- Satıcı Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Satici</h5>
                </div>
                <div class="card-body">
                    @if($urun->satici)
                        <div class="d-flex align-items-center mb-3">
                            @if($urun->satici->profil_resmi)
                                <img src="{{ asset('serve-image.php?p=profil/' . $urun->satici->profil_resmi) }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <strong>{{ $urun->satici->ad_soyad }}</strong>
                                <br><small class="text-muted">{{ $urun->satici->email }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $urun->satici) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt"></i> Satıcı Profilini Gor
                        </a>
                    @else
                        <p class="text-muted mb-0">Satıcı bilgisi yok</p>
                    @endif
                </div>
            </div>

            <!-- Konum -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Konum</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Il:</strong> {{ $urun->il->il_adi ?? '-' }}</p>
                    <p class="mb-1"><strong>Ilce:</strong> {{ $urun->ilce->ilce_adi ?? '-' }}</p>
                    <p class="mb-1"><strong>Mahalle:</strong> {{ $urun->mahalle->mahalle_adi ?? '-' }}</p>
                    @if($urun->adres_detay)
                        <p class="mb-0"><strong>Adres:</strong> {{ $urun->adres_detay }}</p>
                    @endif
                </div>
            </div>

            <!-- Diger Bilgiler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Istatistikler</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Goruntulenme:</strong> {{ $urun->goruntulenme_sayisi ?? 0 }}</p>
                    <p class="mb-2"><strong>Kategori:</strong> {{ $urun->kategori->name ?? 'Kategorisiz' }}</p>
                    <p class="mb-2"><strong>Oluşturulma:</strong> {{ $urun->created_at->format('d.m.Y H:i') }}</p>
                    <p class="mb-0"><strong>Güncelleme:</strong> {{ $urun->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <!-- Hizli Linkler -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-link"></i> Hizli Linkler</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('products.show', $urun) }}" target="_blank" class="btn btn-outline-info btn-sm w-100 mb-2">
                        <i class="fas fa-external-link-alt"></i> Sitede Gor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
