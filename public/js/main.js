// =====================================================
// E-TİCARET - ANA JAVASCRIPT DOSYASI (LARAVEL)
// =====================================================

// CSRF Token - Laravel için gerekli
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap navbar kullanıldığı için custom dropdown kodu kaldırıldı

    // Kullanıcı tipi seçimi
    initUserTypeSelect();

    // Kart formatı
    initCardFormatting();

    // Miktar kontrolleri
    initQuantityControls();

    // Sepete ekleme
    initAddToCart();

    // Favoriler
    initFavorites();

    // Görsel önizleme
    initImagePreview();

    // Form doğrulama
    initFormValidation();
});

// =====================================================
// KULLANICI TİPİ SEÇİMİ
// =====================================================
function initUserTypeSelect() {
    const userTypeOptions = document.querySelectorAll('.user-type-option');

    userTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            userTypeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
}

// =====================================================
// KART NUMARASI FORMATLAMA
// =====================================================
function initCardFormatting() {
    const kartNumarasi = document.getElementById('kart_numarasi');
    const cvv = document.getElementById('cvv');
    const kartOnizleme = document.getElementById('kartNumarasiOnizleme');
    const kartSahibiOnizleme = document.getElementById('kartSahibiOnizleme');
    const kartTarihOnizleme = document.getElementById('kartTarihOnizleme');

    if (kartNumarasi) {
        kartNumarasi.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.substring(0, 16);

            // 4'erli gruplar halinde formatla
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += '-';
                }
                formatted += value[i];
            }

            e.target.value = formatted;

            // Önizleme güncelle
            if (kartOnizleme) {
                let displayValue = formatted || '****-****-****-****';
                while (displayValue.replace(/-/g, '').length < 16) {
                    displayValue += '*';
                    if (displayValue.replace(/-/g, '').length % 4 === 0 && displayValue.replace(/-/g, '').length < 16) {
                        displayValue = displayValue.substring(0, displayValue.length) + '-';
                    }
                }
                kartOnizleme.textContent = displayValue.substring(0, 19);
            }
        });
    }

    // CVV sadece 3 hane
    if (cvv) {
        cvv.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.substring(0, 3);
        });
    }

    // Kart sahibi önizleme
    const kartSahibi = document.getElementById('kart_sahibi');
    if (kartSahibi && kartSahibiOnizleme) {
        kartSahibi.addEventListener('input', function(e) {
            kartSahibiOnizleme.textContent = e.target.value.toUpperCase() || 'AD SOYAD';
        });
    }

    // Tarih önizleme
    const sonKullanmaAy = document.getElementById('son_kullanma_ay');
    const sonKullanmaYil = document.getElementById('son_kullanma_yil');

    function updateDatePreview() {
        if (kartTarihOnizleme) {
            const ay = sonKullanmaAy ? sonKullanmaAy.value : 'AA';
            const yil = sonKullanmaYil ? sonKullanmaYil.value.substring(2) : 'YY';
            kartTarihOnizleme.textContent = `${ay || 'AA'}/${yil || 'YY'}`;
        }
    }

    if (sonKullanmaAy) sonKullanmaAy.addEventListener('change', updateDatePreview);
    if (sonKullanmaYil) sonKullanmaYil.addEventListener('change', updateDatePreview);
}

// =====================================================
// MİKTAR KONTROLLER
// =====================================================
function initQuantityControls() {
    document.querySelectorAll('.quantity-control').forEach(control => {
        const minusBtn = control.querySelector('.minus');
        const plusBtn = control.querySelector('.plus');
        const quantitySpan = control.querySelector('.quantity');
        const sepetId = control.dataset.sepetId;

        if (minusBtn && quantitySpan) {
            minusBtn.addEventListener('click', function() {
                let quantity = parseInt(quantitySpan.textContent);
                if (quantity > 1) {
                    quantity--;
                    updateQuantity(sepetId, quantity, quantitySpan);
                }
            });
        }

        if (plusBtn && quantitySpan) {
            plusBtn.addEventListener('click', function() {
                let quantity = parseInt(quantitySpan.textContent);
                quantity++;
                updateQuantity(sepetId, quantity, quantitySpan);
            });
        }
    });
}

function updateQuantity(sepetId, quantity, spanElement) {
    fetch('/sepet/' + sepetId, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            miktar: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            spanElement.textContent = quantity;
            // Sayfayı yenile toplam fiyat için
            location.reload();
        } else {
            showNotification(data.error || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Hata:', error);
    });
}

