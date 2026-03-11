@extends('admin.layouts.app')

@section('title', 'Yeni Slider')
@section('page-title', 'Yeni Slider Ekle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Yeni slider veya carousel bölümü ekleyin</p>
    </div>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Geri
    </a>
</div>

<div class="card card-custom">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-plus"></i> Yeni Slider</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="baslik" class="form-label">Baslik *</label>
                            <input type="text" name="baslik" id="baslik"
                                   class="form-control @error('baslik') is-invalid @enderror"
                                   value="{{ old('baslik') }}" required>
                            @error('baslik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alt_baslik" class="form-label">Alt Baslik</label>
                            <input type="text" name="alt_baslik" id="alt_baslik"
                                   class="form-control @error('alt_baslik') is-invalid @enderror"
                                   value="{{ old('alt_baslik') }}">
                            @error('alt_baslik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Link (URL)</label>
                            <input type="text" name="link" id="link"
                                   class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link') }}" placeholder="https://...">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resim" class="form-label">Resim</label>
                            <input type="file" name="resim" id="resim"
                                   class="form-control @error('resim') is-invalid @enderror"
                                   accept="image/*">
                            @error('resim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimum 5MB, JPG/PNG formatinda</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tip" class="form-label">Tip *</label>
                            <select name="tip" id="tip" class="form-select @error('tip') is-invalid @enderror" required>
                                <option value="ozel" {{ old('tip') == 'ozel' ? 'selected' : '' }}>Ozel Slider</option>
                                <option value="populer" {{ old('tip') == 'populer' ? 'selected' : '' }}>Populer Ürünler</option>
                                <option value="yeni" {{ old('tip') == 'yeni' ? 'selected' : '' }}>Yeni Ürünler</option>
                                <option value="indirimli" {{ old('tip') == 'indirimli' ? 'selected' : '' }}>Indirimli Ürünler</option>
                            </select>
                            @error('tip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sira" class="form-label">Sira</label>
                            <input type="number" name="sira" id="sira"
                                   class="form-control @error('sira') is-invalid @enderror"
                                   value="{{ old('sira', 0) }}" min="0">
                            @error('sira')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="aktif" id="aktif"
                                       class="form-check-input" value="1"
                                       {{ old('aktif', true) ? 'checked' : '' }}>
                                <label for="aktif" class="form-check-label">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bilgi Kutusu -->
<div class="card card-custom mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i> Slider Tipleri
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong><i class="fas fa-star text-primary"></i> Özel Slider:</strong> Kampanya, duyuru banner'ları için. Resim ve link manuel eklenir.</p>
                <p><strong><i class="fas fa-fire text-danger"></i> Popüler Ürünler:</strong> En çok görüntülenen ürünler otomatik gösterilir.</p>
            </div>
            <div class="col-md-6">
                <p><strong><i class="fas fa-clock text-success"></i> Yeni Ürünler:</strong> Son eklenen ürünler otomatik listelenir.</p>
                <p><strong><i class="fas fa-percent text-warning"></i> İndirimli Ürünler:</strong> Eski fiyatı olan ürünler gösterilir.</p>
            </div>
        </div>
    </div>
</div>
@endsection
