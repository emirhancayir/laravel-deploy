@extends('admin.layouts.app')

@section('title', 'Slider Yönetimi')
@section('page-title', 'Slider Yönetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Ana sayfadaki carousel bölümlerini yönetin</p>
    </div>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Yeni Slider
    </a>
</div>

<!-- Slider Tipleri Açıklaması -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-custom text-center" style="border-left: 4px solid #ff9900;">
            <div class="card-body py-3">
                <i class="fas fa-star text-primary mb-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-1">Özel Slider</h6>
                <small class="text-muted">Manuel eklenen banner</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center" style="border-left: 4px solid #f5576c;">
            <div class="card-body py-3">
                <i class="fas fa-fire text-danger mb-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-1">Popüler Ürünler</h6>
                <small class="text-muted">En çok görüntülenen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center" style="border-left: 4px solid #11998e;">
            <div class="card-body py-3">
                <i class="fas fa-clock text-success mb-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-1">Yeni Ürünler</h6>
                <small class="text-muted">Son eklenenler</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center" style="border-left: 4px solid #f093fb;">
            <div class="card-body py-3">
                <i class="fas fa-percent text-warning mb-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-1">İndirimli Ürünler</h6>
                <small class="text-muted">Fiyatı düşenler</small>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header">
        <i class="fas fa-images me-2"></i> Aktif Sliderlar
    </div>
    <div class="card-body p-0">
        @if($sliders->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Henüz slider eklenmemiş.</p>
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> İlk Slider'ı Ekle
                </a>
            </div>
        @else
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th width="60">Sıra</th>
                        <th width="100">Resim</th>
                        <th>Başlık</th>
                        <th>Tip</th>
                        <th>Link</th>
                        <th>Durum</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sliders as $slider)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $slider->sira }}</span>
                            </td>
                            <td>
                                @if($slider->resim)
                                    <img src="{{ $slider->resim_url }}" style="height: 50px; width: 80px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <div style="height: 50px; width: 80px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $slider->baslik }}</strong>
                                @if($slider->alt_baslik)
                                    <br><small class="text-muted">{{ $slider->alt_baslik }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipRenk = match($slider->tip) {
                                        'ozel' => 'primary',
                                        'populer' => 'danger',
                                        'yeni' => 'success',
                                        'indirimli' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $tipRenk }}">{{ $slider->tip_metni }}</span>
                            </td>
                            <td>
                                @if($slider->link)
                                    <a href="{{ $slider->link }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-external-link-alt"></i> Link
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($slider->aktif)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-warning" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline slider-sil-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger slider-sil-btn" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @if($sliders->hasPages())
        <div class="card-footer">
            {{ $sliders->links() }}
        </div>
    @endif
</div>

<!-- Bilgi Kutusu -->
<div class="card card-custom mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i> Slider Tipleri Hakkında
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-star text-primary"></i> Özel Slider</h6>
                <p class="text-muted small">Kampanya, duyuru veya özel banner'lar için kullanılır. Resim ve link manuel eklenir.</p>

                <h6><i class="fas fa-fire text-danger"></i> Popüler Ürünler</h6>
                <p class="text-muted small">En çok görüntülenen ürünler otomatik olarak bu bölümde gösterilir.</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-clock text-success"></i> Yeni Ürünler</h6>
                <p class="text-muted small">Son eklenen ürünler otomatik olarak bu bölümde listelenir.</p>

                <h6><i class="fas fa-percent text-warning"></i> İndirimli Ürünler</h6>
                <p class="text-muted small">Eski fiyatı olan (indirime giren) ürünler burada gösterilir.</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.querySelectorAll('.slider-sil-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.slider-sil-form');

        const confirmed = await showConfirm({
            type: 'danger',
            title: 'Slider Sil',
            message: 'Bu slider silinecek. Emin misiniz?',
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
