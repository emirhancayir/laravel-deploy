@extends('admin.layouts.app')

@section('title', 'Kullanıcı Yönetimi')
@section('page-title', 'Kullanıcı Yönetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Sistemdeki tüm kullanıcıları yönetin</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Yeni Kullanıcı Ekle
    </a>
</div>

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2"></i>Tüm Kullanıcılar ({{ $users->total() }})</span>
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="ara" class="form-control form-control-sm" placeholder="İsim, email ara..." value="{{ request('ara') }}">
            <select name="tip" class="form-select form-select-sm" style="width:auto;">
                <option value="">Tüm Tipler</option>
                <option value="alici" {{ request('tip') == 'alici' ? 'selected' : '' }}>Alıcı</option>
                <option value="satici" {{ request('tip') == 'satici' ? 'selected' : '' }}>Satıcı</option>
                <option value="admin" {{ request('tip') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <select name="durum" class="form-select form-select-sm" style="width:auto;">
                <option value="">Tüm Durumlar</option>
                <option value="aktif" {{ request('durum') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="banli" {{ request('durum') == 'banli' ? 'selected' : '' }}>Banlı</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı</th>
                    <th>Tip</th>
                    <th>Kayıt Tarihi</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <strong>{{ $user->ad }} {{ $user->soyad }}</strong>
                            <br><small class="text-muted">{{ $user->email }}</small>
                            <br><small class="text-muted">{{ $user->telefon }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->kullanici_tipi == 'satici' ? 'success' : ($user->kullanici_tipi == 'admin' || $user->kullanici_tipi == 'super_admin' ? 'danger' : 'info') }}">
                                {{ ucfirst($user->kullanici_tipi) }}
                            </span>
                        </td>
                        <td>
                            {{ $user->created_at->format('d.m.Y H:i') }}
                            <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($user->is_banned)
                                <span class="badge bg-danger">Banlı</span>
                                <br><small class="text-muted">{{ $user->ban_reason }}</small>
                            @elseif($user->email_verified)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-warning">Doğrulanmamış</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="Detay">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$user->is_banned)
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}" title="Banla">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Ban Kaldır">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!$user->superAdminMi() && $user->id !== auth()->id())
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#silModal{{ $user->id }}" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Ban Modal -->
                            <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kullanıcı Banla</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>{{ $user->ad }} {{ $user->soyad }}</strong> kullanıcısını banlamak istediğinize emin misiniz?</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Ban Sebebi</label>
                                                    <textarea name="sebep" class="form-control" rows="3" required placeholder="Ban sebebini yazın..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                <button type="submit" class="btn btn-danger">Banla</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Sil Modal -->
                            @if(!$user->superAdminMi() && $user->id !== auth()->id())
                            <div class="modal fade" id="silModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Kullanıcıyı Sil</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning mb-3">
                                                    <i class="fas fa-warning me-2"></i><strong>Bu işlem geri alınamaz!</strong>
                                                </div>
                                                <p><strong>{{ $user->ad }} {{ $user->soyad }}</strong> ({{ $user->email }}) kullanıcısını silmek istediğinize emin misiniz?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Sil</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Kullanıcı bulunamadı</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
