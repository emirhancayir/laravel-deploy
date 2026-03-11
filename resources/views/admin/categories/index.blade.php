@extends('admin.layouts.app')

@section('title', 'Kategori Yonetimi')
@section('page-title', 'Kategori Yonetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Kategorileri ve komisyon oranlarını yönetin.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Yeni Kategori
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom">
    <div class="card-header">
        <i class="fas fa-tags me-2"></i>Kategoriler ({{ $kategoriler->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori Adı</th>
                    <th class="text-center">Komisyon Oranı</th>
                    <th class="text-center">Ürün Sayısı</th>
                    <th class="text-center">Durum</th>
                    <th class="text-center">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoriler as $kategori)
                    <tr>
                        <td>{{ $kategori->id }}</td>
                        <td>
                            <strong>{{ $kategori->kategori_adi }}</strong>
                            <br><small class="text-muted">{{ $kategori->slug }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success" style="font-size: 1rem; padding: 8px 16px;">
                                %{{ number_format($kategori->komisyon_orani, 1) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $kategori->urunler_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($kategori->aktif)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Pasif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.categories.edit', $kategori) }}" class="btn btn-outline-primary" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($kategori->urunler_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $kategori) }}" method="POST" class="d-inline kategori-sil-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger kategori-sil-btn" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Henüz kategori yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Komisyon Ozeti -->
<div class="card card-custom mt-4">
    <div class="card-header">
        <i class="fas fa-percentage me-2"></i>Komisyon Özeti
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($kategoriler as $kategori)
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #ff990015 0%, #e68a0015 100%); border: 1px solid rgba(255, 153, 0, 0.2);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $kategori->kategori_adi }}</span>
                            <strong class="text-primary">%{{ number_format($kategori->komisyon_orani, 1) }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="alert alert-info mt-3 mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Komisyon, sadece ürün tutarı üzerinden hesaplanır. <strong>Kargo ücretinden komisyon alınmaz.</strong>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.querySelectorAll('.kategori-sil-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.kategori-sil-form');

        const confirmed = await showConfirm({
            type: 'danger',
            title: 'Kategoriyi Sil',
            message: 'Bu kategoriyi silmek istediğinize emin misiniz?',
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