// =====================================================
// SEPETE EKLEME - layouts/app.blade.php'de tanımlı
// =====================================================
function initAddToCart() {
    // Bu fonksiyon artık kullanılmıyor
    // Sepete ekleme layouts/app.blade.php içinde yapılıyor
}

// =====================================================
// FAVORİLER - Form tabanlı, AJAX kullanılmıyor
// =====================================================
function initFavorites() {
    // Bu fonksiyon artık kullanılmıyor
    // Favoriler form submission ile yapılıyor
}

// =====================================================
// BİLDİRİM GÖSTERİCİ
// =====================================================
function showNotification(message, type = 'success') {
    // Önceki bildirimi kaldır
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;

    // Stil ekle
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#10B981' : '#EF4444'};
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(notification);

    // 3 saniye sonra kaldır
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Animasyon stilleri ekle
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// =====================================================
// GÖRSEL ÖNİZLEME
// =====================================================
function initImagePreview() {
    const imageUpload = document.querySelector('.image-upload');
    // Hem 'resim' hem de 'resimler' ID'lerini destekle
    const imageInput = document.getElementById('resimler') || document.getElementById('resim');
    const imagePreview = document.querySelector('.image-preview');
    const imagePreviews = document.getElementById('image-previews');

    if (imageUpload && imageInput) {
        // Tıklama ile dosya seçiciyi aç
        imageUpload.addEventListener('click', function(e) {
            // Input'un kendisine tıklanmadıysa
            if (e.target !== imageInput) {
                imageInput.click();
            }
        });

        // Dosya seçildiğinde önizleme göster
        imageInput.addEventListener('change', function() {
            // Çoklu resim için (resimler[])
            if (imagePreviews && this.files.length > 0) {
                imagePreviews.innerHTML = '';
                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.style.cssText = 'position:relative; width:100px; height:100px; border-radius:8px; overflow:hidden; border:3px solid ' + (index === 0 ? 'var(--primary)' : 'var(--border)') + ';';
                        div.innerHTML = `
                            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">
                            ${index === 0 ? '<span style="position:absolute; bottom:0; left:0; right:0; background:var(--primary); color:white; font-size:0.7rem; text-align:center; padding:2px;">Kapak</span>' : ''}
                        `;
                        imagePreviews.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
            // Tekli resim için (resim)
            else if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (imagePreview) {
                        imagePreview.innerHTML = `<img src="${e.target.result}" alt="Önizleme">`;
                    } else if (imageUpload) {
                        const preview = document.createElement('div');
                        preview.className = 'image-preview';
                        preview.innerHTML = `<img src="${e.target.result}" alt="Önizleme">`;
                        imageUpload.appendChild(preview);
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
}

// =====================================================
// FORM DOĞRULAMA
// =====================================================
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Gerekli alanları kontrol et
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#EF4444';
                } else {
                    field.style.borderColor = '';
                }
            });

            // Email formatı kontrolü
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.style.borderColor = '#EF4444';
                }
            });

            // Kart numarası kontrolü
            const kartField = document.getElementById('kart_numarasi');
            if (kartField && kartField.value) {
                const kartNumarasi = kartField.value.replace(/-/g, '');
                if (kartNumarasi.length !== 16) {
                    isValid = false;
                    kartField.style.borderColor = '#EF4444';
                }
            }

            // CVV kontrolü
            const cvvField = document.getElementById('cvv');
            if (cvvField && cvvField.value) {
                if (cvvField.value.length !== 3) {
                    isValid = false;
                    cvvField.style.borderColor = '#EF4444';
                }
            }

            if (!isValid) {
                e.preventDefault();
                showNotification('Lütfen tüm alanları doğru şekilde doldurun', 'error');
            }
        });
    });
}

