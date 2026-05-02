@if ($paginator->hasPages())
<nav aria-label="{{ __('cms.general.pagination') ?? 'Pagination' }}" class="cms-pagination">

    {{-- ── Previous ──────────────────────────────────────── --}}
    @if ($paginator->onFirstPage())
        <span class="cms-page-btn disabled" aria-disabled="true">
            <i class="bi bi-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="cms-page-btn" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
        </a>
    @endif

    {{-- ── Page Numbers ──────────────────────────────────── --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" separator --}}
        @if (is_string($element))
            <span class="cms-page-btn cms-page-dots" aria-disabled="true">{{ $element }}</span>
        @endif

        {{-- Page Number Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="cms-page-btn active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="cms-page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- ── Next ──────────────────────────────────────────── --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="cms-page-btn" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
        </a>
    @else
        <span class="cms-page-btn disabled" aria-disabled="true">
            <i class="bi bi-chevron-right"></i>
        </span>
    @endif

</nav>
@endif
