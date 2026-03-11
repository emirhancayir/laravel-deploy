@extends('layouts.app')

@section('title', 'Ödeme - ZAMASON')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between">
                            <span><strong>Ödeme Tutarı:</strong></span>
                            <span class="fs-5">{{ $odeme->formatli_toplam }}</span>
                        </div>
                    </div>

                    <!-- iyzico Checkout Form -->
                    <div id="iyzipay-checkout-form" class="responsive">
                        {!! $checkoutFormContent !!}
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body text-center">
                    <p class="mb-0">
                        <i class="fas fa-shield-alt text-success"></i>
                        <small class="text-muted">Ödemeler iyzico güvenli ödeme altyapısı ile işlenmektedir.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
