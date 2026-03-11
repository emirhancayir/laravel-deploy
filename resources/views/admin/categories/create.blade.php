@extends('admin.layouts.app')

@section('title', 'Yeni Kategori')
@section('page-title', 'Yeni Kategori')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Geri
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-plus me-2"></i>Yeni Kategori Ekle
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="kategori_adi" class="form-label">Kategori Adi *</label>
                        <input type="text" class="form-control @error('kategori_adi') is-invalid @enderror"
                               id="kategori_adi" name="kategori_adi"
                               value="{{ old('kategori_adi') }}" required>
                        @error('kategori_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="komisyon_orani" class="form-label">Komisyon Orani (%) *</label>
                        <div class="input-group">
                            <span class="input-group-text">%</span>
                            <input type="number" class="form-control @error('komisyon_orani') is-invalid @enderror"
                                   id="komisyon_orani" name="komisyon_orani"
                                   value="{{ old('komisyon_orani', 10) }}"
                                   min="0" max="100" step="0.5" required>
                        </div>
                        @error('komisyon_orani')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Bu kategorideki urunlerden kesilecek komisyon orani</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="aktif" name="aktif" value="1" checked>
                            <label class="form-check-label" for="aktif">Aktif</label>
                        </div>
                        <small class="text-muted">Pasif kategoriler urun ekleme formunda gozukmez</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            Iptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i>Ipucu
            </div>
            <div class="card-body">
                <p>Komisyon orani, satilan urun tutari uzerinden hesaplanir.</p>
                <p class="text-muted">Kargo ucretinden komisyon alinmaz.</p>

                <hr>

                <h6>Ornek Komisyon Oranlari</h6>
                <ul class="mb-0">
                    <li>Elektronik: %5-10</li>
                    <li>Giyim: %10-15</li>
                    <li>Kitap: %10-20</li>
                    <li>Vasita: %3-5</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
