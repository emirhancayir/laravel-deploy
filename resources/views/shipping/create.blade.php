@extends('layouts.app')

@section('title', 'Kargo Oluştur - ZAMASON')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card" style="padding: 30px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2><i class="fas fa-truck"></i> Kargo Oluştur</h2>
            <p style="color: var(--text-light);">{{ $konusma->urun->urun_adi }}</p>
        </div>

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

        <!-- Teklif Bilgisi -->
        <div style="background: linear-gradient(135deg, #ff990015 0%, #e68a0015 100%); padding: 20px; border-radius: 12px; margin-bottom: 25px;">
            <h4 style="margin-bottom: 15px;"><i class="fas fa-handshake"></i> Kabul Edilen Teklif</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <span style="color: var(--text-light); font-size: 0.9rem;">Tutar</span>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">
                        {{ number_format($teklif->tutar, 2, ',', '.') }} ₺
                    </div>
                </div>
                <div>
                    <span style="color: var(--text-light); font-size: 0.9rem;">Alıcı</span>
                    <div style="font-weight: 600;">{{ $konusma->alici->ad_soyad }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('shipping.store', $konusma) }}">
            @csrf

            <div class="form-group">
                <label for="kargo_firmasi_id"><i class="fas fa-shipping-fast"></i> Kargo Firması *</label>
                <select id="kargo_firmasi_id" name="kargo_firmasi_id" required>
                    <option value="">Seçin</option>
                    @foreach($kargoFirmalari as $firma)
                        <option value="{{ $firma->id }}" {{ old('kargo_firmasi_id') == $firma->id ? 'selected' : '' }}>
                            {{ $firma->firma_adi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="kargo_ucreti"><i class="fas fa-money-bill"></i> Kargo Ücreti (₺) *</label>
                <input type="number" id="kargo_ucreti" name="kargo_ucreti" step="0.01" min="0"
                       value="{{ old('kargo_ucreti', 40) }}" required>
                <small style="color: var(--text-light);">Alıcıdan alınacak kargo ücreti</small>
            </div>

            <div class="form-group">
                <label for="notlar"><i class="fas fa-sticky-note"></i> Notlar (Opsiyonel)</label>
                <textarea id="notlar" name="notlar" rows="3" placeholder="Alıcıya iletmek istediğiniz notlar...">{{ old('notlar') }}</textarea>
            </div>

            <!-- Özet -->
            <div style="background: var(--bg-dark); padding: 20px; border-radius: 12px; margin: 25px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Ürün Fiyatı:</span>
                    <span>{{ number_format($teklif->tutar, 2, ',', '.') }} ₺</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Kargo Ücreti:</span>
                    <span id="kargoUcretiGoster">40,00 ₺</span>
                </div>
                <hr style="border-color: var(--border); margin: 10px 0;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem;">
                    <span>Toplam:</span>
                    <span id="toplamGoster" style="color: var(--primary);">{{ number_format($teklif->tutar + 40, 2, ',', '.') }} ₺</span>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-check"></i> Kargo Oluştur
                </button>
                <a href="{{ route('chat.show', $konusma) }}" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const urunFiyati = {{ $teklif->tutar }};

document.getElementById('kargo_ucreti').addEventListener('input', function() {
    const kargoUcreti = parseFloat(this.value) || 0;
    const toplam = urunFiyati + kargoUcreti;

    document.getElementById('kargoUcretiGoster').textContent =
        kargoUcreti.toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ₺';
    document.getElementById('toplamGoster').textContent =
        toplam.toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ₺';
});
</script>
@endpush
@endsection
