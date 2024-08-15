<div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
    <nav class="w-full sm:w-auto sm:mr-auto">
        <ul class="pagination">
            <li class="page-item">
                {{--                        {{ dd($paginator->getOptions()) }}--}}
                <a class="page-link" href="{{ $paginator->url(1) }}">
                    <i class="w-4 h-4" data-feather="chevrons-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                    <i class="w-4 h-4" data-feather="chevron-left"></i>
                </a>
            </li>
            @if($paginator->currentPage() > 2)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->currentPage() - 2) }}">...</a>
                </li>
            @endif
            @if(!$paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $paginator->url($paginator->currentPage() - 1) }}">{{ $paginator->currentPage() - 1 }}</a>
                </li>
            @endif
            <li class="page-item active">
                <a class="page-link" href="{{ $paginator->url($paginator->currentPage()) }}">{{ $paginator->currentPage() }}</a>
            </li>
            @if($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $paginator->url($paginator->currentPage() + 1) }}">{{ $paginator->currentPage() + 1 }}</a>
                </li>
            @endif
            @if($paginator->lastPage() - 2 > $paginator->currentPage())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->currentPage() + 2) }}">...</a>
                </li>
            @endif
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl()	 }}">
                    <i class="w-4 h-4" data-feather="chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">
                    <i class="w-4 h-4" data-feather="chevrons-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>
