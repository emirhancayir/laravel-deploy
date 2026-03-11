@if ($paginator->hasPages())
<nav class="pagination-nav">
    {{-- Önceki Sayfa --}}
    @if ($paginator->onFirstPage())
        <span class="pagination-btn disabled">
            <i class="fas fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
    @endif

    {{-- Sayfa Numaraları --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pagination-btn disabled">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Sonraki Sayfa --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">
            <i class="fas fa-chevron-right"></i>
        </a>
    @else
        <span class="pagination-btn disabled">
            <i class="fas fa-chevron-right"></i>
        </span>
    @endif
</nav>
@endif
