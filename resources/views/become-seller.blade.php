@extends('layouts.app')

@section('title', 'Satıcı Ol - ' . config('app.name'))

@push('styles')
<style>
/* Hero Section */
.seller-hero {
    background: #ff9900;
    padding: 60px 20px;
    border-radius: 20px;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.seller-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 15s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.seller-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: white;
}

.seller-hero h1 {
    font-size: 2.8rem;
    margin-bottom: 15px;
    font-weight: 700;
}

.seller-hero h1 i {
    background: rgba(255,255,255,0.2);
    padding: 15px;
    border-radius: 50%;
    margin-right: 15px;
}

.seller-hero p {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

.seller-hero-stats {
    display: flex;
    justify-content: center;
    gap: 50px;
    margin-top: 30px;
}

.hero-stat {
    text-align: center;
}

.hero-stat-value {
    font-size: 2rem;
    font-weight: 700;
}

.hero-stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Benefits Section */
.benefits-section {
    margin-bottom: 50px;
}

.benefits-section h2 {
    text-align: center;
    margin-bottom: 10px;
    font-size: 1.8rem;
}

.benefits-subtitle {
    text-align: center;
    color: var(--text-light);
    margin-bottom: 30px;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.benefit-card {
    background: var(--card-bg, white);
    padding: 30px;
    border-radius: 16px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}

.benefit-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #e68a00);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
}

.benefit-card:hover::before {
    transform: scaleX(1);
}

.benefit-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #ff990020 0%, #e68a0020 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.benefit-icon i {
    font-size: 1.8rem;
    background: #ff9900;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.benefit-card h3 {
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.benefit-card p {
    color: var(--text-light);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Steps Section */
.steps-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    padding: 40px;
    border-radius: 20px;
    margin-bottom: 50px;
}

[data-theme="dark"] .steps-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}

.steps-section h2 {
    text-align: center;
    margin-bottom: 40px;
}

.steps-grid {
    display: flex;
    justify-content: space-between;
    position: relative;
}

.steps-grid::before {
    content: '';
    position: absolute;
    top: 40px;
    left: 15%;
    right: 15%;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e68a00);
    z-index: 0;
}

.step-item {
    text-align: center;
    flex: 1;
    position: relative;
    z-index: 1;
}

.step-number {
    width: 80px;
    height: 80px;
    background: #ff9900;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.step-item h4 {
    margin-bottom: 5px;
}

