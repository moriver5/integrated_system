@if ( !is_null($last_page) )
	{{-- Previous Page Link --}}
	@if ( $current_page == 1 )
		<li class="prev"><span>PREV</span></li>
	@else
		<li class="prev"><a href="{{ $prev_url }}" rel="prev"> « PREV</a></li>
	@endif

	{{-- Pagination Elements --}}
	@foreach ($elements as $page => $url)
		{{-- Array Of Links --}}
		@if ($page == $current_page)
			<li><a class="current">{{ $page }}</a></li>
		@else
			<li><a href="{{ $url }}">{{ $page }}</a></li>
		@endif
	@endforeach

	{{-- Next Page Link --}}
	@if ( $current_page != $last_page )
		<li class="next"><a href="{{ $next_url }}" rel="next">NEXT » </a></li>
	@else
		<li class="disabled"><span>NEXT</span></li>
	@endif
@endif
