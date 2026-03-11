@extends('admin.layouts.app')

@section('title', 'Yorum Yönetimi')
@section('page-title', 'Yorum Yönetimi')

@section('content')
<!-- İstatistik Kartları -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-custom text-center">
            <div class="card-body">
                <h3 class="mb-0">{{ $istatistikler['toplam'] }}</h3>
                <small class="text-muted">Toplam Yorum</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center">
            <div class="card-body">
                <h3 class="mb-0 text-warning">{{ $istatistikler['bekleyen'] }}</h3>
                <small class="text-muted">Onay Bekleyen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center">
            <div class="card-body">
                <h3 class="mb-0 text-success">{{ $istatistikler['onaylanan'] }}</h3>
                <small class="text-muted">Onaylanan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom text-center">
            <div class="card-body">
                <h3 class="mb-0 text-primary">{{ $istatistikler['ortalama_puan'] }} <i class="fas fa-star text-warning"></i></h3>
                <small class="text-muted">Ortalama Puan</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtreler -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Durum</label>
                <select name="durum" class="form-select">
                    <option value="">Tümü</option>
                    <option value="bekleyen" {{ request('durum') == 'bekleyen' ? 'selected' : '' }}>Onay Bekleyen</option>
                    <option value="onaylanan" {{ request('durum') == 'onaylanan' ? 'selected' : '' }}>Onaylanan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Puan</label>
                <select name="puan" class="form-select">
                    <option value="">Tümü</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('puan') == $i ? 'selected' : '' }}>{{ $i }} Yıldız</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Arama</label>
                <input type="text" name="ara" class="form-control" placeholder="Yorum, kullanıcı veya ürün ara..." value="{{ request('ara') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrele
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Temizle</a>
            </div>
        </form>
    </div>
</div>

<!-- Yorumlar Tablosu -->
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-comments me-2"></i>Yorumlar</span>
        @if($yorumlar->where('onaylandi', false)->count() > 0)
            <div>
                <form action="{{ route('admin.reviews.bulkApprove') }}" method="POST" class="d-inline" id="topluOnaylaForm">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" disabled id="topluOnaylaBtn">
                        <i class="fas fa-check-double me-1"></i>Seçilenleri Onayla
                    </button>
                </form>
                <form action="{{ route('admin.reviews.bulkDelete') }}" method="POST" class="d-inline ms-2" id="topluSilForm">
                    @csrf
                    <button type="button" class="btn btn-sm btn-danger" disabled id="topluSilBtn" onclick="topluYorumSil()">
                        <i class="fas fa-trash me-1"></i>Seçilenleri Sil
                    </button>
                </form>
            </div>
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th>Kullanıcı</th>
                    <th>Ürün</th>
                    <th>Puan</th>
                    <th>Yorum</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th width="150">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($yorumlar as $yorum)
                    <tr>
                        <td>
                            @if(!$yorum->onaylandi)
                                <input type="checkbox" class="form-check-input yorum-checkbox" value="{{ $yorum->id }}" name="yorumlar[]">
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $yorum->kullanici_id) }}" class="text-decoration-none">
                                {{ $yorum->kullanici->ad ?? 'Silinmiş' }} {{ $yorum->kullanici->soyad ?? '' }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('products.show', $yorum->urun_id) }}" target="_blank" class="text-decoration-none">
                                {{ Str::limit($yorum->urun->urun_adi ?? 'Silinmiş', 30) }}
                            </a>
                        </td>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $yorum->puan)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </td>
                        <td>
                            <span title="{{ $yorum->yorum }}">
                                {{ Str::limit($yorum->yorum ?? '-', 50) }}
                            </span>
                            @if($yorum->resimler && count($yorum->resimler) > 0)
                                <div style="margin-top: 5px; display: flex; gap: 5px;">
                                    @foreach($yorum->resimler as $resim)
                                        <a href="{{ asset('uploads/yorumlar/' . $resim) }}" target="_blank">
                                            <img src="{{ asset('uploads/yorumlar/' . $resim) }}"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($yorum->onaylandi)
                                <span class="badge bg-success">Onaylı</span>
                            @else
                                <span class="badge bg-warning">Beklemede</span>
                            @endif
                        </td>
                        <td>{{ $yorum->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if(!$yorum->onaylandi)
                                <form action="{{ route('admin.reviews.approve', $yorum) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Onayla">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.reject', $yorum) }}" method="POST" class="d-inline yorum-sil-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger yorum-sil-btn" title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Henüz yorum yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($yorumlar->hasPages())
        <div class="card-footer">
            {{ $yorumlar->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.yorum-checkbox');
    const topluOnaylaBtn = document.getElementById('topluOnaylaBtn');
    const topluSilBtn = document.getElementById('topluSilBtn');
    const topluOnaylaForm = document.getElementById('topluOnaylaForm');
    const topluSilForm = document.getElementById('topluSilForm');

    function updateButtons() {
        const checked = document.querySelectorAll('.yorum-checkbox:checked');
        const hasChecked = checked.length > 0;

        if (topluOnaylaBtn) topluOnaylaBtn.disabled = !hasChecked;
        if (topluSilBtn) topluSilBtn.disabled = !hasChecked;

        // Form'lara seçili yorumları ekle
        if (topluOnaylaForm) {
            topluOnaylaForm.querySelectorAll('input[name="yorumlar[]"]').forEach(el => el.remove());
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'yorumlar[]';
                input.value = cb.value;
                topluOnaylaForm.appendChild(input);
            });
        }
        if (topluSilForm) {
            topluSilForm.querySelectorAll('input[name="yorumlar[]"]').forEach(el => el.remove());
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'yorumlar[]';
                input.value = cb.value;
                topluSilForm.appendChild(input);
            });
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateButtons();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButtons);
    });

    // Tekil yorum silme
    document.querySelectorAll('.yorum-sil-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const form = this.closest('.yorum-sil-form');

            const confirmed = await showConfirm({
                type: 'danger',
                title: 'Yorumu Sil',
                message: 'Bu yorum silinecek. Emin misiniz?',
                confirmText: 'Evet, Sil',
                cancelText: 'Vazgeç'
            });

            if (confirmed) {
                form.submit();
            }
        });
    });
});

// Toplu yorum silme
async function topluYorumSil() {
    const checked = document.querySelectorAll('.yorum-checkbox:checked');

    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Yorumları Sil',
        message: `Seçili ${checked.length} yorum silinecek. Emin misiniz?`,
        confirmText: 'Evet, Sil',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('topluSilForm').submit();
    }
}
</script>
@endpush
@endsection
