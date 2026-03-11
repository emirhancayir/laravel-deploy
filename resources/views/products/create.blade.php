@extends('layouts.app')

@section('title', 'Ürün Ekle - ' . config('app.name'))

@section('content')
<div class="product-form">
    <div class="form-title">
        <h2><i class="fas fa-plus-circle"></i> Yeni Ürün Ekle</h2>
        <p>Ürün bilgilerini doldurun</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" data-validate id="urun-form">
        @csrf

        <div class="image-upload">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Ürün resimleri yüklemek için tıklayın</p>
            <p style="font-size: 0.8rem; color: var(--text-light);">Birden fazla resim seçebilirsiniz (İlk resim kapak olur)</p>
            <p style="font-size: 0.8rem; color: var(--text-light);">JPEG, PNG, GIF, WebP - Max 10MB</p>
            <input type="file" name="resimler[]" id="resimler" accept="image/*" multiple style="display:none;">
        </div>
        <div id="image-previews" style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; padding: 10px; background: var(--bg-dark); border-radius: 8px; min-height: 60px;"></div>

        <div class="form-group">
            <label for="urun_adi">Ürün Adı *</label>
            <input type="text" id="urun_adi" name="urun_adi" value="{{ old('urun_adi') }}" required>
        </div>

        <div class="form-group">
            <label for="aciklama">Açıklama <small style="color: var(--text-light);">(maks. 1000 karakter)</small></label>
            <textarea id="aciklama" name="aciklama" rows="4" maxlength="1000" oninput="document.getElementById('aciklamaKarakter').textContent = this.value.length">{{ old('aciklama') }}</textarea>
            <small style="color: var(--text-light);"><span id="aciklamaKarakter">{{ strlen(old('aciklama', '')) }}</span>/1000 karakter</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fiyat">Fiyat (₺) *</label>
                <input type="number" id="fiyat" name="fiyat" step="0.01" min="0" value="{{ old('fiyat') }}" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok Miktarı *</label>
                <input type="number" id="stok" name="stok" min="0" value="{{ old('stok') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id">
                    <option value="">Kategori Seçin</option>
                    @foreach($kategoriler as $kategori)
                        <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->kategori_adi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="durum">Durum</label>
                <select id="durum" name="durum">
                    <option value="aktif" {{ old('durum', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="pasif" {{ old('durum') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
        </div>

        <!-- Dinamik Kategori Attribute'ları -->
        <div id="kategori-attributes" style="display: none;">
            <div class="form-title" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                <h4><i class="fas fa-list-alt"></i> Kategori Özellikleri</h4>
                <p style="font-size: 0.9rem;">Bu kategoriye özel ek bilgiler</p>
            </div>
            <div id="attributes-container"></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="il_id"><i class="fas fa-map-marker-alt"></i> İl *</label>
                <select id="il_id" name="il_id" required>
                    <option value="">İl Seçin</option>
                    @foreach($iller as $il)
                        <option value="{{ $il->id }}" {{ old('il_id') == $il->id ? 'selected' : '' }}>
                            {{ $il->il_adi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="ilce_id"><i class="fas fa-map-pin"></i> İlçe *</label>
                <select id="ilce_id" name="ilce_id" required>
                    <option value="">Önce İl Seçin</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="mahalle_id"><i class="fas fa-home"></i> Mahalle</label>
            <select id="mahalle_id" name="mahalle_id">
                <option value="">Önce İlçe Seçin</option>
            </select>
            <small style="color: var(--text-light); font-size: 0.85rem;">Opsiyonel - Listelenmemişse boş bırakabilirsiniz</small>
        </div>

        <div class="form-group">
            <label for="adres_detay"><i class="fas fa-road"></i> Sokak / Cadde / Bina No <small style="color: var(--text-light);">(maks. 200 karakter)</small></label>
            <textarea id="adres_detay" name="adres_detay" rows="2" maxlength="200" placeholder="Örn: Cumhuriyet Caddesi No: 45" oninput="document.getElementById('adresKarakter').textContent = this.value.length">{{ old('adres_detay') }}</textarea>
            <small style="color: var(--text-light); font-size: 0.85rem;">Opsiyonel - <span id="adresKarakter">{{ strlen(old('adres_detay', '')) }}</span>/200 karakter</small>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-save"></i> Ürünü Kaydet
            </button>
            <a href="{{ route('seller.dashboard') }}" class="btn btn-outline" style="flex: 1;">
                <i class="fas fa-times"></i> İptal
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
const ilceSelect = document.getElementById('ilce_id');
const mahalleSelect = document.getElementById('mahalle_id');
const kategoriSelect = document.getElementById('kategori_id');
const attributesSection = document.getElementById('kategori-attributes');
const attributesContainer = document.getElementById('attributes-container');

// Kategori değiştiğinde attribute'ları yükle
kategoriSelect.addEventListener('change', function() {
    const kategoriId = this.value;

    if (!kategoriId) {
        attributesSection.style.display = 'none';
        attributesContainer.innerHTML = '';
        return;
    }

    // Loading göster
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
});

// Attribute'ları render et
function renderAttributes(attributes) {
    attributesContainer.innerHTML = '';

    // İki sütunlu layout için grup
    let rowHtml = '<div class="form-row">';
    let itemCount = 0;

    attributes.forEach((attr, index) => {
        const isRequired = attr.zorunlu;
        const requiredMark = isRequired ? ' *' : '';
        const requiredAttr = isRequired ? 'required' : '';
        const oldValue = getOldValue('attributes', attr.id);

        let inputHtml = '';

        if (attr.tip === 'select' && attr.secenekler && attr.secenekler.length > 0) {
            inputHtml = `
                <select id="attr_${attr.id}" name="attributes[${attr.id}]" ${requiredAttr}>
                    <option value="">Seçin</option>
                    ${attr.secenekler.map(opt => `<option value="${opt}" ${oldValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            `;
        } else if (attr.tip === 'multiselect' && attr.secenekler && attr.secenekler.length > 0) {
            inputHtml = `
                <select id="attr_${attr.id}" name="attributes[${attr.id}][]" multiple ${requiredAttr} style="min-height: 100px;">
                    ${attr.secenekler.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                </select>
            `;
        } else if (attr.tip === 'number') {
            inputHtml = `<input type="number" id="attr_${attr.id}" name="attributes[${attr.id}]" value="${oldValue}" ${requiredAttr}>`;
        } else {
            inputHtml = `<input type="text" id="attr_${attr.id}" name="attributes[${attr.id}]" value="${oldValue}" ${requiredAttr}>`;
        }

        const fieldHtml = `
            <div class="form-group">
                <label for="attr_${attr.id}">${attr.label}${requiredMark}</label>
                ${inputHtml}
            </div>
        `;

        rowHtml += fieldHtml;
        itemCount++;

        // Her 2 item'da bir yeni row
        if (itemCount % 2 === 0 && index < attributes.length - 1) {
            rowHtml += '</div><div class="form-row">';
        }
    });

    rowHtml += '</div>';
    attributesContainer.innerHTML = rowHtml;
}

// Old value helper (basit implementasyon)
function getOldValue(field, attrId) {
    return '';
}

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

// Resim boyutlandırma fonksiyonu
function resizeImage(file, maxWidth = 1920, quality = 0.8) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function() {
                let width = img.width;
                let height = img.height;

                // Eğer resim zaten küçükse, olduğu gibi bırak
                if (width <= maxWidth) {
                    resolve(file);
                    return;
                }

                // Oranı koruyarak yeniden boyutlandır
                const ratio = maxWidth / width;
                width = maxWidth;
                height = height * ratio;

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {
                    // Yeni dosya oluştur
                    const newFile = new File([blob], file.name, {
                        type: file.type,
                        lastModified: Date.now()
                    });
                    resolve(newFile);
                }, file.type, quality);
            };
            img.onerror = reject;
        };
        reader.onerror = reject;
    });
}

// Resim yönetimi için global array
let uploadedFiles = [];

// Resim seçildiğinde otomatik boyutlandır
const resimInput = document.getElementById('resimler');
resimInput.addEventListener('change', async function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('image-previews');

    if (files.length === 0) return;

    // Loading göster
    previewContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-light);"><i class="fas fa-spinner fa-spin"></i> Resimler işleniyor...</div>';

    try {
        // Tüm resimleri boyutlandır
        const resizedFiles = await Promise.all(
            files.map(file => resizeImage(file))
        );

        // Global array'e ekle
        uploadedFiles = resizedFiles;

        // Preview göster
        renderPreviews();
    } catch (error) {
        console.error('Resim işleme hatası:', error);
        previewContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Resimler işlenirken hata oluştu</div>';
    }
});

// Preview'ları render et
function renderPreviews() {
    const previewContainer = document.getElementById('image-previews');
    previewContainer.innerHTML = '';

    if (uploadedFiles.length === 0) {
        previewContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-light);">Henüz resim seçilmedi</div>';
        return;
    }

    uploadedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.createElement('div');
            preview.className = 'preview-item';
            preview.draggable = true;
            preview.dataset.index = index;
            preview.style.cssText = 'position: relative; width: 120px; height: 120px; cursor: move;';
            preview.innerHTML = `
                <img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; object-position: center; border-radius: 8px; border: 3px solid ${index === 0 ? 'var(--primary)' : 'var(--border)'};">

                ${index === 0 ? '<div style="position: absolute; top: 5px; left: 5px; background: var(--primary); color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="fas fa-star"></i> KAPAK</div>' :
                `<button type="button" onclick="makeCover(${index})" style="position: absolute; top: 5px; left: 5px; background: rgba(0,0,0,0.7); color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer;"><i class="fas fa-star"></i> Kapak Yap</button>`}

                <button type="button" onclick="removeImage(${index})" style="position: absolute; top: 5px; right: 5px; background: var(--danger); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; font-size: 16px; line-height: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>

                <div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.7); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.7rem;">${index + 1}</div>
            `;

            // Drag & Drop events
            preview.addEventListener('dragstart', handleDragStart);
            preview.addEventListener('dragover', handleDragOver);
            preview.addEventListener('drop', handleDrop);
            preview.addEventListener('dragend', handleDragEnd);

            previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
    });

    // Input'u güncelle
    updateFileInput();
}

// Resmi sil
function removeImage(index) {
    uploadedFiles.splice(index, 1);
    renderPreviews();
}

// Kapak yap
function makeCover(index) {
    // İlk sıraya taşı
    const file = uploadedFiles.splice(index, 1)[0];
    uploadedFiles.unshift(file);
    renderPreviews();
}

// Drag & Drop fonksiyonları
let draggedIndex = null;

function handleDragStart(e) {
    const item = e.target.classList.contains('preview-item') ? e.target : e.target.closest('.preview-item');
    if (!item) return;
    draggedIndex = parseInt(item.dataset.index);
    item.style.opacity = '0.5';
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();

    const item = e.target.classList.contains('preview-item') ? e.target : e.target.closest('.preview-item');
    if (!item) return;

    const dropIndex = parseInt(item.dataset.index);

    if (draggedIndex !== null && draggedIndex !== dropIndex) {
        // Swap files
        const draggedFile = uploadedFiles[draggedIndex];
        uploadedFiles.splice(draggedIndex, 1);
        uploadedFiles.splice(dropIndex, 0, draggedFile);
        renderPreviews();
    }
    return false;
}

function handleDragEnd(e) {
    const item = e.target.classList.contains('preview-item') ? e.target : e.target.closest('.preview-item');
    if (item) item.style.opacity = '1';
    draggedIndex = null;
}

// File input'u güncelle
function updateFileInput() {
    const dataTransfer = new DataTransfer();
    uploadedFiles.forEach(file => dataTransfer.items.add(file));
    resimInput.files = dataTransfer.files;
}

// Form gönderilmeden önce boyutlandırılmış dosyaları kullan
document.getElementById('urun-form').addEventListener('submit', function(e) {
    // Eğer boyutlandırılmış dosyalar varsa, onları kullan
    if (uploadedFiles.length > 0) {
        updateFileInput();
    }

    // Boyut kontrolü - 10MB'dan büyük dosyaları kontrol et
    const maxSize = 10 * 1024 * 1024; // 10MB
    for (const file of resimInput.files) {
        if (file.size > maxSize) {
            e.preventDefault();
            showToast('Bazı resimler 10MB\'dan büyük. Lütfen daha küçük dosyalar seçin.', 'error');
            return false;
        }
    }

    // Submit butonunu devre dışı bırak (çift gönderimi önle)
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';
    }
});

// Sayfa yüklendiğinde eğer il seçiliyse ilçeleri yükle
@if(old('il_id'))
document.getElementById('il_id').dispatchEvent(new Event('change'));
setTimeout(() => {
    ilceSelect.value = "{{ old('ilce_id') }}";
    ilceSelect.dispatchEvent(new Event('change'));
    setTimeout(() => {
        mahalleSelect.value = "{{ old('mahalle_id') }}";
    }, 500);
}, 500);
@endif
</script>
@endpush