.step-item p {
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Form Container */
.seller-form-container {
    background: var(--card-bg, white);
    padding: 40px;
    border-radius: 20px;
    border: 1px solid var(--border);
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h2 {
    font-size: 1.8rem;
    margin-bottom: 10px;
}

.form-header p {
    color: var(--text-light);
}

.form-section {
    margin-bottom: 35px;
    padding-bottom: 35px;
    border-bottom: 1px solid var(--border);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.section-icon {
    width: 45px;
    height: 45px;
    background: #ff9900;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.section-header h3 {
    font-size: 1.2rem;
    margin: 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg);
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.form-group small {
    display: block;
    margin-top: 6px;
    color: var(--text-light);
    font-size: 0.85rem;
}

/* Commission Table */
.commission-card {
    background: linear-gradient(135deg, #ff990008 0%, #e68a0008 100%);
    border-radius: 16px;
    padding: 25px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.commission-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.commission-table th {
    background: #ff9900;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.commission-table th:first-child {
    border-radius: 12px 0 0 0;
}

.commission-table th:last-child {
    border-radius: 0 12px 0 0;
    text-align: center;
}

.commission-table td {
    padding: 14px 15px;
    border-bottom: 1px solid var(--border);
}

.commission-table td:last-child {
    text-align: center;
}

.commission-table tr:last-child td:first-child {
    border-radius: 0 0 0 12px;
}

.commission-table tr:last-child td:last-child {
    border-radius: 0 0 12px 0;
}

.commission-table tbody tr {
    transition: background 0.2s ease;
}

.commission-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.05);
}

.commission-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.commission-note {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 20px;
    padding: 15px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    color: var(--text);
}

.commission-note i {
    color: #ff9900;
    font-size: 1.2rem;
    margin-top: 2px;
}

/* Agreement Box */
.agreement-box {
    background: var(--bg);
    padding: 25px;
    border-radius: 16px;
}

.agreement-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 12px;
    transition: background 0.2s ease;
}

.agreement-item:hover {
    background: rgba(102, 126, 234, 0.05);
}

.agreement-item:last-child {
    margin-bottom: 0;
}

.agreement-item input[type="checkbox"] {
    width: 22px;
    height: 22px;
    margin-top: 2px;
    cursor: pointer;
    accent-color: #ff9900;
}

.agreement-item label {
    cursor: pointer;
    line-height: 1.6;
}

.agreement-item a {
    color: #ff9900;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: border-color 0.2s ease;
}

.agreement-item a:hover {
    border-bottom-color: #ff9900;
}

/* Submit Button */
.form-actions {
    text-align: center;
    margin-top: 40px;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 50px;
    background: #ff9900;
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.15rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

.btn-submit:active {
    transform: translateY(0);
}

.form-note {
    margin-top: 15px;
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(5px);
    z-index: 1000;
    overflow-y: auto;
    padding: 20px;
}

.modal-content {
    background: var(--card-bg, white);
    max-width: 700px;
    margin: 30px auto;
    padding: 35px;
    border-radius: 20px;
    position: relative;
    animation: modalIn 0.3s ease;
}

@keyframes modalIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 25px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg);
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-light);
    transition: all 0.2s ease;
}

.close-modal:hover {
    background: #ff9900;
    color: white;
}

.modal-content h2 {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--border);
    padding-right: 50px;
}

.modal-body {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 10px;
}

.modal-body h3 {
    color: #ff9900;
    margin: 25px 0 12px;
    font-size: 1.1rem;
}

.modal-body ul {
    margin-left: 20px;
    margin-bottom: 15px;
}

