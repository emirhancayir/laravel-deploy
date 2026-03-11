@extends('admin.layouts.app')

@section('title', 'IP Yönetimi')
@section('page-title', 'IP Yönetimi')

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-network-wired me-2"></i>Son IP Aktiviteleri</span>
                <a href="{{ route('admin.ip.bans') }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-ban me-1"></i>Banlı IP'ler
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>IP Adresi</th>
                            <th>İşlem</th>
                            <th>Kullanıcı</th>
                            <th>Tarih</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <code>{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->action == 'registration' ? 'success' : ($log->action == 'login' ? 'info' : 'secondary') }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('admin.users.show', $log->user) }}">
                                            {{ $log->user->ad }} {{ $log->user->soyad }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.ip.ban') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="ip" value="{{ $log->ip_address }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu IP\'yi banlamak istediğinize emin misiniz?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Kayıt yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-ban me-2"></i>IP Banla
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ip.ban') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">IP Adresi</label>
                        <input type="text" name="ip" class="form-control" required placeholder="192.168.1.1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sebep</label>
                        <textarea name="sebep" class="form-control" rows="3" placeholder="Ban sebebi..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Süre</label>
                        <select name="sure" class="form-select">
                            <option value="1">1 Gün</option>
                            <option value="7">1 Hafta</option>
                            <option value="30">1 Ay</option>
                            <option value="365">1 Yıl</option>
                            <option value="0">Süresiz</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-ban me-2"></i>Banla
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-custom mt-4">
            <div class="card-header">
                <i class="fas fa-search me-2"></i>IP Ara
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ip.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="ip" class="form-control" placeholder="IP adresi...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
