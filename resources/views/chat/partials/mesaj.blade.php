@php
    $isOwn = $mesaj->gonderen_id === auth()->id();
    $classes = 'mesaj';

    if ($mesaj->tip === 'sistem') {
        $classes .= ' sistem';
    } elseif ($mesaj->tip === 'teklif') {
        $classes .= $isOwn ? ' giden teklif-mesaj' : ' gelen teklif-mesaj';
    } else {
        $classes .= $isOwn ? ' giden' : ' gelen';
    }
@endphp

<div class="{{ $classes }}" data-mesaj-id="{{ $mesaj->id }}">
    @if($isOwn && $mesaj->tip === 'metin')
        <div class="mesaj-menu">
            <button class="mesaj-menu-btn" onclick="toggleMesajMenu({{ $mesaj->id }})" title="Seçenekler">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="mesaj-menu-dropdown" id="mesajMenu{{ $mesaj->id }}" style="display: none;">
                <button onclick="document.getElementById('editForm{{ $mesaj->id }}').style.display='block'; toggleMesajMenu({{ $mesaj->id }});">
                    <i class="fas fa-edit"></i> Düzenle
                </button>
                <form action="{{ route('message.delete', $mesaj->id) }}" method="POST" style="margin: 0;" class="mesaj-sil-form">
                    @csrf
                    <button type="button" class="mesaj-menu-sil mesaj-sil-btn">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </form>
            </div>
        </div>

        {{-- Düzenleme formu --}}
        <div id="editForm{{ $mesaj->id }}" style="display: none; margin-bottom: 10px;">
            <form action="{{ route('message.edit', $mesaj->id) }}" method="POST">
                @csrf
                <textarea name="mesaj" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid var(--border);" rows="2" required>{{ $mesaj->mesaj }}</textarea>
                <div style="display: flex; gap: 5px; margin-top: 5px;">
                    <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Kaydet</button>
                    <button type="button" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;"
                            onclick="document.getElementById('editForm{{ $mesaj->id }}').style.display='none';">İptal</button>
                </div>
            </form>
        </div>
    @endif
    <div class="mesaj-icerik" id="mesaj-icerik-{{ $mesaj->id }}">{!! nl2br(e($mesaj->mesaj)) !!}</div>
    <div class="mesaj-tarih">
        {{ $mesaj->formatli_tarih }}
        @if($isOwn && $mesaj->okundu)
            <i class="fas fa-check-double text-primary" title="Okundu"></i>
        @elseif($isOwn)
            <i class="fas fa-check" title="Gönderildi"></i>
        @endif
    </div>
</div>
