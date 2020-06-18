@if ($paginator->hasPages())
	{{-- Previous Page Link --}}
	@if ($paginator->onFirstPage())
		<li class="prev"><span>PREV</span></li>
	@else
		<li class="prev"><a href="{{ $paginator->previousPageUrl() }}" rel="prev"> « PREV</a></li>
	@endif

	{{-- Pagination Elements --}}
	@foreach ($elements as $element)
		{{-- "Three Dots" Separator --}}
		@if (is_string($element))
			<li class="disabled"><span>{{ $element }}</span></li>
		@endif

		{{-- Array Of Links --}}
		@if (is_array($element))
			@foreach ($element as $page => $url)
				@if ($page == $paginator->currentPage())
				<li><a class="current">{{ $page }}</a></li>
				@else
					<li><a href="{{ $url }}">{{ $page }}</a></li>
				@endif
			@endforeach
		@endif
	@endforeach

	{{-- Next Page Link --}}
	@if ($paginator->hasMorePages())
		<li class="next"><a href="{{ $paginator->nextPageUrl() }}" rel="next">NEXT » </a></li>
	@else
		<li class="disabled"><span>NEXT</span></li>
	@endif
@endif
