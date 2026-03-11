@extends('admin.layouts.app')

@section('title', 'Kategori Duzenle')
@section('page-title', 'Kategori Duzenle')

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
                <i class="fas fa-edit me-2"></i>{{ $kategori->kategori_adi }} - Duzenle
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $kategori) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="kategori_adi" class="form-label">Kategori Adi *</label>
                        <input type="text" class="form-control @error('kategori_adi') is-invalid @enderror"
                               id="kategori_adi" name="kategori_adi"
                               value="{{ old('kategori_adi', $kategori->kategori_adi) }}" required>
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
                                   value="{{ old('komisyon_orani', $kategori->komisyon_orani) }}"
                                   min="0" max="100" step="0.5" required>
                        </div>
                        @error('komisyon_orani')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Bu kategorideki urunlerden kesilecek komisyon orani</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="aktif" name="aktif" value="1"
                                   {{ old('aktif', $kategori->aktif) ? 'checked' : '' }}>
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
                <i class="fas fa-info-circle me-2"></i>Bilgi
            </div>
            <div class="card-body">
                <p><strong>Urun Sayisi:</strong> {{ $kategori->urunler()->count() }}</p>
                <p><strong>Olusturulma:</strong> {{ $kategori->created_at?->format('d.m.Y H:i') ?? '-' }}</p>

                <hr>

                <h6>Komisyon Hesaplama Ornegi</h6>
                <div class="bg-light p-3 rounded">
                    <p class="mb-1">Urun Fiyati: <strong>1.000 TL</strong></p>
                    <p class="mb-1">Komisyon (%{{ $kategori->komisyon_orani }}): <strong class="text-danger">-{{ number_format(1000 * $kategori->komisyon_orani / 100, 2) }} TL</strong></p>
                    <p class="mb-0">Saticiya Kalacak: <strong class="text-success">{{ number_format(1000 - (1000 * $kategori->komisyon_orani / 100), 2) }} TL</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
