@extends('admin.layouts.app')

@section('title', 'Slider Düzenle')
@section('page-title', 'Slider Düzenle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">{{ $slider->baslik }} slider'ını düzenleyin</p>
    </div>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Geri
    </a>
</div>

<div class="card card-custom">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Slider Düzenle</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="baslik" class="form-label">Baslik *</label>
                            <input type="text" name="baslik" id="baslik"
                                   class="form-control @error('baslik') is-invalid @enderror"
                                   value="{{ old('baslik', $slider->baslik) }}" required>
                            @error('baslik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alt_baslik" class="form-label">Alt Baslik</label>
                            <input type="text" name="alt_baslik" id="alt_baslik"
                                   class="form-control @error('alt_baslik') is-invalid @enderror"
                                   value="{{ old('alt_baslik', $slider->alt_baslik) }}">
                            @error('alt_baslik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Link (URL)</label>
                            <input type="text" name="link" id="link"
                                   class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link', $slider->link) }}" placeholder="https://...">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resim" class="form-label">Resim</label>
                            @if($slider->resim)
                                <div class="mb-2">
                                    <img src="{{ $slider->resim_url }}" style="max-height: 150px; border-radius: 8px;">
                                </div>
                            @endif
                            <input type="file" name="resim" id="resim"
                                   class="form-control @error('resim') is-invalid @enderror"
                                   accept="image/*">
                            @error('resim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Yeni resim yuklerseniz mevcut resim degistirilir.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tip" class="form-label">Tip *</label>
                            <select name="tip" id="tip" class="form-select @error('tip') is-invalid @enderror" required>
                                <option value="ozel" {{ old('tip', $slider->tip) == 'ozel' ? 'selected' : '' }}>Ozel Slider</option>
                                <option value="populer" {{ old('tip', $slider->tip) == 'populer' ? 'selected' : '' }}>Populer Ürünler</option>
                                <option value="yeni" {{ old('tip', $slider->tip) == 'yeni' ? 'selected' : '' }}>Yeni Ürünler</option>
                                <option value="indirimli" {{ old('tip', $slider->tip) == 'indirimli' ? 'selected' : '' }}>Indirimli Ürünler</option>
                            </select>
                            @error('tip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sira" class="form-label">Sira</label>
                            <input type="number" name="sira" id="sira"
                                   class="form-control @error('sira') is-invalid @enderror"
                                   value="{{ old('sira', $slider->sira) }}" min="0">
                            @error('sira')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="aktif" id="aktif"
                                       class="form-check-input" value="1"
                                       {{ old('aktif', $slider->aktif) ? 'checked' : '' }}>
                                <label for="aktif" class="form-check-label">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Silme Butonu -->
<div class="card card-custom mt-4 border-danger">
    <div class="card-header text-danger">
        <i class="fas fa-exclamation-triangle me-2"></i> Tehlikeli Bölge
    </div>
    <div class="card-body">
        <p class="mb-3">Bu slider'ı kalıcı olarak silmek istiyorsanız aşağıdaki butona tıklayın.</p>
        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" id="sliderSilForm">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger" onclick="sliderSil()">
                <i class="fas fa-trash me-1"></i> Slider'ı Sil
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
async function sliderSil() {
    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Slider Sil',
        message: 'Bu slider silinecek. Emin misiniz?',
        confirmText: 'Evet, Sil',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('sliderSilForm').submit();
    }
}
</script>
@endpush
@endsection
