@extends('admin.layouts.app')

@section('title', 'Yeni Kullanıcı Ekle')
@section('page-title', 'Yeni Kullanıcı Ekle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Yeni alıcı veya satıcı hesabı oluşturun</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Geri
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="fas fa-user me-2"></i> Temel Bilgiler
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="ad" class="form-label">Ad *</label>
                        <input type="text" name="ad" id="ad" class="form-control @error('ad') is-invalid @enderror" value="{{ old('ad') }}" required>
                        @error('ad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="soyad" class="form-label">Soyad *</label>
                        <input type="text" name="soyad" id="soyad" class="form-control @error('soyad') is-invalid @enderror" value="{{ old('soyad') }}" required>
                        @error('soyad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefon" class="form-label">Telefon</label>
                        <input type="text" name="telefon" id="telefon" class="form-control @error('telefon') is-invalid @enderror" value="{{ old('telefon') }}" placeholder="05XX XXX XX XX">
                        @error('telefon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sifre" class="form-label">Şifre *</label>
                        <input type="text" name="sifre" id="sifre" class="form-control @error('sifre') is-invalid @enderror" value="{{ old('sifre', '123456') }}" required>
                        <small class="text-muted">Varsayılan: 123456</small>
                        @error('sifre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kullanici_tipi" class="form-label">Kullanıcı Tipi *</label>
                        <select name="kullanici_tipi" id="kullanici_tipi" class="form-select @error('kullanici_tipi') is-invalid @enderror" required>
                            <option value="alici" {{ old('kullanici_tipi') == 'alici' ? 'selected' : '' }}>Alıcı</option>
                            <option value="satici" {{ old('kullanici_tipi', 'satici') == 'satici' ? 'selected' : '' }}>Satıcı</option>
                            @if(auth()->user()->superAdminMi())
                                <option value="admin" {{ old('kullanici_tipi') == 'admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                        </select>
                        @error('kullanici_tipi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Satıcı Bilgileri -->
    <div class="card card-custom mb-4" id="satici-bilgileri">
        <div class="card-header">
            <i class="fas fa-store me-2"></i> Satıcı Bilgileri
            <small class="text-muted">(Satıcı seçildiğinde zorunlu)</small>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="firma_adi" class="form-label">Firma/Marka Adı *</label>
                        <input type="text" name="firma_adi" id="firma_adi" class="form-control @error('firma_adi') is-invalid @enderror" value="{{ old('firma_adi') }}">
                        @error('firma_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vergi_no" class="form-label">Vergi No</label>
                        <input type="text" name="vergi_no" id="vergi_no" class="form-control @error('vergi_no') is-invalid @enderror" value="{{ old('vergi_no') }}">
                        @error('vergi_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="adres" class="form-label">Adres *</label>
                <textarea name="adres" id="adres" class="form-control @error('adres') is-invalid @enderror" rows="2">{{ old('adres') }}</textarea>
                @error('adres')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="iban" class="form-label">IBAN *</label>
                <input type="text" name="iban" id="iban" class="form-control @error('iban') is-invalid @enderror" value="{{ old('iban') }}" placeholder="TR00 0000 0000 0000 0000 0000 00">
                @error('iban')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Kaydet
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i> İptal
        </a>
    </div>
</form>

@push('scripts')
<script>
function updateFormVisibility() {
    const tip = document.getElementById('kullanici_tipi').value;
    const saticiBilgileri = document.getElementById('satici-bilgileri');

    // Sadece satıcı seçildiğinde satıcı bilgilerini göster
    saticiBilgileri.style.display = tip === 'satici' ? 'block' : 'none';
}

document.getElementById('kullanici_tipi').addEventListener('change', updateFormVisibility);

// Sayfa yüklendiğinde kontrol et
document.addEventListener('DOMContentLoaded', updateFormVisibility);
</script>
@endpush
@endsection
