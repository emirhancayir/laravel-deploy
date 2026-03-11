@extends('admin.layouts.app')

@section('title', 'Liste Yonetimi')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Liste Yonetimi (Blacklist / Whitelist)</h1>

    <div class="row">
        <!-- IP Listesi -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">IP Listesi</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $ipSayisi }}</div>
                        </div>
                        <div class="fa-2x text-gray-300">
                            <i class="fas fa-network-wired"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.lists.ip') }}" class="btn btn-primary btn-sm mt-3 w-100">
                        <i class="fas fa-cog"></i> Yönet
                    </a>
                </div>
            </div>
        </div>

        <!-- Yasakli Kelimeler -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Yasaklı Kelimeler</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $kelimeSayisi }}</div>
                        </div>
                        <div class="fa-2x text-gray-300">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.lists.keywords') }}" class="btn btn-danger btn-sm mt-3 w-100">
                        <i class="fas fa-cog"></i> Yönet
                    </a>
                </div>
            </div>
        </div>

        <!-- E-posta Domain -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">E-posta Domainleri</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $domainSayisi }}</div>
                        </div>
                        <div class="fa-2x text-gray-300">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.lists.domains') }}" class="btn btn-warning btn-sm mt-3 w-100">
                        <i class="fas fa-cog"></i> Yönet
                    </a>
                </div>
            </div>
        </div>

        <!-- Kullanici Engelleri -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kullanıcı Engelleri</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $engelSayisi }}</div>
                        </div>
                        <div class="fa-2x text-gray-300">
                            <i class="fas fa-user-slash"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.lists.blocks') }}" class="btn btn-info btn-sm mt-3 w-100">
                        <i class="fas fa-cog"></i> Yönet
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aciklama -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Liste Türleri Hakkında</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-network-wired text-primary"></i> IP Listesi</h6>
                    <p class="text-muted small">Belirli IP adreslerini engelleyebilir (blacklist) veya özel izin verebilirsiniz (whitelist). Whitelist'teki IP'ler blacklist kontrolünden muaf tutulur.</p>

                    <h6><i class="fas fa-ban text-danger"></i> Yasaklı Kelimeler</h6>
                    <p class="text-muted small">Ürün adlarında, açıklamalarında, mesajlarda veya kullanıcı adlarında kullanılmasını istemediğiniz kelimeleri engelleyebilir veya otomatik sansürleme yapabilirsiniz.</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-envelope text-warning"></i> E-posta Domainleri</h6>
                    <p class="text-muted small">Gecici e-posta servislerini veya istenmeyen domainleri engelleyebilirsiniz. Whitelist tanımlanırsa sadece o domainlerden kayıt kabul edilir.</p>

                    <h6><i class="fas fa-user-slash text-info"></i> Kullanıcı Engelleri</h6>
                    <p class="text-muted small">Kullanıcılar arası engelleme kayıtlarını gorüntüleyebilir ve gerektiğinde müdahale edebilirsiniz.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
