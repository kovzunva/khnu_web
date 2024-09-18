@extends('layouts.app')

@section('aside')	
	<div class="line"></div>
	@foreach(config('menu.admin_links') as $link)
		<a href="{{ $link['url'] }}" class="">
			{{ $link['text'] }}
		</a>
		<div class="line"></div>
	@endforeach
@endsection

@section('content')	
	<div class="mb-3">
		@yield('inner_content')
	</div>
    @vite(['resources/js/content_maker.js'])   
@endsection