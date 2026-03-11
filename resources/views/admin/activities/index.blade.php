@extends('admin.layouts.app')

@section('title', 'Aktivite Logları')
@section('page-title', 'Aktivite Logları')

@section('content')
<div class="card card-custom">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>Admin Aktiviteleri
    </div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>İşlem</th>
                    <th>Detay</th>
                    <th>IP</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aktiviteler as $aktivite)
                    <tr>
                        <td>
                            @if($aktivite->admin)
                                {{ $aktivite->admin->ad }} {{ $aktivite->admin->soyad }}
                            @else
                                <span class="text-muted">Sistem</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $aktivite->action_type == 'create' ? 'success' : ($aktivite->action_type == 'delete' ? 'danger' : 'info') }}">
                                {{ $aktivite->action }}
                            </span>
                        </td>
                        <td>{{ Str::limit($aktivite->description, 50) }}</td>
                        <td><code>{{ $aktivite->ip_address }}</code></td>
                        <td>{{ $aktivite->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Aktivite yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($aktiviteler->hasPages())
        <div class="card-footer">
            {{ $aktiviteler->links() }}
        </div>
    @endif
</div>
@endsection
