@extends('admin.layouts.app')

@section('title', 'Ürün Düzenle - ' . $urun->urun_adi)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ürün Düzenle</h1>
        <div>
            <a href="{{ route('admin.products.show', $urun) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $urun) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Ürün Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ürün Adi *</label>
                            <input type="text" name="urun_adi" class="form-control @error('urun_adi') is-invalid @enderror"
                                   value="{{ old('urun_adi', $urun->urun_adi) }}" required>
                            @error('urun_adi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Aciklama *</label>
                            <textarea name="aciklama" class="form-control @error('aciklama') is-invalid @enderror"
                                      rows="6" required>{{ old('aciklama', $urun->aciklama) }}</textarea>
                            @error('aciklama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fiyat (TL) *</label>
                                <input type="number" name="fiyat" class="form-control @error('fiyat') is-invalid @enderror"
                                       value="{{ old('fiyat', $urun->fiyat) }}" step="0.01" min="0" required>
                                @error('fiyat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok *</label>
                                <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                                       value="{{ old('stok', $urun->stok) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
                                <option value="">Kategori Sec</option>
                                @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('kategori_id', $urun->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mevcut Resimler -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-images"></i> Mevcut Resimler</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($urun->resim)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}" class="img-fluid rounded">
                                    <small class="text-muted">Ana Resim</small>
                                </div>
                            @endif
                            @foreach($urun->resimler as $resim)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('serve-image.php?p=urunler/' . $resim->resim) }}" class="img-fluid rounded">
                                </div>
                            @endforeach
                            @if(!$urun->resim && $urun->resimler->isEmpty())
                                <div class="col-12 text-center text-muted py-3">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p class="mb-0">Resim yok</p>
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">Not: Resim yonetimi icin satici panelini kullanin.</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Durum -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Durum</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ürün Durumu *</label>
                            <select name="durum" class="form-select @error('durum') is-invalid @enderror" required>
                                <option value="aktif" {{ old('durum', $urun->durum) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="beklemede" {{ old('durum', $urun->durum) == 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                                <option value="pasif" {{ old('durum', $urun->durum) == 'pasif' ? 'selected' : '' }}>Pasif</option>
                                <option value="reddedildi" {{ old('durum', $urun->durum) == 'reddedildi' ? 'selected' : '' }}>Reddedildi</option>
                            </select>
                            @error('durum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Aktif:</strong> Sitede gorunur<br>
                                <strong>Beklemede:</strong> Onay bekliyor<br>
                                <strong>Pasif:</strong> Gorunmez<br>
                                <strong>Reddedildi:</strong> Reddedildi
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Satıcı Bilgisi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Satici</h5>
                    </div>
                    <div class="card-body">
                        @if($urun->satici)
                            <div class="d-flex align-items-center">
                                @if($urun->satici->profil_resmi)
                                    <img src="{{ asset('serve-image.php?p=profil/' . $urun->satici->profil_resmi) }}" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                        <i class="fas fa-user text-white small"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $urun->satici->ad_soyad }}</strong>
                                    <br><small class="text-muted">{{ $urun->satici->email }}</small>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Satıcı bilgisi yok</p>
                        @endif
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.products.show', $urun) }}" class="btn btn-secondary w-100">
                            Iptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
