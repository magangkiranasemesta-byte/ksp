{{-- Pagination ringkas & responsif. Pemakaian: @include('partials.pagination', ['paginator' => $items]) --}}
@if($paginator->hasPages())
    <nav class="flex flex-col sm:flex-row items-center justify-between gap-3 px-1 py-3" aria-label="Pagination">
        <p class="text-xs text-slate-500">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </p>

        <div class="inline-flex items-stretch rounded-xl border border-slate-200 overflow-hidden bg-white">
            @if($paginator->onFirstPage())
                <span class="min-w-[42px] px-3 py-2 text-sm text-slate-300 border-r border-slate-200 text-center">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" aria-label="Sebelumnya"
                   class="min-w-[42px] px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 border-r border-slate-200 text-center">‹</a>
            @endif

            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="min-w-[42px] px-3 py-2 text-sm font-semibold text-slate-800 bg-slate-100 border-r border-slate-200 text-center">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="min-w-[42px] px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 border-r border-slate-200 text-center">{{ $page }}</a>
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" aria-label="Berikutnya"
                   class="min-w-[42px] px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 text-center">›</a>
            @else
                <span class="min-w-[42px] px-3 py-2 text-sm text-slate-300 text-center">›</span>
            @endif
        </div>
    </nav>
@endif
