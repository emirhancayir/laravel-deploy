@extends('admin.layouts.app')

@section('title', 'Yasakli Kelimeler Yonetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Yasakli Kelimeler Yonetimi</h1>
        <a href="{{ route('admin.lists.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="row">
        <!-- Ekleme Formu -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Yeni Kelime Ekle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lists.keyword.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Kelime *</label>
                            <input type="text" name="kelime" class="form-control" placeholder="Yasaklanacak kelime..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Eslesme Tipi *</label>
                            <select name="tip" class="form-select" required>
                                <option value="icerir">Icerir (parcali eslesme)</option>
                                <option value="tam_eslesme">Tam Eslesme (kelime siniri)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uygulanacak Alanlar *</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]" value="urun_adi" id="alan_urun_adi" checked>
                                <label class="form-check-label" for="alan_urun_adi">Ürün Adi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]" value="urun_aciklama" id="alan_urun_aciklama" checked>
                                <label class="form-check-label" for="alan_urun_aciklama">Ürün Aciklama</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]" value="mesaj" id="alan_mesaj" checked>
                                <label class="form-check-label" for="alan_mesaj">Mesajlar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]" value="kullanici_adi" id="alan_kullanici_adi">
                                <label class="form-check-label" for="alan_kullanici_adi">Kullanici Adi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]" value="yorum" id="alan_yorum" checked>
                                <label class="form-check-label" for="alan_yorum">Yorumlar</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Aksiyon *</label>
                            <select name="aksiyon" class="form-select" required onchange="toggleYerine(this)">
                                <option value="engelle">Engelle (kayit/gonderim engellenir)</option>
                                <option value="sansurle">Sansurle (kelime gizlenir)</option>
                                <option value="uyar">Uyar (sadece log tutulur)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="yerineDiv" style="display:none;">
                            <label class="form-label">Yerine Koyulacak</label>
                            <input type="text" name="yerine" class="form-control" placeholder="*** veya [sansurlu]">
                            <small class="text-muted">Bos birakilirsa yildizla degistirilir</small>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-plus"></i> Ekle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Yasakli Kelimeler</h5>
                        <form class="d-flex align-items-center gap-2" method="GET">
                            <select name="aksiyon" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">Tum Aksiyonlar</option>
                                <option value="engelle" {{ request('aksiyon') == 'engelle' ? 'selected' : '' }}>Engelle</option>
                                <option value="sansurle" {{ request('aksiyon') == 'sansurle' ? 'selected' : '' }}>Sansurle</option>
                                <option value="uyar" {{ request('aksiyon') == 'uyar' ? 'selected' : '' }}>Uyar</option>
                            </select>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" name="ara" class="form-control" placeholder="Kelime ara..." value="{{ request('ara') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kelime</th>
                                    <th>Tip</th>
                                    <th>Alanlar</th>
                                    <th>Aksiyon</th>
                                    <th>Durum</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelimeler as $kelime)
                                    <tr class="{{ !$kelime->aktif ? 'table-secondary' : '' }}">
                                        <td><strong>{{ $kelime->kelime }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $kelime->tip == 'tam_eslesme' ? 'info' : 'secondary' }}">
                                                {{ $kelime->tip == 'tam_eslesme' ? 'Tam' : 'Icerir' }}
                                            </span>
                                        </td>
                                        <td><small>{{ $kelime->alanlar_metni }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $kelime->aksiyon == 'engelle' ? 'danger' : ($kelime->aksiyon == 'sansurle' ? 'warning' : 'info') }}">
                                                {{ $kelime->aksiyon_metni }}
                                            </span>
                                            @if($kelime->yerine)
                                                <br><small class="text-muted">-> {{ $kelime->yerine }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.lists.keyword.toggle', $kelime) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $kelime->aktif ? 'success' : 'secondary' }}">
                                                    {{ $kelime->aktif ? 'Aktif' : 'Pasif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.lists.keyword.edit', $kelime) }}" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.lists.keyword.delete', $kelime) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Henüz yasakli kelime yok
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($kelimeler->hasPages())
                    <div class="card-footer">
                        {{ $kelimeler->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleYerine(select) {
    document.getElementById('yerineDiv').style.display = select.value === 'sansurle' ? 'block' : 'none';
}
</script>
@endsection
