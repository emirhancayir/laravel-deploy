@extends('layouts.app')

@section('title', 'Profilim - ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
/* Dark Mode için Düzeltmeler */
[data-theme="dark"] .profile-container,
[data-theme="dark"] .profile-header,
[data-theme="dark"] .profile-form {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .profile-header {
    background: #1e1e2e !important;
}

[data-theme="dark"] .profile-form {
    background: #1e1e2e !important;
}

[data-theme="dark"] .profile-info h2 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .profile-info p {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .profile-form h3,
[data-theme="dark"] .profile-form h4 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .profile-form p {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .upload-label {
    background: #252541 !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .upload-label span {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .upload-label small {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .upload-preview-container {
    background: #252541 !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .upload-info p {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .upload-info small {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .crop-container {
    background: #1e1e2e !important;
}

[data-theme="dark"] .crop-header h3 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .crop-tool-btn {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] label {
    color: #e0e0e0 !important;
}

[data-theme="dark"] small {
    color: #a0a0a0 !important;
}

/* 2FA Section Override - Daha Spesifik */
[data-theme="dark"] h4 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] span {
    color: inherit !important;
}

/* Tüm inline style override'ları */
[data-theme="dark"] [style*="color:black"],
[data-theme="dark"] [style*="color: black"],
[data-theme="dark"] [style*="color:#555"],
[data-theme="dark"] [style*="color: #555"],
[data-theme="dark"] [style*="color:#2e7d32"],
[data-theme="dark"] [style*="color: #2e7d32"],
[data-theme="dark"] [style*="color:#e65100"],
[data-theme="dark"] [style*="color: #e65100"],
[data-theme="dark"] [style*="color:#1976d2"],
[data-theme="dark"] [style*="color: #1976d2"] {
    color: #e0e0e0 !important;
}

/* 2FA Background Override */
[data-theme="dark"] [style*="linear-gradient(135deg, #e3f2fd"] {
    background: linear-gradient(135deg, #1e3a5a 0%, #2a4a6a 100%) !important;
    border-color: #3a5a7a !important;
}

/* 2FA Icon Color */
[data-theme="dark"] .fas.fa-shield-alt[style*="color"] {
    color: #ffa726 !important;
}

/* 2FA Status Colors */
[data-theme="dark"] .fas.fa-check-circle,
[data-theme="dark"] .fas.fa-exclamation-triangle {
    color: inherit !important;
}

/* Button with inline style */
[data-theme="dark"] .btn-outline[style*="border-color"],
[data-theme="dark"] .btn-outline[style*="color"] {
    border-color: #ffa726 !important;
    color: #ffa726 !important;
}

.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-header {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 25px;
    margin-bottom: 30px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info h2 {
    margin-bottom: 5px;
}

.profile-info p {
    color: var(--text-secondary);
    margin-bottom: 5px;
}

.profile-badge {
    display: inline-block;
    padding: 5px 15px;
    background: rgba(79, 70, 229, 0.1);
    color: var(--primary);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.profile-form {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
}

.profile-form h3 {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border);
}

.profile-upload-area {
    margin-bottom: 20px;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    border: 2px dashed var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--background);
}

.upload-label:hover {
    border-color: var(--primary);
    background: rgba(79, 70, 229, 0.05);
}

.upload-label i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 10px;
}

.upload-label span {
    font-weight: 500;
    margin-bottom: 5px;
}

.upload-label small {
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.upload-preview-container {
    display: flex;
    align-items: center;
    gap: 25px;
    padding: 20px;
    background: var(--background);
    border-radius: var(--radius);
    border: 2px dashed var(--border);
}

.upload-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    overflow: hidden;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
}

.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.preview-overlay i {
    font-size: 1.5rem;
    margin-bottom: 5px;
}

.preview-overlay span {
    font-size: 0.85rem;
    font-weight: 500;
}

.upload-preview:hover .preview-overlay {
    opacity: 1;
}

.upload-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.upload-info p {
    color: var(--text-primary);
    font-weight: 500;
    margin: 0;
}

.upload-info p.selected {
    color: var(--success);
}

.upload-info small {
    color: var(--text-secondary);
}

/* Cropper Modal */
.crop-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.crop-modal.active {
    display: flex;
}

.crop-container {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 20px;
    max-width: 600px;
    width: 95%;
    max-height: 90vh;
    overflow: hidden;
}

.crop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border);
}

.crop-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.crop-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
    padding: 5px;
}

.crop-close:hover {
    color: var(--error);
}

.crop-image-container {
    width: 100%;
    max-height: 400px;
    background: #1a1a1a;
    border-radius: 8px;
    overflow: hidden;
}

.crop-image-container img {
    display: block;
    max-width: 100%;
}

.crop-tools {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
}

.crop-tool-btn {
    width: 40px;
    height: 40px;
    border: 2px solid var(--border);
    background: var(--surface);
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    color: var(--text-primary);
}

