@extends('layouts.app')

@section('title', 'Sohbet - ' . $konusma->urun->urun_adi . ' - ZAMASON')

@section('content')
{{-- Mesaj hatalari icin toast --}}
@if($errors->has('mesaj'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ $errors->first('mesaj') }}', 'error');
        });
    </script>
@endif
@php
    $karsiTaraf = $konusma->karsiTaraf(auth()->user());
    $benAliciyim = $konusma->alici_id === auth()->id();
    $kabulEdilenTeklif = $konusma->teklifler->where('durum', 'kabul_edildi')->first();
@endphp

<div class="sohbet-container">
    <!-- Sol Panel: Ürün ve Teklif Bilgisi -->
    <div class="sohbet-sol-panel">
        <div class="card" style="padding: 20px;">
            <!-- Ürün Bilgisi -->
            <a href="{{ route('products.show', $konusma->urun) }}" class="urun-ozet">
                @if($konusma->urun->resim)
                    <img src="{{ asset('serve-image.php?p=urunler/' . $konusma->urun->resim) }}" alt="">
                @elseif($konusma->urun->resimler->isNotEmpty())
                    <img src="{{ asset('serve-image.php?p=urunler/' . $konusma->urun->resimler->first()->resim) }}" alt="">
                @else
                    <div class="urun-placeholder"><i class="fas fa-image"></i></div>
                @endif
                <div class="urun-detay">
                    <h4>{{ Str::limit($konusma->urun->urun_adi, 40) }}</h4>
                    <div class="urun-fiyat">{{ $konusma->urun->formatli_fiyat }}</div>
                </div>
            </a>

            <hr style="margin: 20px 0; border-color: var(--border);">

            <!-- Teklif Bolumu -->
            <div class="teklif-bolumu">
                <h4 style="margin-bottom: 15px;"><i class="fas fa-hand-holding-usd"></i> Teklif Ver</h4>

                @php
                    $aktifTeklif = $konusma->teklifler->where('durum', 'beklemede')->first();
                @endphp

                @if($aktifTeklif)
                    <div class="aktif-teklif" id="aktifTeklifBox" data-teklif-id="{{ $aktifTeklif->id }}">
                        <div class="teklif-bilgi">
                            <span class="teklif-tutar">{{ $aktifTeklif->formatli_tutar }}</span>
                            <span class="teklif-durum beklemede" id="teklifDurum">Beklemede</span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-light); margin: 10px 0;">
                            {{ $aktifTeklif->teklifEden->ad }} tarafindan
                        </p>

                        @if($aktifTeklif->teklif_eden_id === auth()->id())
                            <!-- Teklifi ben gonderdim, iptal edebilirim -->
                            <button type="button" class="btn btn-outline" style="width: 100%;" onclick="teklifIslem('iptal', {{ $aktifTeklif->id }})">
                                <i class="fas fa-times"></i> Teklifi Iptal Et
                            </button>
                        @else
                            <!-- Teklif bana geldi, kabul/red edebilirim -->
                            <div style="display: flex; gap: 10px;" id="teklifButonlar">
                                <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="teklifIslem('kabul', {{ $aktifTeklif->id }})">
                                    <i class="fas fa-check"></i> Kabul Et
                                </button>
                                <button type="button" class="btn btn-danger" style="flex: 1;" onclick="teklifIslem('reddet', {{ $aktifTeklif->id }})">
                                    <i class="fas fa-times"></i> Reddet
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <form action="{{ route('offer.store', $konusma) }}" method="POST" id="teklifForm">
                        @csrf
                        <div class="form-group">
                            <label>Teklif Tutarı (TL)</label>
                            <input type="number" name="tutar" step="0.01" min="1" max="99999"
                                   placeholder="Ornegin: {{ number_format($konusma->urun->fiyat * 0.9, 0) }}"
                                   class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>Not (Opsiyonel)</label>
                            <textarea name="not" rows="2" class="form-input"
                                      placeholder="Teklifinize eklemek istediğiniz not..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Teklif Gonder
                        </button>
                    </form>
                @endif
            </div>

            <!-- Kabul Edilen Teklif -->
            @if($kabulEdilenTeklif)
            <hr style="margin: 20px 0; border-color: var(--border);">

            <div style="background: linear-gradient(135deg, #38ef7d15 0%, #11998e15 100%); border: 1px solid var(--success); padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 2rem; margin-bottom: 10px;"></i>
                <h4 style="color: var(--success); margin-bottom: 10px;">Teklif Kabul Edildi!</h4>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    {{ number_format($kabulEdilenTeklif->tutar, 2, ',', '.') }} ₺
                </div>

                @if($benAliciyim && !$konusma->urun->satildi)
                    @php
                        $sepetteVar = \App\Models\SepetItem::where('kullanici_id', auth()->id())
                            ->where('teklif_id', $kabulEdilenTeklif->id)
                            ->exists();
                    @endphp

                    @if($sepetteVar)
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary" style="width: 100%; margin-top: 15px;">
                            <i class="fas fa-shopping-cart"></i> Sepete Git
                        </a>
                    @else
                        <form action="{{ route('cart.add', $kabulEdilenTeklif) }}" method="POST" style="margin-top: 15px;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-cart-plus"></i> Sepete Ekle
                            </button>
                        </form>
                    @endif
                @elseif($konusma->urun->satildi)
                    <p style="font-size: 0.85rem; color: var(--warning); margin-top: 10px;">
                        <i class="fas fa-info-circle"></i> Bu ürün satılmıştır.
                    </p>
                @else
                    <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 10px;">
                        Alıcının ödeme yapmasını bekleyin.
                    </p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Sag Panel: Chat -->
    <div class="sohbet-sag-panel">
        <div class="card sohbet-card">
            <!-- Chat Header -->
            <div class="sohbet-header">
                <a href="{{ route('chat.index') }}" class="btn btn-outline" style="padding: 8px 12px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="sohbet-header-bilgi">
                    <div class="sohbet-avatar">
                        @if($karsiTaraf && $karsiTaraf->profil_resmi)
                            <img src="{{ asset('serve-image.php?p=profil/' . $karsiTaraf->profil_resmi) }}" alt="">
                        @else
                            <span>{{ $karsiTaraf ? strtoupper(substr($karsiTaraf->ad, 0, 1)) : '?' }}</span>
                        @endif
                    </div>
                    <div>
                        <strong>{{ $karsiTaraf ? $karsiTaraf->ad_soyad : 'Bilinmeyen' }}</strong>
                        <span class="user-status">{{ $benAliciyim ? 'Satici' : 'Alici' }}</span>
                    </div>
                </div>
                <div class="sohbet-menu">
                    <button class="btn btn-outline" style="padding: 8px 12px;" onclick="toggleSohbetMenu()">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="sohbet-menu-dropdown" id="sohbetMenuDropdown" style="display: none;">
                        <button onclick="sohbetiTemizle()">
                            <i class="fas fa-broom"></i> Sohbeti Temizle
                        </button>
                        <form action="{{ route('chat.archive', $konusma) }}" method="POST" style="margin: 0;" id="arsivleForm">
                            @csrf
                            <button type="button" onclick="sohbetiArsivle()">
                                <i class="fas fa-archive"></i> Arşivle
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mesajlar -->
            <div class="sohbet-mesajlar" id="mesajlarContainer">
                @forelse($konusma->mesajlar as $mesaj)
                    @include('chat.partials.mesaj', ['mesaj' => $mesaj])
                @empty
                    <div class="mesaj sistem">Henüz mesaj yok</div>
                @endforelse
            </div>

            <!-- Mesaj Gönderme Formu (AJAX) -->
            <div class="sohbet-form">
                <form id="mesajForm" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="mesaj" id="mesajInput" placeholder="Mesajınızı yazın..."
                           autocomplete="off" maxlength="2000" required
                           style="flex: 1; padding: 12px; border: 2px solid var(--border); border-radius: 8px;">
                    <button type="submit" id="mesajGonderBtn" class="btn btn-primary" style="padding: 12px 20px;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Dark Mode Chat Show Düzeltmeleri */