// =====================================================
// ÜRÜN SİLME (Satıcı Paneli) - Form ile hallediliyor
// =====================================================
function deleteProduct(urunId) {
    if (confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
        // Form oluştur ve gönder
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/satici/urun/' + urunId;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = getCsrfToken();
        form.appendChild(csrfInput);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// =====================================================
// SEPETTEN ÜRÜN ÇIKARMA - Form ile hallediliyor
// =====================================================
function removeFromCart(sepetId) {
    if (confirm('Bu ürünü sepetten çıkarmak istiyor musunuz?')) {
        // Form oluştur ve gönder
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/sepet/' + sepetId;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = getCsrfToken();
        form.appendChild(csrfInput);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// =====================================================
// ÜRÜN RESİMLERİ LIGHTBOX
// =====================================================
let lightboxCurrentIndex = 0;
let lightboxImages = [];

document.addEventListener('DOMContentLoaded', function() {
    // Lightbox modal oluştur
    if (!document.getElementById('lightboxModal')) {
        const lightbox = document.createElement('div');
        lightbox.id = 'lightboxModal';
        lightbox.className = 'lightbox-modal';
        lightbox.innerHTML = `
            <span class="lightbox-close">&times;</span>
            <button class="lightbox-prev" onclick="lightboxPrevImage()"><i class="fas fa-chevron-left"></i></button>
            <button class="lightbox-next" onclick="lightboxNextImage()"><i class="fas fa-chevron-right"></i></button>
            <img src="" alt="Büyük Görüntü">
            <div class="lightbox-counter"></div>
        `;
        document.body.appendChild(lightbox);

        // Lightbox arkaplanına tıklayınca kapat
        lightbox.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Close butonu
        lightbox.querySelector('.lightbox-close').addEventListener('click', function(e) {
            e.stopPropagation();
            lightbox.classList.remove('active');
        });

        // Klavye kontrolleri
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightboxModal');
            if (!lightbox.classList.contains('active')) return;

            if (e.key === 'Escape') {
                lightbox.classList.remove('active');
            } else if (e.key === 'ArrowLeft') {
                lightboxPrevImage();
            } else if (e.key === 'ArrowRight') {
                lightboxNextImage();
            }
        });
    }

    // Sadece detay sayfasındaki ürün resimlerine lightbox ekle
    // Hem ana resim hem de thumbnail'leri al
    const mainImage = document.getElementById('mainProductImage');
    const thumbnailImages = document.querySelectorAll('.thumbnail-img');

    if (mainImage || thumbnailImages.length > 0) {
        // Eğer thumbnail varsa onları kullan, yoksa sadece ana resmi kullan
        if (thumbnailImages.length > 0) {
            lightboxImages = Array.from(thumbnailImages).map(img => img.src);

            // Thumbnail'lere click event ekle
            thumbnailImages.forEach((img, index) => {
                img.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    lightboxCurrentIndex = index;
                    showLightboxImage(index);
                });
            });
        } else if (mainImage && mainImage.src) {
            // Tek resim varsa array'e ekle
            lightboxImages = [mainImage.src];
        }

        // Ana resme de click event ekle
        if (mainImage) {
            mainImage.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Ana resmin hangi thumbnail'e karşılık geldiğini bul
                const currentSrc = this.src;
                const index = lightboxImages.findIndex(src => src === currentSrc);
                lightboxCurrentIndex = index >= 0 ? index : 0;
                showLightboxImage(lightboxCurrentIndex);
            });
        }
    }
});

function showLightboxImage(index) {
    const lightbox = document.getElementById('lightboxModal');
    const lightboxImg = lightbox.querySelector('img');
    const counter = lightbox.querySelector('.lightbox-counter');

    lightboxImg.src = lightboxImages[index];
    lightbox.classList.add('active');

    // Sayaç göster
    if (lightboxImages.length > 1) {
        counter.textContent = `${index + 1} / ${lightboxImages.length}`;
        counter.style.display = 'block';
    } else {
        counter.style.display = 'none';
    }

    // Butonları göster/gizle
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    prevBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';
    nextBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';

    // Detay sayfasındaki ana resmi güncelle
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        mainImage.src = lightboxImages[index];
    }

    // Detay sayfasındaki thumbnail'leri güncelle
    if (typeof updateThumbnailActive === 'function') {
        updateThumbnailActive(index);
    }

    // currentImageIndex'i de güncelle (detay sayfası için)
    if (typeof currentImageIndex !== 'undefined') {
        currentImageIndex = index;
    }
}

function lightboxPrevImage() {
    lightboxCurrentIndex = (lightboxCurrentIndex - 1 + lightboxImages.length) % lightboxImages.length;
    showLightboxImage(lightboxCurrentIndex);
}

function lightboxNextImage() {
    lightboxCurrentIndex = (lightboxCurrentIndex + 1) % lightboxImages.length;
    showLightboxImage(lightboxCurrentIndex);
}