.crop-tool-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.crop-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar" id="avatarPreview">
            @if($kullanici->profil_resmi)
                <img src="{{ asset('serve-image.php?p=profil/' . $kullanici->profil_resmi) }}" alt="Profil">
            @else
                {{ strtoupper(substr($kullanici->ad ?? '', 0, 1) . substr($kullanici->soyad ?? '', 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <h2>{{ $kullanici->ad }} {{ $kullanici->soyad }}</h2>
            <p><i class="fas fa-envelope"></i> {{ $kullanici->email }}</p>
            <span class="profile-badge">
                <i class="fas {{ $kullanici->kullanici_tipi === 'satici' ? 'fa-store' : 'fa-shopping-bag' }}"></i>
                {{ $kullanici->kullanici_tipi === 'satici' ? 'Satıcı' : 'Alıcı' }}
            </span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('başarılı'))
        <div class="alert alert-success">
            {{ session('başarılı') }}
        </div>
    @endif

    <div class="profile-form">
        <h3><i class="fas fa-user-edit"></i> Profil Bilgilerini Düzenle</h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" data-validate>
            @csrf

            <div class="form-group">
                <label><i class="fas fa-camera"></i> Profil Resmi</label>
                <div class="profile-upload-area">
                    <input type="file" id="profil_resmi" name="profil_resmi" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">

                    <div class="upload-preview-container">
                        <!-- Önizleme Alanı -->
                        <div class="upload-preview" id="uploadPreview">
                            @if($kullanici->profil_resmi)
                                <img src="{{ asset('serve-image.php?p=profil/' . $kullanici->profil_resmi) }}" alt="Mevcut Profil" id="previewImage">
                                <div class="preview-overlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Değiştir</span>
                                </div>
                            @else
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                <img src="" alt="Önizleme" id="previewImage" style="display: none;">
                                <div class="preview-overlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Seç</span>
                                </div>
                            @endif
                        </div>

                        <!-- Bilgi ve Buton -->
                        <div class="upload-info">
                            <label for="profil_resmi" class="btn btn-outline" style="cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt"></i> Resim Seç
                            </label>
                            <p id="selectedFileName">Henüz resim seçilmedi</p>
                            <small>JPG, PNG, GIF veya WEBP (max 5MB)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="ad">Ad *</label>
                    <input type="text" id="ad" name="ad" value="{{ old('ad', $kullanici->ad) }}" required>
                </div>
                <div class="form-group">
                    <label for="soyad">Soyad *</label>
                    <input type="text" id="soyad" name="soyad" value="{{ old('soyad', $kullanici->soyad) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="{{ $kullanici->email }}" disabled
                       style="background: var(--background); cursor: not-allowed;">
                <small style="color: var(--text-secondary);">Email adresi değiştirilemez.</small>
            </div>

            <div class="form-group">
                <label for="telefon">Telefon</label>
                <input type="tel" id="telefon" name="telefon" value="{{ old('telefon', $kullanici->telefon) }}"
                       placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label for="adres">Adres</label>
                <textarea id="adres" name="adres" rows="3"
                          placeholder="Teslimat adresiniz">{{ old('adres', $kullanici->adres) }}</textarea>
            </div>

            <h3 style="margin-top: 30px;"><i class="fas fa-lock"></i> Şifre Değiştir</h3>
            <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 0.9rem;">
                Şifrenizi değiştirmek istemiyorsanız bu alanları boş bırakın.
            </p>

            <div class="form-row">
                <div class="form-group">
                    <label for="yeni_sifre">Yeni Şifre</label>
                    <input type="password" id="yeni_sifre" name="yeni_sifre" minlength="6">
                </div>
                <div class="form-group">
                    <label for="yeni_sifre_confirmation">Yeni Şifre (Tekrar)</label>
                    <input type="password" id="yeni_sifre_confirmation" name="yeni_sifre_confirmation">
                </div>
            </div>

            <!-- 2FA Section -->
            <div style="margin-top: 25px; padding: 20px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 10px; border: 1px solid #90caf9;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #1976d2;"></i>
                        <div>
                            <h4 style="display: block;color:black;">İki Faktörlü Doğrulama (2FA)</h4>
                            <span style="font-size: 0.9rem; color: #555;">
                                @if(auth()->user()->twoFactorEnabled())
                                    <span style="color: #2e7d32;"><i class="fas fa-check-circle"></i> Etkin</span>
                                @else
                                    <span style="color: #e65100;"><i class="fas fa-exclamation-triangle"></i> Devre disi</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('profile.2fa') }}" class="btn btn-outline" style="border-color: #1976d2; color: #1976d2;">
                        <i class="fas fa-cog"></i> Ayarlar
                    </a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                <i class="fas fa-save"></i> Değişiklikleri Kaydet
            </button>
        </form>
    </div>
</div>

<!-- Crop Modal -->
<div class="crop-modal" id="cropModal">
    <div class="crop-container">
        <div class="crop-header">
            <h3><i class="fas fa-crop-alt"></i> Fotoğrafı Ayarla</h3>
            <button type="button" class="crop-close" id="cropClose">&times;</button>
        </div>
        <div class="crop-image-container">
            <img id="cropImage" src="" alt="Kırpılacak resim">
        </div>
        <div class="crop-tools">
            <button type="button" class="crop-tool-btn" id="zoomIn" title="Yakınlaştır">
                <i class="fas fa-search-plus"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="zoomOut" title="Uzaklaştır">
                <i class="fas fa-search-minus"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="rotateLeft" title="Sola Döndür">
                <i class="fas fa-undo"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="rotateRight" title="Sağa Döndür">
                <i class="fas fa-redo"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="flipH" title="Yatay Çevir">
                <i class="fas fa-arrows-alt-h"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="flipV" title="Dikey Çevir">
                <i class="fas fa-arrows-alt-v"></i>
            </button>
            <button type="button" class="crop-tool-btn" id="reset" title="Sıfırla">
                <i class="fas fa-sync"></i>
            </button>
        </div>
        <div class="crop-actions">
            <button type="button" class="btn btn-outline" id="cropCancel">
                <i class="fas fa-times"></i> İptal
            </button>
            <button type="button" class="btn btn-primary" id="cropApply">
                <i class="fas fa-save"></i> Uygula ve Kaydet
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
let cropper = null;
let croppedBlob = null;
const cropModal = document.getElementById('cropModal');
const cropImage = document.getElementById('cropImage');
const fileInput = document.getElementById('profil_resmi');

// Önizleme alanına tıklayınca dosya seç
document.getElementById('uploadPreview').addEventListener('click', function() {
    fileInput.click();
});

// Dosya seçildiğinde crop modal aç
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];

    if (file) {
        // Dosya boyutu kontrolü (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showToast('Dosya boyutu 5MB\'dan büyük olamaz!', 'error');
            this.value = '';
            return;
        }

        // Resmi modal'da göster
        const reader = new FileReader();
        reader.onload = function(e) {
            cropImage.src = e.target.result;
            cropModal.classList.add('active');

            // Cropper'ı başlat
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                cropBoxResizable: true,
                cropBoxMovable: true,
                guides: true,
                center: true,
                highlight: true,
                background: true,
            });
        };
        reader.readAsDataURL(file);
    }
});

