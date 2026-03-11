@extends('layouts.app')

@section('title', 'Ürün Düzenle - ZAMASON')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card" style="padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2><i class="fas fa-edit"></i> Ürün Düzenle</h2>
            <p style="color: var(--text-light);">{{ $urun->urun_adi }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $urun) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($urun->resimler->count() > 0)
                <div class="form-group">
                    <label>Mevcut Resimler</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach($urun->resimler as $resim)
                            <div style="position: relative; width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 2px solid var(--border);">
                                <img src="{{ asset('serve-image.php?p=urunler/' . $resim->resim) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label>Yeni Resimler Ekle</label>
                <div style="border: 2px dashed var(--border); border-radius: 12px; padding: 30px; text-align: center; cursor: pointer;"
                     onclick="document.getElementById('resimler').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--text-light); margin-bottom: 10px;"></i>
                    <p style="font-size: 0.9rem;">Yeni resim eklemek için tıklayın</p>
                    <input type="file" name="resimler[]" id="resimler" accept="image/*" multiple style="display: none;">
                </div>
                <div id="image-previews" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;"></div>
            </div>

            <div class="form-group">
                <label for="urun_adi">Ürün Adı *</label>
                <input type="text" id="urun_adi" name="urun_adi" value="{{ old('urun_adi', $urun->urun_adi) }}" required>
            </div>

            <div class="form-group">
                <label for="aciklama">Açıklama <small style="color: var(--text-light);">(maks. 1000 karakter)</small></label>
                <textarea id="aciklama" name="aciklama" rows="4" maxlength="1000" oninput="document.getElementById('aciklamaKarakter').textContent = this.value.length">{{ old('aciklama', $urun->aciklama) }}</textarea>
                <small style="color: var(--text-light);"><span id="aciklamaKarakter">{{ strlen(old('aciklama', $urun->aciklama ?? '')) }}</span>/1000 karakter</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fiyat">Fiyat (₺) *</label>
                    <input type="number" id="fiyat" name="fiyat" step="0.01" min="0" max="99999" value="{{ old('fiyat', $urun->fiyat) }}" required>
                    <small style="color: var(--text-light); font-size: 0.85rem;">Maksimum 99.999 TL</small>
                </div>
                <div class="form-group">
                    <label for="stok">Stok Miktarı *</label>
                    <input type="number" id="stok" name="stok" min="0" value="{{ old('stok', $urun->stok) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id">
                        <option value="">Kategori Seçin</option>
                        @foreach($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $urun->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="durum">Durum</label>
                    <select id="durum" name="durum">
                        <option value="aktif" {{ old('durum', $urun->durum) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="pasif" {{ old('durum', $urun->durum) == 'pasif' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
            </div>

            <!-- Dinamik Kategori Attribute'ları -->
            <div id="kategori-attributes" style="display: {{ $urun->attributeValues->count() > 0 ? 'block' : 'none' }};">
                <div style="margin: 20px 0; padding-top: 20px; border-top: 1px solid var(--border);">
                    <h4 style="margin-bottom: 15px;"><i class="fas fa-list-alt"></i> Kategori Özellikleri</h4>
                </div>
                <div id="attributes-container">
                    @if($urun->attributeValues->count() > 0)
                        @php
                            $existingValues = $urun->attributeValues->keyBy('attribute_id');
                        @endphp
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="il_id"><i class="fas fa-map-marker-alt"></i> İl *</label>
                    <select id="il_id" name="il_id" required>
                        <option value="">İl Seçin</option>
                        @foreach($iller as $il)
                            <option value="{{ $il->id }}" {{ old('il_id', $urun->il_id) == $il->id ? 'selected' : '' }}>
                                {{ $il->il_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="ilce_id"><i class="fas fa-map-pin"></i> İlçe *</label>
                    <select id="ilce_id" name="ilce_id" required>
                        <option value="">Önce İl Seçin</option>
                        @if($urun->ilce)
                            <option value="{{ $urun->ilce_id }}" selected>{{ $urun->ilce->ilce_adi }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="mahalle_id"><i class="fas fa-home"></i> Mahalle (opsiyonel)</label>
                <select id="mahalle_id" name="mahalle_id">
                    <option value="">Önce İlçe Seçin</option>
                    @if($urun->mahalle)
                        <option value="{{ $urun->mahalle_id }}" selected>{{ $urun->mahalle->mahalle_adi }}</option>
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label for="adres_detay"><i class="fas fa-road"></i> Sokak / Cadde / Bina No <small style="color: var(--text-light);">(maks. 200 karakter)</small></label>
                <textarea id="adres_detay" name="adres_detay" rows="2" maxlength="200" placeholder="Örn: Cumhuriyet Caddesi No: 45" oninput="document.getElementById('adresKarakter').textContent = this.value.length">{{ old('adres_detay', $urun->adres_detay) }}</textarea>
                <small style="color: var(--text-light); font-size: 0.85rem;">Opsiyonel - <span id="adresKarakter">{{ strlen(old('adres_detay', $urun->adres_detay ?? '')) }}</span>/200 karakter</small>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
                <a href="{{ route('seller.dashboard') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Mevcut attribute değerleri
const existingAttributeValues = {!! json_encode($urun->attributeValues->pluck('deger', 'attribute_id')) !!};

document.getElementById('resimler').addEventListener('change', function(e) {
    const previews = document.getElementById('image-previews');
    previews.innerHTML = '';

    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.style.cssText = 'width:80px; height:80px; border-radius:8px; overflow:hidden; border:2px solid var(--primary);';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
            previews.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

const ilceSelect = document.getElementById('ilce_id');
const mahalleSelect = document.getElementById('mahalle_id');
const kategoriSelect = document.getElementById('kategori_id');
const attributesSection = document.getElementById('kategori-attributes');
const attributesContainer = document.getElementById('attributes-container');

// Kategori değiştiğinde attribute'ları yükle
kategoriSelect.addEventListener('change', function() {
    loadCategoryAttributes(this.value);
});

function loadCategoryAttributes(kategoriId) {
    if (!kategoriId) {
        attributesSection.style.display = 'none';
        attributesContainer.innerHTML = '';
        return;
    }

    attributesSection.style.display = 'block';
    attributesContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-light);"><i class="fas fa-spinner fa-spin"></i> Özellikler yükleniyor...</div>';

    fetch(`/api/kategori/${kategoriId}/attributes`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.attributes || data.attributes.length === 0) {
                attributesSection.style.display = 'none';
                attributesContainer.innerHTML = '';
                return;
            }

            renderAttributes(data.attributes);
        })
        .catch(error => {
            console.error('Attribute yükleme hatası:', error);
            attributesSection.style.display = 'none';
            attributesContainer.innerHTML = '';
        });
}

function renderAttributes(attributes) {
    attributesContainer.innerHTML = '';

    let rowHtml = '<div class="form-row">';
    let itemCount = 0;

    attributes.forEach((attr, index) => {
        const isRequired = attr.zorunlu;
        const requiredMark = isRequired ? ' *' : '';
        const requiredAttr = isRequired ? 'required' : '';
        const existingValue = existingAttributeValues[attr.id] || '';

        let inputHtml = '';

        if (attr.tip === 'select' && attr.secenekler && attr.secenekler.length > 0) {
            inputHtml = `
                <select id="attr_${attr.id}" name="attributes[${attr.id}]" ${requiredAttr}>
                    <option value="">Seçin</option>
                    ${attr.secenekler.map(opt => `<option value="${opt}" ${existingValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            `;
        } else if (attr.tip === 'multiselect' && attr.secenekler && attr.secenekler.length > 0) {
            const selectedValues = existingValue ? existingValue.split(',') : [];
            inputHtml = `
                <select id="attr_${attr.id}" name="attributes[${attr.id}][]" multiple ${requiredAttr} style="min-height: 100px;">
                    ${attr.secenekler.map(opt => `<option value="${opt}" ${selectedValues.includes(opt) ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            `;
        } else if (attr.tip === 'number') {
            inputHtml = `<input type="number" id="attr_${attr.id}" name="attributes[${attr.id}]" value="${existingValue}" ${requiredAttr}>`;
        } else {
            inputHtml = `<input type="text" id="attr_${attr.id}" name="attributes[${attr.id}]" value="${existingValue}" ${requiredAttr}>`;
        }

        const fieldHtml = `
            <div class="form-group">
                <label for="attr_${attr.id}">${attr.label}${requiredMark}</label>
                ${inputHtml}
            </div>
        `;

        rowHtml += fieldHtml;
        itemCount++;

        if (itemCount % 2 === 0 && index < attributes.length - 1) {
            rowHtml += '</div><div class="form-row">';
        }
    });

    rowHtml += '</div>';
    attributesContainer.innerHTML = rowHtml;
}

// Sayfa yüklendiğinde mevcut kategorinin attribute'larını yükle
@if($urun->kategori_id)
loadCategoryAttributes({{ $urun->kategori_id }});
@endif

// İl değiştiğinde ilçeleri yükle
document.getElementById('il_id').addEventListener('change', function() {
    const ilId = this.value;

    ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    ilceSelect.disabled = true;
    mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
    mahalleSelect.disabled = true;

    if (!ilId) {
        ilceSelect.innerHTML = '<option value="">Önce İl Seçin</option>';
        ilceSelect.disabled = false;
        return;
    }

    fetch(`/api/address/districts/${ilId}`)
        .then(response => response.json())
        .then(data => {
            ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
            data.forEach(ilce => {
                const option = document.createElement('option');
                option.value = ilce.id;
                option.textContent = ilce.ilce_adi;
                ilceSelect.appendChild(option);
            });
            ilceSelect.disabled = false;
        })
        .catch(error => {
            console.error('İlçe yükleme hatası:', error);
            ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
            ilceSelect.disabled = false;
        });
});

// İlçe değiştiğinde mahalleleri yükle
ilceSelect.addEventListener('change', function() {
    const ilceId = this.value;

    mahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    mahalleSelect.disabled = true;

    if (!ilceId) {
        mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
        mahalleSelect.disabled = false;
        return;
    }

    fetch(`/api/address/neighborhoods/${ilceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                mahalleSelect.innerHTML = '<option value="">Mahalle bulunamadı (boş bırakabilirsiniz)</option>';
            } else {
                mahalleSelect.innerHTML = '<option value="">Mahalle Seçin (opsiyonel)</option>';
                data.forEach(mahalle => {
                    const option = document.createElement('option');
                    option.value = mahalle.id;
                    option.textContent = mahalle.mahalle_adi;
                    mahalleSelect.appendChild(option);
                });
            }
            mahalleSelect.disabled = false;
        })
        .catch(error => {
            console.error('Mahalle yükleme hatası:', error);
            mahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
            mahalleSelect.disabled = false;
        });
});

// Sayfa yüklendiğinde mevcut il için ilçeleri ve mahalleleri yükle
@if($urun->il_id)
(function() {
    const ilId = {{ $urun->il_id }};
    const mevcutIlceId = {{ $urun->ilce_id ?? 'null' }};
    const mevcutMahalleId = {{ $urun->mahalle_id ?? 'null' }};

    fetch(`/api/address/districts/${ilId}`)
        .then(response => response.json())
        .then(data => {
            ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
            data.forEach(ilce => {
                const option = document.createElement('option');
                option.value = ilce.id;
                option.textContent = ilce.ilce_adi;
                if (ilce.id == mevcutIlceId) option.selected = true;
                ilceSelect.appendChild(option);
            });

            // İlçe seçiliyse mahalleleri yükle
            if (mevcutIlceId) {
                fetch(`/api/address/neighborhoods/${mevcutIlceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            mahalleSelect.innerHTML = '<option value="">Mahalle bulunamadı (boş bırakabilirsiniz)</option>';
                        } else {
                            mahalleSelect.innerHTML = '<option value="">Mahalle Seçin (opsiyonel)</option>';
                            data.forEach(mahalle => {
                                const option = document.createElement('option');
                                option.value = mahalle.id;
                                option.textContent = mahalle.mahalle_adi;
                                if (mahalle.id == mevcutMahalleId) option.selected = true;
                                mahalleSelect.appendChild(option);
                            });
                        }
                    });
            }
        });
})();
@endif
</script>
@endpush
@endsection
