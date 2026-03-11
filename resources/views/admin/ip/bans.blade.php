@extends('admin.layouts.app')

@section('title', 'Banlı IP\'ler')
@section('page-title', 'Banlı IP\'ler')

@section('content')
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-ban me-2"></i>Banlı IP Adresleri</span>
        <a href="{{ route('admin.ip.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Geri
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead>
                <tr>
                    <th>IP Adresi</th>
                    <th>Sebep</th>
                    <th>Banlayan</th>
                    <th>Ban Tarihi</th>
                    <th>Bitiş</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bans as $ban)
                    <tr>
                        <td><code>{{ $ban->ip_address }}</code></td>
                        <td>{{ $ban->reason ?? '-' }}</td>
                        <td>
                            @if($ban->bannedBy)
                                {{ $ban->bannedBy->ad }}
                            @else
                                <span class="text-muted">Sistem</span>
                            @endif
                        </td>
                        <td>{{ $ban->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($ban->expires_at)
                                {{ $ban->expires_at->format('d.m.Y') }}
                            @else
                                <span class="badge bg-danger">Süresiz</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.ip.unban', $ban->ip_address) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Ban kaldırılsın mı?')">
                                    <i class="fas fa-check"></i> Kaldır
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Banlı IP yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bans->hasPages())
        <div class="card-footer">
            {{ $bans->links() }}
        </div>
    @endif
</div>
@endsection
