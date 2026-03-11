@extends('admin.layouts.app')

@section('title', 'Kullanıcı Engelleri Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Kullanıcı Engelleri Yönetimi</h1>
        <a href="{{ route('admin.lists.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-slash"></i> Kullanıcı Engelleri</h5>
                <form method="GET">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" name="ara" class="form-control" placeholder="Kullanici ara..." value="{{ request('ara') }}">
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
                            <th>Engelleyen</th>
                            <th></th>
                            <th>Engellenen</th>
                            <th>Sebep</th>
                            <th>Tarih</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($engeller as $engel)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($engel->engelleyen->profil_resmi)
                                            <img src="{{ asset('serve-image.php?p=profil/' . $engel->engelleyen->profil_resmi) }}" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                                <i class="fas fa-user text-white small"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $engel->engelleyen->ad_soyad }}</strong>
                                            <br><small class="text-muted">{{ $engel->engelleyen->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <i class="fas fa-arrow-right text-danger"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($engel->engellenen->profil_resmi)
                                            <img src="{{ asset('serve-image.php?p=profil/' . $engel->engellenen->profil_resmi) }}" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                                <i class="fas fa-user text-white small"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $engel->engellenen->ad_soyad }}</strong>
                                            <br><small class="text-muted">{{ $engel->engellenen->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $engel->sebep ?? '-' }}</td>
                                <td>{{ $engel->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.lists.engel.kaldir', $engel) }}" method="POST" class="d-inline" onsubmit="return confirm('Engeli kaldirmak istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-unlock"></i> Engeli Kaldir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Henüz kullanıcı engeli yok
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($engeller->hasPages())
            <div class="card-footer">
                {{ $engeller->links() }}
            </div>
        @endif
    </div>

    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle"></i>
        <strong>Bilgi:</strong> Kullanıcılar birbirlerini engelleyebilir. Engellenen kullanıcı, engelleyen kullanıcıya mesaj gönderemez, ürünlerini göremez ve teklif veremez.
        Admin olarak gereksiz engelleri kaldırabilisiniz.
    </div>
</div>
@endsection
