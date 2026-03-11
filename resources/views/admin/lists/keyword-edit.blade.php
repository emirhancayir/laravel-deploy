@extends('admin.layouts.app')

@section('title', 'Yasakli Kelime Düzenle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Yasakli Kelime Düzenle</h1>
        <a href="{{ route('admin.lists.keywords') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Kelime Düzenle: <strong>{{ $kelime->kelime }}</strong></h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lists.keyword.update', $kelime) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Kelime *</label>
                            <input type="text" name="kelime" class="form-control @error('kelime') is-invalid @enderror"
                                   value="{{ old('kelime', $kelime->kelime) }}" required>
                            @error('kelime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Eslesme Tipi *</label>
                            <select name="tip" class="form-select @error('tip') is-invalid @enderror" required>
                                <option value="icerir" {{ old('tip', $kelime->tip) == 'icerir' ? 'selected' : '' }}>
                                    Icerir (parcali eslesme)
                                </option>
                                <option value="tam_eslesme" {{ old('tip', $kelime->tip) == 'tam_eslesme' ? 'selected' : '' }}>
                                    Tam Eslesme (kelime siniri)
                                </option>
                            </select>
                            @error('tip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <strong>Icerir:</strong> "mal" kelimesi "normal" icinde de bulunur.<br>
                                <strong>Tam Eslesme:</strong> "mal" sadece ayri bir kelime olarak eslesir.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Uygulanacak Alanlar *</label>
                            @php
                                $mevcutAlanlar = old('uygulanacak_alanlar', $kelime->uygulanacak_alanlar ?? []);
                            @endphp
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]"
                                       value="urun_adi" id="alan_urun_adi" {{ in_array('urun_adi', $mevcutAlanlar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="alan_urun_adi">Ürün Adi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]"
                                       value="urun_aciklama" id="alan_urun_aciklama" {{ in_array('urun_aciklama', $mevcutAlanlar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="alan_urun_aciklama">Ürün Aciklama</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]"
                                       value="mesaj" id="alan_mesaj" {{ in_array('mesaj', $mevcutAlanlar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="alan_mesaj">Mesajlar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]"
                                       value="kullanici_adi" id="alan_kullanici_adi" {{ in_array('kullanici_adi', $mevcutAlanlar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="alan_kullanici_adi">Kullanici Adi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uygulanacak_alanlar[]"
                                       value="yorum" id="alan_yorum" {{ in_array('yorum', $mevcutAlanlar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="alan_yorum">Yorumlar</label>
                            </div>
                            @error('uygulanacak_alanlar')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Aksiyon *</label>
                            <select name="aksiyon" class="form-select @error('aksiyon') is-invalid @enderror" required onchange="toggleYerine(this)">
                                <option value="engelle" {{ old('aksiyon', $kelime->aksiyon) == 'engelle' ? 'selected' : '' }}>
                                    Engelle (kayit/gonderim engellenir)
                                </option>
                                <option value="sansurle" {{ old('aksiyon', $kelime->aksiyon) == 'sansurle' ? 'selected' : '' }}>
                                    Sansurle (kelime gizlenir)
                                </option>
                                <option value="uyar" {{ old('aksiyon', $kelime->aksiyon) == 'uyar' ? 'selected' : '' }}>
                                    Uyar (sadece log tutulur)
                                </option>
                            </select>
                            @error('aksiyon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="yerineDiv" style="{{ old('aksiyon', $kelime->aksiyon) == 'sansurle' ? '' : 'display:none;' }}">
                            <label class="form-label">Yerine Koyulacak</label>
                            <input type="text" name="yerine" class="form-control"
                                   value="{{ old('yerine', $kelime->yerine) }}" placeholder="*** veya [sansurlu]">
                            <small class="text-muted">Bos birakilirsa yildizla degistirilir</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                            <a href="{{ route('admin.lists.keywords') }}" class="btn btn-secondary">
                                Iptal
                            </a>
                        </div>
                    </form>
                </div>
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
