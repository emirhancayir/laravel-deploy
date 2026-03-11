@extends('layouts.app')

@section('title', 'Ödeme Başarılı - ZAMASON')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="text-success mb-3">Ödemeniz Başarılı!</h2>
                    <p class="text-muted mb-4">
                        Siparişiniz başarıyla oluşturuldu. Satıcı en kısa sürede ürünü kargoya verecektir.
                    </p>

                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row text-start">
                            <div class="col-6 mb-3">
                                <small class="text-muted">Sipariş No</small>
                                <div><strong>#{{ $odeme->id }}</strong></div>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">Ödeme Tutarı</small>
                                <div><strong class="text-success">{{ $odeme->formatli_toplam }}</strong></div>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">Ürün</small>
                                <div><strong>{{ $odeme->urun->urun_adi }}</strong></div>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">Satıcı</small>
                                <div><strong>{{ $odeme->satici->ad_soyad }}</strong></div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Teslimat Adresi</small>
                                <div><strong>{{ $odeme->teslimat_adresi }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('chat.show', $odeme->konusma) }}" class="btn btn-primary">
                            <i class="fas fa-comments"></i> Satıcı ile İletişime Geç
                        </a>
                        <a href="{{ route('payment.list') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Siparişlerim
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-link">
                            <i class="fas fa-home"></i> Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
