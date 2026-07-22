@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="text-gray-400 px-3 py-1 font-semibold cursor-not-allowed">Önceki</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="text-brand hover:text-brand-dark px-3 py-1 font-semibold transition">Önceki</a>
        @endif

        <span class="text-gray-300">,</span>

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="text-gray-500 px-2">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="text-brand font-extrabold px-1">[{{ $page }}]</span>
                    @else
                        <a href="{{ $url }}" class="text-gray-600 hover:text-brand font-semibold px-1 transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <span class="text-gray-300">,</span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="text-brand hover:text-brand-dark px-3 py-1 font-semibold transition">Sonraki</a>
        @else
            <span class="text-gray-400 px-3 py-1 font-semibold cursor-not-allowed">Sonraki</span>
        @endif

    </nav>
@endif
