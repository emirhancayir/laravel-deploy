@extends('admin.layouts.app')

@section('title', 'Site Ayarları')
@section('page-title', 'Site Ayarları')

@section('content')
<!-- Cache Temizle Butonu -->
<div class="mb-4">
    <form action="{{ route('admin.settings.clearCache') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-sync-alt me-2"></i>Tüm Cache'leri Temizle
        </button>
    </form>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-cog me-2"></i>Genel Ayarlar
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    @foreach($settings as $setting)
                        <div class="mb-3">
                            <label class="form-label">{{ $setting->label ?? $setting->key }}</label>
                            @if($setting->type == 'textarea')
                                <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                            @elseif($setting->type == 'boolean')
                                <select name="settings[{{ $setting->key }}]" class="form-select">
                                    <option value="1" {{ $setting->value == '1' || $setting->value === true ? 'selected' : '' }}>Evet</option>
                                    <option value="0" {{ $setting->value == '0' || $setting->value === false || $setting->value === null || $setting->value === '' ? 'selected' : '' }}>Hayır</option>
                                </select>
                            @elseif($setting->type == 'number')
                                <input type="number" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                            @endif
                            @if($setting->description)
                                <small class="text-muted">{{ $setting->description }}</small>
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-plus me-2"></i>Yeni Ayar Ekle
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Anahtar</label>
                        <input type="text" name="key" class="form-control" required placeholder="site_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Değer</label>
                        <input type="text" name="value" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Etiket</label>
                        <input type="text" name="label" class="form-control" placeholder="Site Adı">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip</label>
                        <select name="type" class="form-select">
                            <option value="text">Metin</option>
                            <option value="textarea">Uzun Metin</option>
                            <option value="number">Sayı</option>
                            <option value="boolean">Evet/Hayır</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <input type="text" name="description" class="form-control" placeholder="Bu ayar ne işe yarar">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus me-2"></i>Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
