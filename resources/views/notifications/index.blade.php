@extends('layouts.app')

@section('title', 'Bildirimler')

@section('content')
<div class="container py-5">
    <div class="bildirimler-container">
        <div class="bildirimler-header">
            <h1><i class="fas fa-bell"></i> Bildirimler</h1>
            @if($bildirimler->total() > 0)
                <button class="btn btn-secondary" onclick="tumunuOkunduIsaretle()">
                    <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
                </button>
            @endif
        </div>

        @if($bildirimler->isEmpty())
            <div class="bildirim-bos">
                <i class="fas fa-bell-slash"></i>
                <p>Henüz bildiriminiz bulunmuyor.</p>
            </div>
        @else
            <div class="bildirimler-liste">
                @foreach($bildirimler as $bildirim)
                    <div class="bildirim-item {{ is_null($bildirim->read_at) ? 'unread' : 'read' }}" data-id="{{ $bildirim->id }}">
                        <div class="bildirim-ikon">
                            @php
                                $ikon = $bildirim->data['ikon'] ?? 'bell';
                                $ikonMap = [
                                    'message' => 'comments',
                                    'offer' => 'tags',
                                    'check-circle' => 'check-circle',
                                    'x-circle' => 'times-circle',
                                    'truck' => 'truck',
                                    'credit-card' => 'credit-card',
                                    'star' => 'star',
                                ];
                                $ikonClass = $ikonMap[$ikon] ?? $ikon;
                            @endphp
                            <i class="fas fa-{{ $ikonClass }}"></i>
                        </div>
                        <div class="bildirim-icerik">
                            <h4>{{ $bildirim->data['baslik'] ?? 'Bildirim' }}</h4>
                            <p>{{ $bildirim->data['mesaj'] ?? '' }}</p>
                            <span class="bildirim-tarih">{{ $bildirim->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="bildirim-aksiyonlar">
                            @if(isset($bildirim->data['link']) || isset($bildirim->data['konusma_id']) || isset($bildirim->data['teklif_id']))
                                @php
                                    // Linki belirle
                                    $link = $bildirim->data['link'] ?? '#';

                                    // Eski /mesajlar/ linklerini /sohbet/ olarak düzelt
                                    $link = str_replace('/mesajlar/', '/sohbet/', $link);

                                    // Teklif bildirimleri için sohbete yönlendir
                                    if (isset($bildirim->data['teklif_id']) && isset($bildirim->data['konusma_id'])) {
                                        $link = '/sohbet/' . $bildirim->data['konusma_id'];
                                    } elseif (isset($bildirim->data['teklif_id'])) {
                                        // konusma_id yoksa teklif'ten al
                                        $teklif = \App\Models\Teklif::find($bildirim->data['teklif_id']);
                                        if ($teklif) {
                                            $link = '/sohbet/' . $teklif->konusma_id;
                                        }
                                    }
                                @endphp
                                <a href="{{ $link }}" class="btn btn-sm btn-primary" onclick="okunduIsaretle('{{ $bildirim->id }}')">
                                    <i class="fas fa-arrow-right"></i> Git
                                </a>
                            @endif
                            @if(is_null($bildirim->read_at))
                                <button class="btn btn-sm btn-outline" onclick="okunduIsaretle('{{ $bildirim->id }}')">
                                    <i class="fas fa-check"></i> Okundu
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $bildirimler->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.bildirimler-container {
    max-width: 800px;
    margin: 0 auto;
}

.bildirimler-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.bildirimler-header h1 {
    font-size: 2rem;
    font-weight: bold;
}

.bildirim-bos {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.bildirim-bos i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.bildirimler-liste {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.bildirim-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.bildirim-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.bildirim-item.unread {
    background: #f0f7ff;
    border-left: 4px solid #007bff;
}

.bildirim-ikon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #007bff;
    color: white;
    border-radius: 50%;
    font-size: 1.25rem;
}

.bildirim-item.unread .bildirim-ikon {
    background: #28a745;
}

.bildirim-item.read {
    background: var(--card-bg, white);
    border-left: 4px solid transparent;
}

.bildirim-ikon {
    transition: background 0.3s ease;
}

.bildirim-aksiyonlar button {
    transition: opacity 0.3s ease;
}

.bildirim-icerik {
    flex: 1;
}

.bildirim-icerik h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.bildirim-icerik p {
    margin: 0 0 0.5rem 0;
    color: #666;
}

.bildirim-tarih {
    font-size: 0.875rem;
    color: #999;
}

.bildirim-aksiyonlar {
    display: flex;
    gap: 0.5rem;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}
</style>

<script>
function okunduIsaretle(id, event) {
    // Eğer link tıklandıysa sayfaya git, ama önce okundu işaretle
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        const item = document.querySelector(`[data-id="${id}"]`);
        if (item) {
            // Animasyonlu geçiş
            item.style.transition = 'all 0.3s ease';
            item.classList.remove('unread');
            item.classList.add('read');

            // İkon rengini değiştir
            const ikon = item.querySelector('.bildirim-ikon');
            if (ikon) {
                ikon.style.background = '#007bff';
            }

            // Okundu butonunu kaldır
            const okunduBtn = item.querySelector('button[onclick*="okunduIsaretle"]');
            if (okunduBtn) {
                okunduBtn.style.opacity = '0';
                setTimeout(() => okunduBtn.remove(), 300);
            }
        }

        // Header'daki bildirim sayısını güncelle
        guncelleHeaderBildirimSayisi();

        showToast('Bildirim okundu olarak işaretlendi', 'success');
    })
    .catch(err => {
        console.error(err);
        showToast('Bir hata oluştu', 'error');
    });
}

async function tumunuOkunduIsaretle() {
    const confirmed = await showConfirm({
        type: 'info',
        title: 'Tümünü Okundu İşaretle',
        message: 'Tüm bildirimleri okundu olarak işaretlemek istediğinize emin misiniz?',
        confirmText: 'Evet, İşaretle',
        cancelText: 'Vazgeç'
    });

    if (!confirmed) return;

    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        // Tüm bildirimleri anlık olarak okundu yap
        document.querySelectorAll('.bildirim-item.unread').forEach(item => {
            item.style.transition = 'all 0.3s ease';
            item.classList.remove('unread');
            item.classList.add('read');

            // İkon rengini değiştir
            const ikon = item.querySelector('.bildirim-ikon');
            if (ikon) {
                ikon.style.background = '#007bff';
            }

            // Okundu butonlarını kaldır
            const okunduBtn = item.querySelector('button[onclick*="okunduIsaretle"]');
            if (okunduBtn) okunduBtn.remove();
        });

        // "Tümünü Okundu İşaretle" butonunu gizle
        const tumunuBtn = document.querySelector('.bildirimler-header .btn-secondary');
        if (tumunuBtn) {
            tumunuBtn.style.opacity = '0';
            setTimeout(() => tumunuBtn.style.display = 'none', 300);
        }

        // Header'daki bildirim sayısını güncelle
        guncelleHeaderBildirimSayisi();

        showToast('Tüm bildirimler okundu olarak işaretlendi', 'success');
    })
    .catch(err => {
        console.error(err);
        showToast('Bir hata oluştu', 'error');
    });
}

// Header'daki bildirim badge'ini güncelle
function guncelleHeaderBildirimSayisi() {
    fetch('/notifications/unread-count', {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        const badge = document.querySelector('.notification-count');
        if (data.sayi > 0) {
            if (badge) {
                badge.textContent = data.sayi > 99 ? '99+' : data.sayi;
            }
        } else {
            if (badge) badge.remove();
        }
    })
    .catch(console.error);
}
</script>
@endsection
