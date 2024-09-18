@extends('layouts.app')

@section('aside')
	@foreach(config('menu.content_maker_links') as $link)
		<a href="{{ $link['url'] }}" class="">
			{{ $link['text'] }}
		</a>
		<div class="line"></div>
	@endforeach
	@if (auth()->check() && auth()->user()->hasPermission('content-make'))
		<a href="{{ route('items-to-apply') }}" class="">
			Затвердження
		</a>
		<div class="line"></div>
		<a href="{{ route('doubles') }}" class="">
			Дублі
		</a>
		<div class="line"></div>
	@endif
@endsection

@section('content')	
	<div class="mb-3">
		@yield('inner_content')
	</div>
    @vite(['resources/js/content_maker.js'])   
@endsection