[data-theme="dark"] .card {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sohbet-container {
    color: #e0e0e0 !important;
}

[data-theme="dark"] h1,
[data-theme="dark"] h2,
[data-theme="dark"] h3,
[data-theme="dark"] h4 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] p {
    color: #a0a0a0 !important;
}

[data-theme="dark"] strong {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .urun-ozet {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .urun-ozet:hover {
    background: #252541 !important;
}

[data-theme="dark"] .urun-detay h4 {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .urun-fiyat {
    color: #10b981 !important;
}

[data-theme="dark"] .teklif-bolumu {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .aktif-teklif {
    background: #252541 !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .teklif-tutar {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .form-input,
[data-theme="dark"] input,
[data-theme="dark"] textarea {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .form-input::placeholder,
[data-theme="dark"] input::placeholder,
[data-theme="dark"] textarea::placeholder {
    color: #6b6b8b !important;
}

[data-theme="dark"] label {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sohbet-header {
    background: #252541 !important;
    border-color: #3a3a5a !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sohbet-avatar {
    background: #2d2d44 !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .user-status {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .sohbet-mesajlar {
    background: #16162a !important;
}

[data-theme="dark"] .mesaj.gelen {
    background: #252541 !important;
    color: #e0e0e0 !important;
}

[data-theme="dark"] .mesaj.giden {
    background: #ff9900 !important;
    color: #fff !important;
}

[data-theme="dark"] .mesaj.sistem {
    background: #2d2d44 !important;
    color: #a0a0a0 !important;
}

[data-theme="dark"] .mesaj-tarih {
    color: #a0a0a0 !important;
}

[data-theme="dark"] .sohbet-form {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .sohbet-menu-dropdown {
    background: #1e1e2e !important;
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .sohbet-menu-dropdown button {
    color: #e0e0e0 !important;
}

[data-theme="dark"] .sohbet-menu-dropdown button:hover {
    background: #252541 !important;
}

[data-theme="dark"] hr {
    border-color: #3a3a5a !important;
}

[data-theme="dark"] .badge {
    color: #fff !important;
}

[data-theme="dark"] .teklif-durum.beklemede {
    background: #f59e0b !important;
}

[data-theme="dark"] .teklif-durum.kabul {
    background: #10b981 !important;
}
</style>
@endpush

@push('scripts')
<script>
const konusmaId = {{ $konusma->id }};
const userId = {{ auth()->id() }};
const mesajlarContainer = document.getElementById('mesajlarContainer');
const mesajInput = document.getElementById('mesajInput');
const mesajGonderBtn = document.getElementById('mesajGonderBtn');

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// URL'ler
const mesajGonderUrl = "{{ route('message.store', $konusma) }}";
const yeniMesajlarUrl = "{{ route('message.new', $konusma) }}";
const okunduUrl = "{{ route('message.read', $konusma) }}";

// Form submit - AJAX ile gonder
const mesajForm = document.getElementById('mesajForm');
mesajForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const mesaj = mesajInput.value.trim();
    if (!mesaj) return;

    // Butonu loading yap
    const originalBtnHtml = mesajGonderBtn.innerHTML;
    mesajGonderBtn.disabled = true;
    mesajGonderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const response = await fetch(mesajGonderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ mesaj: mesaj })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Basarili - mesaji ekle
            appendMesaj(data.mesaj, true);
            scrollToBottom();
            mesajInput.value = '';
            mesajGonderBtn.innerHTML = '<i class="fas fa-check"></i>';
        } else {
            // Hata - toast goster
            showToast(data.error || 'Mesaj gonderilemedi', 'error');
            mesajGonderBtn.innerHTML = originalBtnHtml;
        }
    } catch (err) {
        console.error('Mesaj gonderme hatasi:', err);
        showToast('Baglanti hatasi. Tekrar deneyin.', 'error');
        mesajGonderBtn.innerHTML = originalBtnHtml;
    }

    mesajGonderBtn.disabled = false;
    setTimeout(() => {
        mesajGonderBtn.innerHTML = originalBtnHtml;
        mesajInput.focus();
    }, 1000);
});

function appendMesaj(mesaj, isOwn) {
    const div = document.createElement('div');
    let classes = 'mesaj';

    if (mesaj.tip === 'sistem') {
        classes += ' sistem';
    } else if (mesaj.tip === 'teklif') {
        classes += isOwn || mesaj.gonderen_id === userId ? ' giden teklif-mesaj' : ' gelen teklif-mesaj';
    } else {
        classes += isOwn || mesaj.gonderen_id === userId ? ' giden' : ' gelen';
    }

    div.className = classes;
    div.setAttribute('data-mesaj-id', mesaj.id);

    // Menü geçici olarak devre dışı (ModSecurity sebebiyle)
    let menuHtml = '';
    // if ((isOwn || mesaj.gonderen_id === userId) && mesaj.tip === 'metin') {
    //     menuHtml = `...`;
    // }

    // Durum ikonu (sadece kendi mesajları için)
    let statusIcon = '';
    if (isOwn || mesaj.gonderen_id === userId) {
        if (mesaj.okundu) {
            statusIcon = '<i class="fas fa-check-double text-primary" title="Okundu"></i>';
        } else {
            statusIcon = '<i class="fas fa-check" data-mesaj-status="${mesaj.id}" title="Gönderildi"></i>';
        }
    }

    div.innerHTML = `
        ${menuHtml}
        <div class="mesaj-icerik" id="mesaj-icerik-${mesaj.id}">${escapeHtml(mesaj.mesaj).replace(/\n/g, '<br>')}</div>
        <div class="mesaj-tarih">
            ${mesaj.tarih || 'Şimdi'}
            ${statusIcon}
        </div>
    `;

    mesajlarContainer.appendChild(div);
}

function scrollToBottom() {
    mesajlarContainer.scrollTop = mesajlarContainer.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Scroll to bottom on load
scrollToBottom();

// Yeni mesajlari kontrol et (polling - her 5 saniyede)
let sonMesajId = {{ $konusma->mesajlar->last()?->id ?? 0 }};

setInterval(function() {
    fetch(`${yeniMesajlarUrl}?son_mesaj_id=${sonMesajId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.mesajlar.length > 0) {
            data.mesajlar.forEach(mesaj => {
                // Sistem mesajlarını her zaman göster, diğer mesajları sadece karşı taraftan gelenleri göster
                if (mesaj.tip === 'sistem' || mesaj.gonderen_id !== userId) {
                    appendMesaj(mesaj, false);
                }
                sonMesajId = Math.max(sonMesajId, mesaj.id);
            });
            scrollToBottom();
        }

        // Okundu durumunu güncelle (çift tik)
        if (data.okunan_mesajlar && data.okunan_mesajlar.length > 0) {
            data.okunan_mesajlar.forEach(mesajId => {
                const mesajDiv = document.querySelector(`[data-mesaj-id="${mesajId}"]`);
                if (mesajDiv) {
                    const statusIcon = mesajDiv.querySelector('.fa-check:not(.fa-check-double)');
                    if (statusIcon) {
                        statusIcon.classList.remove('fa-check');
                        statusIcon.classList.add('fa-check-double', 'text-primary');
                        statusIcon.title = 'Okundu';
                    }
                }
            });
        }
    })
    .catch(console.error);
}, 5000);

// Mesajlari okundu isaretle
fetch(okunduUrl, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
    }
});

// Sohbet menusunu toggle
function toggleSohbetMenu() {
    const dropdown = document.getElementById('sohbetMenuDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Mesaj menusunu toggle
function toggleMesajMenu(mesajId) {
    // Önce diğer tüm mesaj menülerini kapat
    document.querySelectorAll('.mesaj-menu-dropdown').forEach(menu => {
        if (menu.id !== `mesajMenu${mesajId}`) {
            menu.style.display = 'none';
        }
    });

    // Bu mesajın menüsünü aç/kapat
    const dropdown = document.getElementById(`mesajMenu${mesajId}`);
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Sohbeti arşivle
async function sohbetiArsivle() {
    const confirmed = await showConfirm({
        type: 'info',
        title: 'Sohbeti Arşivle',
        message: 'Bu sohbeti arşivlemek istediğinize emin misiniz? Arşivlenen sohbetler listede görünmez.',
        confirmText: 'Evet, Arşivle',
        cancelText: 'Vazgeç'
    });

    if (confirmed) {
        document.getElementById('arsivleForm').submit();
    }
}

// Sohbeti temizle
async function sohbetiTemizle() {
    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Sohbeti Temizle',
        message: 'Tüm mesajlar silinecek. Emin misiniz?',
        confirmText: 'Evet, Temizle',
        cancelText: 'Vazgeç'
    });

    if (!confirmed) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('chat.clear', $konusma) }}";
    form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    document.body.appendChild(form);
    form.submit();
}

// Mesaj sil
async function mesajSil(mesajId) {
    const confirmed = await showConfirm({
        type: 'danger',
        title: 'Mesajı Sil',
        message: 'Bu mesajı silmek istediğinize emin misiniz?',
        confirmText: 'Sil',
        cancelText: 'Vazgeç'
    });

    if (!confirmed) return;

    const url = `{{ url('/sohbet/mesaj') }}/${mesajId}/sil`;
    console.log('Sil URL:', url);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const mesajDiv = document.querySelector(`[data-mesaj-id="${mesajId}"]`);
            if (mesajDiv) {
                mesajDiv.style.opacity = '0';
                setTimeout(() => mesajDiv.remove(), 300);
            }
            showToast('Mesaj silindi', 'success');
        } else {
            showToast(data.message || 'Mesaj silinemedi', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Bir hata oluştu', 'error');
    });
}

// Mesaj duzenle
let duzenlenenMesajId = null;

function mesajDuzenle(mesajId) {
    const mesajDiv = document.getElementById(`mesaj-icerik-${mesajId}`);
    if (!mesajDiv) return;

    duzenlenenMesajId = mesajId;
    const eskiMesaj = mesajDiv.innerText;

    const textarea = document.createElement('textarea');
    textarea.value = eskiMesaj;
    textarea.className = 'mesaj-duzenle-input';
    textarea.rows = 3;

    const kaydetBtn = document.createElement('button');
    kaydetBtn.innerHTML = '<i class="fas fa-check"></i> Kaydet';
    kaydetBtn.className = 'btn btn-primary btn-sm';
    kaydetBtn.onclick = () => mesajKaydet(mesajId, textarea.value);

    const iptalBtn = document.createElement('button');
    iptalBtn.innerHTML = '<i class="fas fa-times"></i> Iptal';
    iptalBtn.className = 'btn btn-outline btn-sm';
    iptalBtn.onclick = () => {
        mesajDiv.innerHTML = escapeHtml(eskiMesaj).replace(/\n/g, '<br>');
        duzenlenenMesajId = null;
    };

    mesajDiv.innerHTML = '';
    mesajDiv.appendChild(textarea);
    const btnDiv = document.createElement('div');
    btnDiv.style.marginTop = '8px';
    btnDiv.style.display = 'flex';
    btnDiv.style.gap = '8px';
    btnDiv.appendChild(kaydetBtn);
    btnDiv.appendChild(iptalBtn);
    mesajDiv.appendChild(btnDiv);
    textarea.focus();
}

function mesajKaydet(mesajId, yeniMesaj) {
    if (!yeniMesaj.trim()) {
        showNotification('Mesaj boş olamaz', 'error');
        return;
    }

    const url = `{{ url('/sohbet/mesaj') }}/${mesajId}/duzenle`;
    console.log('Duzenle URL:', url);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            mesaj: yeniMesaj
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const mesajDiv = document.getElementById(`mesaj-icerik-${mesajId}`);
            if (mesajDiv) {
                mesajDiv.innerHTML = escapeHtml(data.mesaj.mesaj).replace(/\n/g, '<br>');
            }
            duzenlenenMesajId = null;
            showNotification('Mesaj güncellendi', 'success');
        } else {
            showNotification(data.message || 'Mesaj güncellenemedi', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('Bir hata oluştu', 'error');
    });
}

// showNotification artık global showToast'u kullanıyor
function showNotification(message, type = 'info') {
    showToast(message, type);
}

// Dropdown'u dışarı tıklayınca kapat
document.addEventListener('click', function(e) {
    // Sohbet menüsünü kapat
    const sohbetMenu = document.querySelector('.sohbet-menu');
    const sohbetDropdown = document.getElementById('sohbetMenuDropdown');
    if (sohbetMenu && sohbetDropdown && !sohbetMenu.contains(e.target)) {
        sohbetDropdown.style.display = 'none';
    }

    // Mesaj menülerini kapat
    if (!e.target.closest('.mesaj-menu')) {
        document.querySelectorAll('.mesaj-menu-dropdown').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Teklif islemleri (kabul/red/iptal) - Sayfa yenilenmeden
async function teklifIslem(islem, teklifId) {
    const routes = {
        'kabul': "/chat/offer/" + teklifId + "/accept",
        'reddet': "/chat/offer/" + teklifId + "/reject",
        'iptal': "/chat/offer/" + teklifId + "/cancel"
    };

    const url = routes[islem];
    if (!url) return;

    const confirmConfigs = {
        'kabul': {
            type: 'success',
            title: 'Teklifi Kabul Et',
            message: 'Bu teklifi kabul etmek istediğinize emin misiniz?',
            confirmText: 'Kabul Et'
        },
        'reddet': {
            type: 'danger',
            title: 'Teklifi Reddet',
            message: 'Bu teklifi reddetmek istediğinize emin misiniz?',
            confirmText: 'Reddet'
        },
        'iptal': {
            type: 'warning',
            title: 'Teklifi İptal Et',
            message: 'Teklifinizi iptal etmek istediğinize emin misiniz?',
            confirmText: 'İptal Et'
        }
    };

    const confirmed = await showConfirm(confirmConfigs[islem]);
    if (!confirmed) return;

    // Butonları devre dışı bırak
    const aktifBox = document.getElementById('aktifTeklifBox');
    const buttons = aktifBox ? aktifBox.querySelectorAll('button') : [];
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
    });

    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'İşlem başarılı', 'success');

            // UI'ı güncelle
            const teklifDurum = document.getElementById('teklifDurum');
            const teklifButonlar = document.getElementById('teklifButonlar');

            if (islem === 'kabul') {
                if (teklifDurum) {
                    teklifDurum.textContent = 'Kabul Edildi';
                    teklifDurum.className = 'teklif-durum kabul';
                }
                // Kabul edildi mesajını göster ve butonu sepete ekle yap
                if (aktifBox) {
                    aktifBox.innerHTML = `
                        <div style="text-align: center; padding: 15px;">
                            <i class="fas fa-check-circle" style="color: var(--success); font-size: 2rem;"></i>
                            <h4 style="color: var(--success); margin: 10px 0;">Teklif Kabul Edildi!</h4>
                            <p style="color: var(--text-secondary);">Sayfa yenileniyor...</p>
                        </div>
                    `;
                    // Sayfayı yenile (sepete ekle butonu için)
                    setTimeout(() => location.reload(), 1500);
                }
            } else if (islem === 'reddet' || islem === 'iptal') {
                // Teklif bölümünü yeniden yükle (yeni teklif formu gösterilsin)
                setTimeout(() => location.reload(), 1000);
            }
        } else {
            showToast(data.message || 'İşlem başarısız', 'error');
            // Butonları tekrar aktif et
            buttons.forEach(btn => btn.disabled = false);
            location.reload(); // Hata durumunda sayfayı yenile
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Bir hata oluştu', 'error');
        location.reload();
    });
}

// Teklifleri polling ile kontrol et (her 5 saniyede)
let lastTeklifId = {{ $aktifTeklif->id ?? 0 }};
setInterval(function() {
    // Sadece aktif teklif yoksa veya farklı bir teklif geldiyse kontrol et
    fetch("{{ route('chat.show', $konusma) }}", {
        headers: { 'Accept': 'text/html' }
    })
    .then(res => res.text())
    .then(html => {
        // Yeni bir teklif geldi mi kontrol et
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTeklifBox = doc.getElementById('aktifTeklifBox');
        const currentBox = document.getElementById('aktifTeklifBox');

        if (newTeklifBox && !currentBox) {
            // Yeni teklif geldi, sayfayı yenile
            showNotification('Yeni teklif geldi!', 'info');
            setTimeout(() => location.reload(), 1500);
        } else if (!newTeklifBox && currentBox) {
            // Teklif iptal edildi/kabul edildi, sayfayı yenile
            location.reload();
        }
    })
    .catch(() => {});
}, 10000); // 10 saniyede bir kontrol

// Mesaj silme butonları (partial'dan gelen)
document.querySelectorAll('.mesaj-sil-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.mesaj-sil-form');

        const confirmed = await showConfirm({
            type: 'danger',
            title: 'Mesajı Sil',
            message: 'Bu mesajı silmek istediğinize emin misiniz?',
            confirmText: 'Sil',
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
