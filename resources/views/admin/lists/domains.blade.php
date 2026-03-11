@extends('admin.layouts.app')

@section('title', 'E-posta Domain Yonetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">E-posta Domain Yonetimi</h1>
        <a href="{{ route('admin.lists.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="row">
        <!-- Ekleme Formu -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Yeni Domain Ekle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lists.domain.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Domain *</label>
                            <input type="text" name="domain" class="form-control" placeholder="ornek.com" required>
                            <small class="text-muted">@ isareti olmadan sadece domain yazin</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tip *</label>
                            <select name="tip" class="form-select" required>
                                <option value="blacklist">Blacklist (Engelle)</option>
                                <option value="whitelist">Whitelist (Sadece Buna Izin Ver)</option>
                            </select>
                            <small class="text-muted">Whitelist tanimlanirsa sadece o domainlerden kayıt kabul edilir</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sebep</label>
                            <input type="text" name="sebep" class="form-control" placeholder="Engelleme sebebi...">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-plus"></i> Ekle
                        </button>
                    </form>

                    <hr>

                    <form action="{{ route('admin.lists.domain.add-temporary') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-magic"></i> Gecici E-posta Domainlerini Ekle
                        </button>
                        <small class="text-muted d-block mt-2">Bilinen 20+ gecici e-posta servisini otomatik ekler</small>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Domain Listesi</h5>
                        <form class="d-flex align-items-center gap-2" method="GET">
                            <select name="tip" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">Tum Tipler</option>
                                <option value="blacklist" {{ request('tip') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                <option value="whitelist" {{ request('tip') == 'whitelist' ? 'selected' : '' }}>Whitelist</option>
                            </select>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" name="ara" class="form-control" placeholder="Domain ara..." value="{{ request('ara') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Domain</th>
                                    <th>Tip</th>
                                    <th>Sebep</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($domainler as $domain)
                                    <tr class="{{ !$domain->aktif ? 'table-secondary' : '' }}">
                                        <td><code>{{ $domain->domain }}</code></td>
                                        <td>
                                            @if($domain->tip == 'blacklist')
                                                <span class="badge bg-danger">Blacklist</span>
                                            @else
                                                <span class="badge bg-success">Whitelist</span>
                                            @endif
                                        </td>
                                        <td>{{ $domain->sebep ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('admin.lists.domain.toggle', $domain) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $domain->aktif ? 'success' : 'secondary' }}">
                                                    {{ $domain->aktif ? 'Aktif' : 'Pasif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $domain->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            <form action="{{ route('admin.lists.domain.delete', $domain) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Henüz domain kaydı yok
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($domainler->hasPages())
                    <div class="card-footer">
                        {{ $domainler->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
