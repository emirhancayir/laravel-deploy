@extends('layouts.app')

@section('title', 'Arsivlenen Mesajlar - ZAMASON')

@section('content')
<div class="card" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="margin: 0;">
            <i class="fas fa-archive"></i> Arsivlenen Mesajlar
        </h1>
        <a href="{{ route('chat.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Mesajlarima Don
        </a>
    </div>

    @if($konusmalar->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
            <i class="fas fa-archive" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
            <h3 style="margin-bottom: 10px;">Arsivde mesaj yok</h3>
            <p>Arsivlediginiz sohbetler burada gorunecek.</p>
        </div>
    @else
        <div class="konusma-listesi">
            @foreach($konusmalar as $konusma)
                @php
                    $karsiTaraf = $konusma->karsiTaraf(auth()->user());
                @endphp
                <div class="konusma-item arsivli" style="display: flex; align-items: center;">
                    <a href="{{ route('chat.show', $konusma) }}" style="display: flex; align-items: center; flex: 1; text-decoration: none; color: inherit;">
                        <div class="konusma-avatar">
                            @if($karsiTaraf && $karsiTaraf->profil_resmi)
                                <img src="{{ asset('serve-image.php?p=profil/' . $karsiTaraf->profil_resmi) }}" alt="">
                            @else
                                <span>{{ $karsiTaraf ? strtoupper(substr($karsiTaraf->ad, 0, 1)) : '?' }}</span>
                            @endif
                        </div>
                        <div class="konusma-bilgi">
                            <div class="konusma-baslik">
                                <strong>{{ $karsiTaraf ? $karsiTaraf->ad_soyad : 'Bilinmeyen' }}</strong>
                                <span class="konusma-tarih">
                                    {{ $konusma->updated_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="konusma-urun">
                                <i class="fas fa-tag"></i> {{ Str::limit($konusma->urun->urun_adi ?? 'Silinmis urun', 30) }}
                            </div>
                            @if($konusma->sonMesaj)
                                <p class="konusma-son-mesaj">
                                    {{ Str::limit($konusma->sonMesaj->mesaj, 50) }}
                                </p>
                            @endif
                        </div>
                    </a>
                    <div style="padding: 0 15px;">
                        <form action="{{ route('chat.unarchive', $konusma) }}" method="POST" class="arsivden-cikar-form">
                            @csrf
                            <button type="button" class="btn btn-sm btn-primary arsivden-cikar-btn" title="Arsivden Cikar">
                                <i class="fas fa-inbox"></i> Arsivden Cikar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $konusmalar->links() }}
        </div>
    @endif
</div>

<style>
.konusma-item.arsivli {
    opacity: 0.85;
    background: var(--bg-secondary, #f8f9fa);
}
.konusma-item.arsivli:hover {
    opacity: 1;
}
</style>

@push('scripts')
<script>
document.querySelectorAll('.arsivden-cikar-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const form = this.closest('.arsivden-cikar-form');

        const confirmed = await showConfirm({
            type: 'info',
            title: 'Arsivden Cikar',
            message: 'Bu sohbeti arsivden cikarmak istediginize emin misiniz?',
            confirmText: 'Evet, Cikar',
            cancelText: 'Vazgec'
        });

        if (confirmed) {
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
