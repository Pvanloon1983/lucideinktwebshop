@props([
    // Each: ['label' => 'Text', 'url' => route(...)]
    'items' => [],
])

@php $count = count($items); @endphp

<nav class="bc" aria-label="Breadcrumb">
    <ol class="bc-list" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach ($items as $i => $item)
            @php $isLast = $i === $count - 1; @endphp

            <li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if($i === 0)
                    <svg aria-hidden="true" class="bc-home" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 1.293a1 1 0 0 0-1.414 0L2 8.586V17a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4h4v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V8.586l-7.293-7.293z"/>
                    </svg>
                @endif

                @if(!empty($item['url']) && !$isLast)
                    <a href="{{ $item['url'] }}" class="bc-link" itemprop="item">
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="bc-current" itemprop="name" aria-current="page">{{ $item['label'] }}</span>
                @endif

                <meta itemprop="position" content="{{ $i + 1 }}" />
            </li>

            @if(!$isLast)
                <span class="bc-sep" aria-hidden="true">
          <i class="fa-solid fa-chevron-right bc-chev"></i>
        </span>
            @endif
        @endforeach
    </ol>
</nav>
