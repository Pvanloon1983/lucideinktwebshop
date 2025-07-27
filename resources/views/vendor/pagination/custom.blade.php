<nav class="custom-pagination" role="navigation" aria-label="Paginanavigatie">
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled prev" aria-disabled="true" aria-label="Vorige">
                <span aria-hidden="true">&laquo; Vorige</span>
            </li>
        @else
            <li class="prev">
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Vorige">&laquo; Vorige</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled dots" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active page-num" aria-current="page"><span>{{ $page }}</span></li>
                    @else
                        <li class="page-num"><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="next">
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Volgende">Volgende &raquo;</a>
            </li>
        @else
            <li class="disabled next" aria-disabled="true" aria-label="Volgende">
                <span aria-hidden="true">Volgende &raquo;</span>
            </li>
        @endif
    </ul>
</nav>
