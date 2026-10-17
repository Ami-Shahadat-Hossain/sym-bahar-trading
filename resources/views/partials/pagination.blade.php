@if ($paginator->hasPages())
    <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-600">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
            of {{ $paginator->total() }}
        </div>
        <nav class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm rounded bg-gray-100 text-gray-400 cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="px-3 py-1.5 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Prev</a>
            @endif

            {{-- Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1.5 text-sm text-gray-500">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-sm rounded bg-slate-800 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="px-3 py-1.5 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="px-3 py-1.5 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-1.5 text-sm rounded bg-gray-100 text-gray-400 cursor-not-allowed">Next</span>
            @endif
        </nav>
    </div>
@endif
