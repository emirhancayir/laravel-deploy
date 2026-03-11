@extends('layouts.app')

@section('title', 'Kargo Takip - ZAMASON')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    @if(session('başarılı'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('başarılı') }}
        </div>
    @endif

    @if(session('hata'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('hata') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Kargo Durumu -->
    <div class="card" style="padding: 30px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2><i class="fas fa-truck"></i> Kargo Takip</h2>
            <span class="badge badge-{{ $kargo->durum_rengi }}" style="font-size: 1rem; padding: 10px 20px;">
                {{ $kargo->durum_metni }}
            </span>
        </div>

        <!-- Durum Timeline -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 30px; position: relative;">
            <div style="position: absolute; top: 20px; left: 10%; right: 10%; height: 4px; background: var(--border);"></div>

            @php
                $durumlar = ['beklemede', 'hazirlaniyor', 'kargoda', 'teslim_edildi'];
                $aktifIndex = array_search($kargo->durum, $durumlar);
                if ($aktifIndex === false) $aktifIndex = -1;
            @endphp

            @foreach(['Adres Bekleniyor', 'Hazırlanıyor', 'Kargoda', 'Teslim Edildi'] as $index => $durum)
                <div style="text-align: center; position: relative; z-index: 1;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;
                        {{ $index <= $aktifIndex ? 'background: var(--primary-gradient); color: white;' : 'background: var(--bg-dark); color: var(--text-light);' }}">
                        @if($index < $aktifIndex)
                            <i class="fas fa-check"></i>
                        @elseif($index == $aktifIndex)
                            <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <span style="font-size: 0.85rem; {{ $index <= $aktifIndex ? 'color: var(--primary); font-weight: 600;' : 'color: var(--text-light);' }}">
                        {{ $durum }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Ürün Bilgisi -->
        <div style="display: flex; gap: 20px; padding: 20px; background: var(--bg-dark); border-radius: 12px; margin-bottom: 25px;">
            @if($kargo->urun->resim)
                <img src="{{ asset('serve-image.php?p=urunler/' . $kargo->urun->resim) }}"
                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
            @endif
            <div style="flex: 1;">
                <h4 style="margin-bottom: 10px;">{{ $kargo->urun->urun_adi }}</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.95rem;">
                    <div>
                        <span style="color: var(--text-light);">Ürün Fiyatı:</span>
                        <span style="font-weight: 600;">{{ $kargo->formatli_fiyat }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-light);">Kargo Ücreti:</span>
                        <span style="font-weight: 600;">{{ $kargo->formatli_kargo_ucreti }}</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <span style="color: var(--text-light);">Toplam:</span>
                        <span style="font-weight: 700; color: var(--primary); font-size: 1.2rem;">{{ $kargo->formatli_toplam }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kargo Firması & Takip -->
        @if($kargo->kargoFirmasi)
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div style="padding: 15px; background: var(--bg-dark); border-radius: 8px;">
                <span style="color: var(--text-light); font-size: 0.9rem;">Kargo Firması</span>
                <div style="font-weight: 600; margin-top: 5px;">
                    <i class="fas fa-shipping-fast"></i> {{ $kargo->kargoFirmasi->firma_adi }}
                </div>
            </div>
            <div style="padding: 15px; background: var(--bg-dark); border-radius: 8px;">
                <span style="color: var(--text-light); font-size: 0.9rem;">Takip Numarası</span>
                <div style="font-weight: 600; margin-top: 5px;">
                    @if($kargo->takip_no)
                        {{ $kargo->takip_no }}
                        @if($kargo->takip_link)
                            <a href="{{ $kargo->takip_link }}" target="_blank" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem; margin-left: 10px;">
                                <i class="fas fa-external-link-alt"></i> Takip Et
                            </a>
                        @endif
                    @else
                        <span style="color: var(--text-light);">Henüz girilmedi</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Gönderen / Alıcı -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="padding: 15px; border: 1px solid var(--border); border-radius: 8px;">
                <h5 style="margin-bottom: 10px; color: var(--text-light);">
                    <i class="fas fa-store"></i> Gönderen (Satıcı)
                </h5>
                <div style="font-weight: 600;">{{ $kargo->gonderen->ad_soyad }}</div>
            </div>
            <div style="padding: 15px; border: 1px solid var(--border); border-radius: 8px;">
                <h5 style="margin-bottom: 10px; color: var(--text-light);">
                    <i class="fas fa-user"></i> Alıcı
                </h5>
                <div style="font-weight: 600;">{{ $kargo->alici->ad_soyad }}</div>
            </div>
        </div>
    </div>

    <!-- Alıcı için: Adres Girişi -->
    @if($kargo->alici_id === auth()->id() && $kargo->durum === 'beklemede')
    <div class="card" style="padding: 30px; margin-bottom: 20px;">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-map-marker-alt"></i> Teslimat Adresinizi Girin</h3>

        <form method="POST" action="{{ route('shipping.adres', $kargo) }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="alici_il_id">İl *</label>
                    <select id="alici_il_id" name="alici_il_id" required>
                        <option value="">İl Seçin</option>
                        @foreach($iller as $il)
                            <option value="{{ $il->id }}">{{ $il->il_adi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="alici_ilce_id">İlçe *</label>
                    <select id="alici_ilce_id" name="alici_ilce_id" required>
                        <option value="">Önce İl Seçin</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="alici_mahalle_id">Mahalle *</label>
                <select id="alici_mahalle_id" name="alici_mahalle_id" required>
                    <option value="">Önce İlçe Seçin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="alici_adres_detay">Sokak / Cadde / Bina No / Daire *</label>
                <textarea id="alici_adres_detay" name="alici_adres_detay" rows="3"
                    placeholder="Örn: Cumhuriyet Caddesi No: 45, Kat: 3, Daire: 12" required></textarea>
            </div>

            <div class="form-group">
                <label for="alici_telefon">Telefon Numarası *</label>
                <input type="tel" id="alici_telefon" name="alici_telefon"
                    placeholder="05XX XXX XX XX" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fas fa-save"></i> Adresi Kaydet ve Onayla
            </button>
        </form>
    </div>
    @endif

    <!-- Alıcı Adresi Gösterimi -->
    @if($kargo->alici_adres_detay)
    <div class="card" style="padding: 30px; margin-bottom: 20px;">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-map-marker-alt"></i> Teslimat Adresi</h3>
        <div style="padding: 20px; background: var(--bg-dark); border-radius: 8px;">
            <p style="margin-bottom: 10px;">
                <strong>{{ $kargo->aliciIl?->il_adi }} / {{ $kargo->aliciIlce?->ilce_adi }}@if($kargo->aliciMahalle) / {{ $kargo->aliciMahalle->mahalle_adi }}@endif</strong>
            </p>
            <p style="margin-bottom: 10px;">{{ $kargo->alici_adres_detay }}</p>
            <p><i class="fas fa-phone"></i> {{ $kargo->alici_telefon }}</p>
        </div>
    </div>
    @endif

    <!-- Satıcı için: Takip Numarası Girişi -->
    @if($kargo->gonderen_id === auth()->id() && $kargo->durum === 'hazirlaniyor')
    <div class="card" style="padding: 30px; margin-bottom: 20px;">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-barcode"></i> Takip Numarası Girin</h3>
        <p style="color: var(--text-light); margin-bottom: 20px;">
            Ürünü kargoya verdikten sonra takip numarasını buraya girin.
        </p>

        <form method="POST" action="{{ route('shipping.takip', $kargo) }}">
            @csrf
            <div class="form-group">
                <label for="takip_no">Takip Numarası *</label>
                <input type="text" id="takip_no" name="takip_no"
                    placeholder="Kargo firmasından aldığınız takip numarası" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fas fa-check"></i> Kargoya Verdim
            </button>
        </form>
    </div>
    @endif

    <!-- Teslim Edildi Butonu -->
    @if($kargo->durum === 'kargoda')
    <div class="card" style="padding: 30px; margin-bottom: 20px; text-align: center;">
        <h3 style="margin-bottom: 15px;"><i class="fas fa-box-open"></i> Ürünü Teslim Aldınız mı?</h3>
        <p style="color: var(--text-light); margin-bottom: 20px;">
            Ürünü teslim aldıysanız aşağıdaki butona tıklayın.
        </p>

        <form method="POST" action="{{ route('shipping.teslim', $kargo) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-success" style="padding: 15px 40px;">
                <i class="fas fa-check-circle"></i> Evet, Teslim Aldım
            </button>
        </form>
    </div>
    @endif

    <!-- Geri Dön -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('chat.show', $kargo->konusma) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Sohbete Dön
        </a>
    </div>
</div>

@push('scripts')
<script>
const aliciIlceSelect = document.getElementById('alici_ilce_id');
const aliciMahalleSelect = document.getElementById('alici_mahalle_id');

document.getElementById('alici_il_id')?.addEventListener('change', function() {
    const ilId = this.value;

    aliciIlceSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    aliciIlceSelect.disabled = true;
    aliciMahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
    aliciMahalleSelect.disabled = true;

    if (!ilId) {
        aliciIlceSelect.innerHTML = '<option value="">Önce İl Seçin</option>';
        aliciIlceSelect.disabled = false;
        return;
    }

    fetch(`/zamason/api/adres/ilceler/${ilId}`)
        .then(response => response.json())
        .then(data => {
            aliciIlceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
            data.forEach(ilce => {
                const option = document.createElement('option');
                option.value = ilce.id;
                option.textContent = ilce.ilce_adi;
                aliciIlceSelect.appendChild(option);
            });
            aliciIlceSelect.disabled = false;
        })
        .catch(error => {
            console.error('İlçe yükleme hatası:', error);
            aliciIlceSelect.innerHTML = '<option value="">Hata oluştu</option>';
            aliciIlceSelect.disabled = false;
        });
});

// İlçe değiştiğinde mahalleleri yükle
aliciIlceSelect?.addEventListener('change', function() {
    const ilceId = this.value;

    aliciMahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    aliciMahalleSelect.disabled = true;

    if (!ilceId) {
        aliciMahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
        aliciMahalleSelect.disabled = false;
        return;
    }

    fetch(`/zamason/api/adres/mahalleler/${ilceId}`)
        .then(response => response.json())
        .then(data => {
            aliciMahalleSelect.innerHTML = '<option value="">Mahalle Seçin</option>';
            data.forEach(mahalle => {
                const option = document.createElement('option');
                option.value = mahalle.id;
                option.textContent = mahalle.mahalle_adi;
                aliciMahalleSelect.appendChild(option);
            });
            aliciMahalleSelect.disabled = false;
        })
        .catch(error => {
            console.error('Mahalle yükleme hatası:', error);
            aliciMahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
            aliciMahalleSelect.disabled = false;
        });
});
</script>
@endpush
@endsection
