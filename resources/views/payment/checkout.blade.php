@extends('layouts.app')

@section('title', 'Ödeme - ZAMASON')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-credit-card"></i> Ödeme</h2>
        </div>
    </div>

    <form action="{{ route('payment.initiate') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Teslimat Adresi -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="fas fa-map-marker-alt"></i> Teslimat Adresi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="teslimat_il_id" class="form-label">İl *</label>
                                <select name="teslimat_il_id" id="teslimat_il_id" class="form-select @error('teslimat_il_id') is-invalid @enderror" required>
                                    <option value="">İl Seçin</option>
                                    @foreach($iller as $il)
                                        <option value="{{ $il->id }}" {{ old('teslimat_il_id') == $il->id ? 'selected' : '' }}>
                                            {{ $il->il_adi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teslimat_il_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="teslimat_ilce_id" class="form-label">İlçe *</label>
                                <select name="teslimat_ilce_id" id="teslimat_ilce_id" class="form-select @error('teslimat_ilce_id') is-invalid @enderror" required>
                                    <option value="">Önce il seçin</option>
                                </select>
                                @error('teslimat_ilce_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="teslimat_mahalle_id" class="form-label">Mahalle (opsiyonel)</label>
                                <select name="teslimat_mahalle_id" id="teslimat_mahalle_id" class="form-select @error('teslimat_mahalle_id') is-invalid @enderror">
                                    <option value="">Önce ilçe seçin</option>
                                </select>
                                @error('teslimat_mahalle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="teslimat_adres_detay" class="form-label">Adres Detayı *</label>
                            <textarea name="teslimat_adres_detay" id="teslimat_adres_detay" rows="3"
                                      class="form-control @error('teslimat_adres_detay') is-invalid @enderror"
                                      placeholder="Sokak, cadde, bina no, daire no..."
                                      required>{{ old('teslimat_adres_detay', auth()->user()->adres) }}</textarea>
                            @error('teslimat_adres_detay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="teslimat_telefon" class="form-label">Telefon *</label>
                            <input type="tel" name="teslimat_telefon" id="teslimat_telefon"
                                   class="form-control @error('teslimat_telefon') is-invalid @enderror"
                                   value="{{ old('teslimat_telefon', auth()->user()->telefon) }}"
                                   placeholder="05XX XXX XX XX" required>
                            @error('teslimat_telefon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sipariş Ürünleri -->
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="fas fa-box"></i> Sipariş Ürünleri</strong>
                    </div>
                    <div class="card-body p-0">
                        @foreach($sepetItems as $item)
                            <div class="p-3 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($item->urun->resim)
                                            <img src="{{ asset('serve-image.php?p=urunler/' . $item->urun->resim) }}"
                                                 class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-7">
                                        <h6 class="mb-1">{{ $item->urun->urun_adi }}</h6>
                                        <small class="text-muted">Satıcı: {{ $item->urun->satici->ad_soyad }}</small>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <strong>{{ $item->formatli_tutar }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sipariş Özeti -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <strong>Sipariş Özeti</strong>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ürün Toplamı:</span>
                            <span>{{ number_format($toplamTutar, 2, ',', '.') }} TL</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Kargo Ücreti:</span>
                            <span>{{ number_format($kargoTutari, 2, ',', '.') }} TL</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Toplam:</strong>
                            <strong class="text-success fs-5">{{ number_format($genelToplam, 2, ',', '.') }} TL</strong>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-lock"></i> Güvenli Ödemeye Geç
                        </button>

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> SSL ile korunmaktadır
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// İl/İlçe/Mahalle dropdown
document.getElementById('teslimat_il_id').addEventListener('change', function() {
    const ilId = this.value;
    const ilceSelect = document.getElementById('teslimat_ilce_id');
    const mahalleSelect = document.getElementById('teslimat_mahalle_id');

    ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    mahalleSelect.innerHTML = '<option value="">Önce ilçe seçin</option>';

    if (ilId) {
        fetch(`/api/address/districts/${ilId}`)
            .then(response => response.json())
            .then(data => {
                ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
                data.forEach(ilce => {
                    ilceSelect.innerHTML += `<option value="${ilce.id}">${ilce.ilce_adi}</option>`;
                });
            })
            .catch(error => {
                console.error('İlçe yükleme hatası:', error);
                ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    } else {
        ilceSelect.innerHTML = '<option value="">Önce il seçin</option>';
    }
});

document.getElementById('teslimat_ilce_id').addEventListener('change', function() {
    const ilceId = this.value;
    const mahalleSelect = document.getElementById('teslimat_mahalle_id');

    mahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';

    if (ilceId) {
        fetch(`/api/address/neighborhoods/${ilceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    mahalleSelect.innerHTML = '<option value="">Mahalle bulunamadı (boş bırakabilirsiniz)</option>';
                } else {
                    mahalleSelect.innerHTML = '<option value="">Mahalle Seçin (opsiyonel)</option>';
                    data.forEach(mahalle => {
                        mahalleSelect.innerHTML += `<option value="${mahalle.id}">${mahalle.mahalle_adi}</option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Mahalle yükleme hatası:', error);
                mahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    } else {
        mahalleSelect.innerHTML = '<option value="">Önce ilçe seçin</option>';
    }
});
</script>
@endpush
@endsection