// Zoom In
document.getElementById('zoomIn').addEventListener('click', function() {
    if (cropper) cropper.zoom(0.1);
});

// Zoom Out
document.getElementById('zoomOut').addEventListener('click', function() {
    if (cropper) cropper.zoom(-0.1);
});

// Rotate Left
document.getElementById('rotateLeft').addEventListener('click', function() {
    if (cropper) cropper.rotate(-90);
});

// Rotate Right
document.getElementById('rotateRight').addEventListener('click', function() {
    if (cropper) cropper.rotate(90);
});

// Flip Horizontal
document.getElementById('flipH').addEventListener('click', function() {
    if (cropper) {
        const data = cropper.getData();
        cropper.scaleX(data.scaleX === -1 ? 1 : -1);
    }
});

// Flip Vertical
document.getElementById('flipV').addEventListener('click', function() {
    if (cropper) {
        const data = cropper.getData();
        cropper.scaleY(data.scaleY === -1 ? 1 : -1);
    }
});

// Reset
document.getElementById('reset').addEventListener('click', function() {
    if (cropper) cropper.reset();
});

// Modal kapat
function closeModal() {
    cropModal.classList.remove('active');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

document.getElementById('cropClose').addEventListener('click', closeModal);
document.getElementById('cropCancel').addEventListener('click', function() {
    closeModal();
    fileInput.value = '';
});

// Uygula ve Kaydet
document.getElementById('cropApply').addEventListener('click', function() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Önizlemeyi güncelle
        const previewImage = document.getElementById('previewImage');
        const placeholder = document.getElementById('uploadPlaceholder');
        const avatarPreview = document.getElementById('avatarPreview');
        const fileNameP = document.getElementById('selectedFileName');

        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

        previewImage.src = croppedDataUrl;
        previewImage.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (avatarPreview) {
            avatarPreview.innerHTML = '<img src="' + croppedDataUrl + '" alt="Önizleme">';
        }

        fileNameP.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';
        fileNameP.classList.add('selected');

        // Blob olarak kaydet ve formu gönder
        canvas.toBlob(function(blob) {
            // Yeni file oluştur ve input'a ata
            const croppedFile = new File([blob], 'profil.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            fileInput.files = dataTransfer.files;

            closeModal();

            // Formu otomatik gönder
            setTimeout(function() {
                document.querySelector('form[action*="profil"]').submit();
            }, 100);
        }, 'image/jpeg', 0.9);
    }
});

// ESC tuşu ile kapat
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && cropModal.classList.contains('active')) {
        closeModal();
        fileInput.value = '';
    }
});
</script>
@endsection
