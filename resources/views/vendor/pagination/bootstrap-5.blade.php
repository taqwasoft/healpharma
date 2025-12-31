@if ($paginator->hasPages())
    <nav class="p-3">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <p class="text-muted">
                    {{ __('Showing') }}
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    {{ __('results') }}
                </p>
            </div>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">{{ __('Previous') }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">{{ __('Previous') }}</a></li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @php
                            $count = count($element);
                        @endphp

                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @elseif ($page <= 7 || $page > $count - 2 || abs($page - $paginator->currentPage()) < 2)
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @elseif ($page == 8)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @break
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">{{ __('Next') }}</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">{{ __('Next') }}</span></li>
                @endif
            </ul>
        </div>
    </nav>
@endif
