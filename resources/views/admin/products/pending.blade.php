@extends('admin.layouts.app')

@section('title', 'Onay Bekleyen Ürünler')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-clock"></i> Onay Bekleyen Ürünler
            <span class="badge bg-warning text-dark ms-2">{{ $bekleyenSayisi }}</span>
        </h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Tüm Ürünler
        </a>
    </div>

    @if(session('basarili'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('basarili') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Arama -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="ara" class="form-control"
                           placeholder="Ürün adı veya satıcı ara..."
                           value="{{ request('ara') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Ara
                    </button>
                </div>
                @if(request('ara'))
                    <div class="col-md-2">
                        <a href="{{ route('admin.products.pending') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Temizle
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($urunler->count() > 0)
        <!-- Urunler -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px">Resim</th>
                                <th>Ürün Adı</th>
                                <th>Satıcı</th>
                                <th>Kategori</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th style="width: 200px">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($urunler as $urun)
                                <tr>
                                    <td>
                                        @if($urun->resim)
                                            <img src="{{ asset('serve-image.php?p=urunler/' . $urun->resim) }}"
                                                 alt="{{ $urun->urun_adi }}"
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="text-muted text-center" style="width: 60px; height: 60px; line-height: 60px; border: 1px solid #dee2e6; border-radius: 4px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $urun) }}" class="text-decoration-none">
                                            <strong>{{ $urun->urun_adi }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $urun->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $urun->satici_id) }}" class="text-decoration-none">
                                            {{ $urun->satici->ad }} {{ $urun->satici->soyad }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $urun->satici->email }}</small>
                                    </td>
                                    <td>{{ $urun->kategori?->kategori_adi ?? '-' }}</td>
                                    <td><strong>{{ number_format($urun->fiyat, 2, ',', '.') }} TL</strong></td>
                                    <td>
                                        <span class="{{ $urun->stok < 5 ? 'text-danger' : 'text-success' }}">
                                            {{ $urun->stok }} adet
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.show', $urun) }}"
                                               class="btn btn-sm btn-info"
                                               title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="onaylaUrun({{ $urun->id }})"
                                                    title="Onayla">
                                                <i class="fas fa-check"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reddetModal{{ $urun->id }}"
                                                    title="Reddet">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Onay Formu (gizli) -->
                                        <form id="onayForm{{ $urun->id }}"
                                              action="{{ route('admin.products.approve', $urun) }}"
                                              method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>

                                        <!-- Reddetme Modal -->
                                        <div class="modal fade" id="reddetModal{{ $urun->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.products.reject', $urun) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Ürünü Reddet</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>{{ $urun->urun_adi }}</strong> ürününü reddetmek istediğinizden emin misiniz?</p>
                                                            <div class="mb-3">
                                                                <label for="red_nedeni{{ $urun->id }}" class="form-label">Red Nedeni *</label>
                                                                <textarea class="form-control"
                                                                          id="red_nedeni{{ $urun->id }}"
                                                                          name="red_nedeni"
                                                                          rows="3"
                                                                          required
                                                                          placeholder="Ürünün reddedilme sebebini açıklayın..."></textarea>
                                                                <small class="text-muted">Bu mesaj satıcıya gönderilecektir.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                            <button type="submit" class="btn btn-danger">Reddet</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $urunler->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4>Onay Bekleyen Ürün Yok</h4>
                <p class="text-muted">Tüm ürünler onaylanmış veya işlem bekliyor.</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
async function onaylaUrun(urunId) {
    const confirmed = await showConfirm({
        type: 'success',
        title: 'Ürünü Onayla',
        message: 'Bu ürünü onaylamak istediğinizden emin misiniz? Ürün yayına alınacaktır.',
        confirmText: 'Evet, Onayla',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('onayForm' + urunId).submit();
    }
}
</script>
@endpush
@endsection