.modal-body li {
    margin-bottom: 10px;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .seller-hero {
        padding: 40px 20px;
    }

    .seller-hero h1 {
        font-size: 2rem;
    }

    .seller-hero-stats {
        flex-wrap: wrap;
        gap: 25px;
    }

    .steps-grid {
        flex-direction: column;
        gap: 30px;
    }

    .steps-grid::before {
        display: none;
    }

    .seller-form-container {
        padding: 25px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .btn-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="seller-page" style="max-width: 1000px; margin: 0 auto;">

    <!-- Hero Section -->
    <div class="seller-hero">
        <div class="seller-hero-content">
            <h1><i class="fas fa-store"></i> Satici Ol</h1>
            <p>{{ config('app.name') }}'da satis yaparak binlerce musteriye ulasin ve kazanmaya baslayin!</p>
            <div class="seller-hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">1000+</div>
                    <div class="hero-stat-label">Aktif Satici</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">50K+</div>
                    <div class="hero-stat-label">Musteri</div>
                </div>
                <div class="hero-stat">
                    @php
                        $minKomisyon = $kategoriler->min('komisyon_orani') ?? 5;
                        $maxKomisyon = $kategoriler->max('komisyon_orani') ?? 15;
                    @endphp
                    <div class="hero-stat-value">%{{ number_format($minKomisyon, 0) }}-{{ number_format($maxKomisyon, 0) }}</div>
                    <div class="hero-stat-label">Komisyon</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="benefits-section">
        <h2>Neden {{ config('app.name') }}?</h2>
        <p class="benefits-subtitle">Satis yapmak hic bu kadar kolay olmamisti</p>
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Genis Musteri Kitlesi</h3>
                <p>Binlerce aktif aliciya aninda ulasin ve satislarinizi katlayarak artirin.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Guvenli Odeme</h3>
                <p>iyzico altyapisi ile guvenli odeme. Paraniz aninda hesabinizda.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Kolay Yonetim</h3>
                <p>Urun ekleme, stok takibi ve siparis yonetimi tek panelde.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>7/24 Destek</h3>
                <p>Sorulariniz icin her zaman yaninizda. Hizli ve cozum odakli destek.</p>
            </div>
        </div>
    </div>

    <!-- Steps Section -->
    <div class="steps-section">
        <h2>Nasil Satici Olunur?</h2>
        <div class="steps-grid">
            <div class="step-item">
                <div class="step-number">1</div>
                <h4>Basvuru Yap</h4>
                <p>Formu doldur</p>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <h4>Onay Al</h4>
                <p>Hizli degerlendirme</p>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <h4>Urun Ekle</h4>
                <p>Magazani kur</p>
            </div>
            <div class="step-item">
                <div class="step-number">4</div>
                <h4>Satis Yap</h4>
                <p>Kazanmaya basla</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 30px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div class="seller-form-container">
        <div class="form-header">
            <h2><i class="fas fa-file-signature"></i> Satici Basvuru Formu</h2>
            <p>Bilgilerinizi doldurun, hemen satisa baslayin</p>
        </div>

        <form method="POST" action="{{ route('seller.become.store') }}" class="seller-form">
            @csrf

            <!-- Firma Bilgileri -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Firma Bilgileri</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firma_adi">Firma / Marka Adi *</label>
                        <input type="text" id="firma_adi" name="firma_adi" value="{{ old('firma_adi') }}"
                               placeholder="Ornek: Teknoloji Dukkani" required>
                        <small>Urunlerinizde gorunecek marka/firma adi</small>
                    </div>
                    <div class="form-group">
                        <label for="vergi_no">Vergi Numarasi (Opsiyonel)</label>
                        <input type="text" id="vergi_no" name="vergi_no" value="{{ old('vergi_no') }}"
                               placeholder="Sahis sirketi icin TC no girilebilir">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefon">Telefon Numarasi *</label>
                        <input type="tel" id="telefon" name="telefon" value="{{ old('telefon', auth()->user()->telefon) }}"
                               placeholder="05XX XXX XX XX" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="adres">Is Adresi *</label>
                    <textarea id="adres" name="adres" rows="3"
                              placeholder="Tam adresinizi yazin">{{ old('adres', auth()->user()->adres) }}</textarea>
                </div>
            </div>

            <!-- Odeme Bilgileri -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Odeme Bilgileri</h3>
                </div>

                <div class="form-group">
                    <label for="iban">IBAN Numarasi *</label>
                    <input type="text" id="iban" name="iban" value="{{ old('iban') }}"
                           placeholder="TR00 0000 0000 0000 0000 0000 00" required>
                    <small><i class="fas fa-info-circle"></i> Satislarinizdan elde ettiginiz gelir bu hesaba aktarilacaktir</small>
                </div>
            </div>

            <!-- Komisyon Oranlari -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h3>Komisyon Oranlari</h3>
                </div>

                <div class="commission-card">
                    <table class="commission-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> Kategori</th>
                                <th>Komisyon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Kategori ikonları
                                $kategoriIkonlari = [
                                    'elektronik' => 'fa-laptop',
                                    'giyim' => 'fa-tshirt',
                                    'ev' => 'fa-home',
                                    'vasita' => 'fa-car',
                                    'spor' => 'fa-dumbbell',
                                    'kitap' => 'fa-book',
                                    'kozmetik' => 'fa-spa',
                                    'oyuncak' => 'fa-gamepad',
                                    'gida' => 'fa-utensils',
                                    'bahce' => 'fa-seedling',
                                    'anne' => 'fa-baby',
                                    'pet' => 'fa-paw',
                                    'default' => 'fa-tag',
                                ];
                                $renkler = ['text-primary', 'text-info', 'text-warning', 'text-danger', 'text-success', 'text-secondary'];
                            @endphp
                            @forelse($kategoriler as $index => $kategori)
                                @php
                                    $slug = \Str::slug($kategori->kategori_adi);
                                    $ikon = 'fa-tag';
                                    foreach($kategoriIkonlari as $key => $value) {
                                        if(str_contains($slug, $key)) {
                                            $ikon = $value;
                                            break;
                                        }
                                    }
                                    $renk = $renkler[$index % count($renkler)];
                                @endphp
                                <tr>
                                    <td><i class="fas {{ $ikon }} {{ $renk }}"></i> {{ $kategori->kategori_adi }}</td>
                                    <td><span class="commission-badge">%{{ number_format($kategori->komisyon_orani, 1) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Kategori bulunamadi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="commission-note">
                        <i class="fas fa-lightbulb"></i>
                        <div>Komisyon oranlari satis tutari uzerinden kesilir. <strong>Kargo ucretinden komisyon alinmaz.</strong></div>
                    </div>
                </div>
            </div>

            <!-- Sozlesmeler -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Sozlesmeler ve Onaylar</h3>
                </div>

                <div class="agreement-box">
                    <div class="agreement-item">
                        <input type="checkbox" id="sozlesme" name="sozlesme" {{ old('sozlesme') ? 'checked' : '' }} required>
                        <label for="sozlesme">
                            <a href="#" onclick="showModal('sozlesme-modal'); return false;">Satici Sozlesmesi</a>'ni okudum ve kabul ediyorum. *
                        </label>
                    </div>

                    <div class="agreement-item">
                        <input type="checkbox" id="kvkk" name="kvkk" {{ old('kvkk') ? 'checked' : '' }} required>
                        <label for="kvkk">
                            <a href="#" onclick="showModal('kvkk-modal'); return false;">KVKK Aydinlatma Metni</a>'ni okudum ve kabul ediyorum. *
                        </label>
                    </div>

                    <div class="agreement-item">
                        <input type="checkbox" id="komisyon" name="komisyon" {{ old('komisyon') ? 'checked' : '' }} required>
                        <label for="komisyon">
                            Yukarida belirtilen komisyon oranlarini kabul ediyorum. *
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-rocket"></i> Satici Ol
                </button>
                <p class="form-note">* isaretli alanlar zorunludur</p>
            </div>
        </form>
    </div>
</div>

<!-- Satici Sozlesmesi Modal -->
<div id="sozlesme-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="hideModal('sozlesme-modal')">&times;</span>
        <h2><i class="fas fa-file-contract"></i> Satici Sozlesmesi</h2>
        <div class="modal-body">
            <h3>1. Taraflar</h3>
            <p>Bu sozlesme {{ config('app.name') }} ("Platform") ile satici olarak kayit olan kullanici ("Satici") arasinda akdedilmistir.</p>

            <h3>2. Saticinin Yukumlulukleri</h3>
            <ul>
                <li>Satici, platformda satisa sundugu urunlerin yasal mevzuata uygun oldugunu taahhut eder.</li>
                <li>Urun aciklamalari ve gorselleri gercegi yansitmalidir.</li>
                <li>Siparisler en gec 3 is gunu icinde kargoya verilmelidir.</li>
                <li>Iade ve degisim talepleri yasal sureler icinde karsilanmalidir.</li>
                <li>Musteri sikayetlerine 24 saat icinde yanit verilmelidir.</li>
            </ul>

            <h3>3. Platformun Yukumlulukleri</h3>
            <ul>
                <li>Platform, saticiya teknik altyapi ve musteri erisimi saglar.</li>
                <li>Odemeler, siparisin tamamlanmasindan sonra 7 is gunu icinde aktarilir.</li>
                <li>Platform, satici bilgilerini gizli tutar.</li>
            </ul>

            <h3>4. Komisyon ve Odemeler</h3>
            <p>Platform, her satistan kategori bazli komisyon keser. Komisyon oranlari basvuru formunda belirtilmistir.</p>

            <h3>5. Yasakli Urunler</h3>
            <p>Yasadisi urunler, sahte/taklit urunler, tehlikeli maddeler ve yetiskin icerikli urunlerin satisi yasaktir.</p>

            <h3>6. Sozlesmenin Feshi</h3>
            <p>Taraflardan her biri 30 gun onceden bildirmek kaydiyla sozlesmeyi feshedebilir. Kural ihlali durumunda platform derhal fesih hakkina sahiptir.</p>
        </div>
    </div>
</div>

<!-- KVKK Modal -->
<div id="kvkk-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="hideModal('kvkk-modal')">&times;</span>
        <h2><i class="fas fa-shield-alt"></i> KVKK Aydinlatma Metni</h2>
        <div class="modal-body">
            <p><strong>{{ config('app.name') }} E-Ticaret Platformu Satici Kisisel Verilerin Korunmasi ve Islenmesi Hakkinda Aydinlatma Metni</strong></p>

            <h3>1. Veri Sorumlusu</h3>
            <p>{{ config('app.name') }} E-Ticaret Platformu, KVKK kapsaminda "Veri Sorumlusu" sifatiyla hareket etmektedir.</p>

            <h3>2. Toplanan Kisisel Veriler</h3>
            <ul>
                <li><strong>Kimlik Bilgileri:</strong> Ad, soyad, T.C. kimlik numarasi</li>
                <li><strong>Iletisim Bilgileri:</strong> Telefon numarasi, e-posta adresi, is adresi</li>
                <li><strong>Finansal Bilgiler:</strong> IBAN numarasi, vergi numarasi</li>
                <li><strong>Ticari Bilgiler:</strong> Firma adi, vergi dairesi</li>
            </ul>

            <h3>3. Kisisel Verilerin Islenme Amaclari</h3>
            <ul>
                <li>Satici hesabinin olusturulmasi ve yonetimi</li>
                <li>Satis islemlerinin takibi ve yonetimi</li>
                <li>Komisyon hesaplamalari ve odeme islemleri</li>
                <li>Yasal duzenlernelere uyum saglanmasi</li>
            </ul>

            <h3>4. KVKK Kapsamindaki Haklariniz</h3>
            <ul>
                <li>Kisisel verilerinizin islenip islenmedigini ogrenme</li>
                <li>Kisisel verileriniz islenmisse buna iliskin bilgi talep etme</li>
                <li>Kisisel verilerin eksik veya yanlis islenmis olmasi halinde bunlarin duzeltilmesini isteme</li>
                <li>Kisisel verilerin silinmesini veya yok edilmesini isteme</li>
            </ul>

            <h3>5. Basvuru Yontemi</h3>
            <p>Haklarinizi kullanmak icin <strong>{{ config('mail.from.address') }}</strong> adresine e-posta gonderebilirsiniz.</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showModal(id) {
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Click outside to close
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// IBAN formatlama
document.getElementById('iban').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').toUpperCase();
    if (value.length > 0 && !value.startsWith('TR')) {
        value = 'TR' + value.replace('TR', '');
    }
    // Her 4 karakterde bir bosluk ekle
    let formatted = '';
    for (let i = 0; i < value.length && i < 26; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += value[i];
    }
    e.target.value = formatted;
});

// Telefon formatlama
document.getElementById('telefon').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.startsWith('90')) value = value.substring(2);
        if (value.startsWith('0')) value = value.substring(1);

        let formatted = '';
        if (value.length > 0) formatted = '0' + value.substring(0, 3);
        if (value.length > 3) formatted += ' ' + value.substring(3, 6);
        if (value.length > 6) formatted += ' ' + value.substring(6, 8);
        if (value.length > 8) formatted += ' ' + value.substring(8, 10);

        e.target.value = formatted;
    }
});
</script>
@endpush
