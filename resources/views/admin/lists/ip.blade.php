@extends('admin.layouts.app')

@section('title', 'IP Listesi Yonetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">IP Listesi Yonetimi</h1>
        <a href="{{ route('admin.lists.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="row">
        <!-- Ekleme Formu -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Yeni IP Ekle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lists.ip.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">IP Adresi *</label>
                            <input type="text" name="ip_adresi" class="form-control" placeholder="192.168.1.100 veya 192.168.1.*" required>
                            <small class="text-muted">Wildcard icin * kullanabilirsiniz</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tip *</label>
                            <select name="tip" class="form-select" required>
                                <option value="blacklist">Blacklist (Engelle)</option>
                                <option value="whitelist">Whitelist (Izin Ver)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sebep</label>
                            <input type="text" name="sebep" class="form-control" placeholder="Engelleme sebebi...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bitis Tarihi (Opsiyonel)</label>
                            <input type="datetime-local" name="bitis_tarihi" class="form-control">
                            <small class="text-muted">Bos birakilirsa kalici olur</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Ekle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> IP Listesi</h5>
                        <form class="d-flex align-items-center gap-2" method="GET">
                            <select name="tip" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">Tum Tipler</option>
                                <option value="blacklist" {{ request('tip') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                <option value="whitelist" {{ request('tip') == 'whitelist' ? 'selected' : '' }}>Whitelist</option>
                            </select>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" name="ara" class="form-control" placeholder="IP ara..." value="{{ request('ara') }}">
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
                                    <th>IP Adresi</th>
                                    <th>Tip</th>
                                    <th>Sebep</th>
                                    <th>Bitis</th>
                                    <th>Durum</th>
                                    <th>Ekleyen</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ipListesi as $ip)
                                    <tr class="{{ !$ip->aktif ? 'table-secondary' : '' }}">
                                        <td><code>{{ $ip->ip_adresi }}</code></td>
                                        <td>
                                            @if($ip->tip == 'blacklist')
                                                <span class="badge bg-danger">Blacklist</span>
                                            @else
                                                <span class="badge bg-success">Whitelist</span>
                                            @endif
                                        </td>
                                        <td>{{ $ip->sebep ?? '-' }}</td>
                                        <td>
                                            @if($ip->kalici_mi)
                                                <span class="badge bg-dark">Kalici</span>
                                            @elseif($ip->suresi_doldu_mu)
                                                <span class="badge bg-secondary">Suresi Dolmus</span>
                                            @else
                                                {{ $ip->bitis_tarihi->format('d.m.Y H:i') }}
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.lists.ip.toggle', $ip) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $ip->aktif ? 'success' : 'secondary' }}">
                                                    {{ $ip->aktif ? 'Aktif' : 'Pasif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $ip->ekleyen?->ad_soyad ?? 'Sistem' }}</td>
                                        <td>
                                            <form action="{{ route('admin.lists.ip.delete', $ip) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinizden emin misiniz?')">
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
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Henüz IP kaydı yok
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ipListesi->hasPages())
                    <div class="card-footer">
                        {{ $ipListesi->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
