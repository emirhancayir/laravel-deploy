@extends('admin.layouts.app')

@section('title', 'Kullanıcı Detayı')
@section('page-title', 'Kullanıcı Detayı')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;font-size:32px;">
                    {{ strtoupper(substr($user->ad, 0, 1)) }}
                </div>
                <h4>{{ $user->ad }} {{ $user->soyad }}</h4>
                <p class="text-muted">{{ $user->email }}</p>

                <span class="badge bg-{{ $user->kullanici_tipi == 'satici' ? 'success' : ($user->kullanici_tipi == 'admin' ? 'danger' : 'info') }} mb-3">
                    {{ ucfirst($user->kullanici_tipi) }}
                </span>

                @if($user->is_banned)
                    <div class="alert alert-danger">
                        <i class="fas fa-ban me-2"></i>Bu kullanıcı banlı
                        <br><small>{{ $user->ban_reason }}</small>
                    </div>
                @endif

                <hr>

                <div class="text-start">
                    <p><i class="fas fa-phone me-2 text-muted"></i>{{ $user->telefon ?? '-' }}</p>
                    <p><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $user->adres ?? '-' }}</p>
                    <p><i class="fas fa-calendar me-2 text-muted"></i>Kayıt: {{ $user->created_at->format('d.m.Y H:i') }}</p>
                    <p><i class="fas fa-globe me-2 text-muted"></i>Kayıt IP: {{ $user->registration_ip ?? '-' }}</p>
                    <p><i class="fas fa-sign-in-alt me-2 text-muted"></i>Son Giriş IP: {{ $user->last_login_ip ?? '-' }}</p>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    @if(!$user->is_banned)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#banModal">
                            <i class="fas fa-ban me-2"></i>Banla
                        </button>
                    @else
                        <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Ban Kaldır
                            </button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tipModal">
                        <i class="fas fa-user-tag me-2"></i>Tip Değiştir
                    </button>

                    @if(!$user->superAdminMi() && $user->id !== auth()->id())
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#silModal">
                            <i class="fas fa-trash me-2"></i>Kullanıcıyı Sil
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if($user->saticiMi())
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <i class="fas fa-store me-2"></i>Satıcı Bilgileri
                </div>
                <div class="card-body">
                    <p><strong>Firma:</strong> {{ $user->firma_adi ?? '-' }}</p>
                    <p><strong>Vergi No:</strong> {{ $user->vergi_no ?? '-' }}</p>
                    <p><strong>IBAN:</strong> {{ $user->iban ?? '-' }}</p>
                    <p><strong>Onay Tarihi:</strong> {{ $user->satici_onay_tarihi ? $user->satici_onay_tarihi->format('d.m.Y') : '-' }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-8">
        <!-- İstatistikler -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card blue">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="value">{{ $user->urunler()->count() }}</div>
                            <div class="label">Ürün</div>
                        </div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card green">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="value">{{ $user->teklifleri()->count() }}</div>
                            <div class="label">Teklif</div>
                        </div>
                        <div class="icon"><i class="fas fa-handshake"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card orange">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="value">{{ $user->aliciKonusmalari()->count() + $user->saticiKonusmalari()->count() }}</div>
                            <div class="label">Konuşma</div>
                        </div>
                        <div class="icon"><i class="fas fa-comments"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Ürünler -->
        <div class="card card-custom mb-4">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>Son Ürünleri
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->urunler()->latest()->take(5)->get() as $urun)
                            <tr>
                                <td>{{ Str::limit($urun->urun_adi, 30) }}</td>
                                <td>{{ number_format($urun->fiyat, 2) }} TL</td>
                                <td>
                                    <span class="badge bg-{{ $urun->durum == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($urun->durum) }}
                                    </span>
                                </td>
                                <td>{{ $urun->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Ürün yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Son Teklifler -->
        <div class="card card-custom">
            <div class="card-header">
                <i class="fas fa-handshake me-2"></i>Son Teklifleri
            </div>
            <div class="card-body p-0">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Teklif</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->teklifleri()->with('konusma.urun')->latest()->take(5)->get() as $teklif)
                            <tr>
                                <td>{{ Str::limit($teklif->konusma->urun->urun_adi ?? '-', 30) }}</td>
                                <td>{{ number_format($teklif->tutar, 2, ',', '.') }} TL</td>
                                <td>
                                    <span class="badge bg-{{ $teklif->durum == 'kabul_edildi' ? 'success' : ($teklif->durum == 'reddedildi' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $teklif->durum)) }}
                                    </span>
                                </td>
                                <td>{{ $teklif->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Teklif yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ban Modal -->
<div class="modal fade" id="banModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kullanıcı Banla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ban Sebebi</label>
                        <textarea name="sebep" class="form-control" rows="3" required></textarea>
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

<!-- Tip Değiştir Modal -->
<div class="modal fade" id="tipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.changeTip', $user) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kullanıcı Tipi Değiştir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Yeni Tip</label>
                        <select name="tip" class="form-select" required>
                            <option value="alici" {{ $user->kullanici_tipi == 'alici' ? 'selected' : '' }}>Alıcı</option>
                            <option value="satici" {{ $user->kullanici_tipi == 'satici' ? 'selected' : '' }}>Satıcı</option>
                            <option value="admin" {{ $user->kullanici_tipi == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ $user->kullanici_tipi == 'super_admin' ? 'selected' : '' }}>Süper Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kullanıcı Sil Modal -->
@if(!$user->superAdminMi() && $user->id !== auth()->id())
<div class="modal fade" id="silModal" tabindex="-1">
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
                    <div class="alert alert-warning">
                        <i class="fas fa-warning me-2"></i>
                        <strong>Dikkat!</strong> Bu işlem geri alınamaz.
                    </div>
                    <p><strong>{{ $user->ad }} {{ $user->soyad }}</strong> ({{ $user->email }}) kullanıcısını silmek istediğinize emin misiniz?</p>
                    <p class="text-muted mb-0">Bu kullanıcıya ait tüm veriler (ürünler, teklifler, mesajlar vb.) da silinecektir.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Evet, Sil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-4">
    <i class="fas fa-arrow-left me-2"></i>Geri Dön
</a>
@endsection
