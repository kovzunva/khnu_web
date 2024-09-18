@extends('layouts.app')

@section('aside')
	@foreach(config('menu.profile_links') as $link)
		<a href="{{ $link['url'] }}" class="">
			{{ $link['text'] }}
		</a>
		<div class="line"></div>
	@endforeach
@endsection

@section('content')	
	@include('components.profile-header')
	@yield('inner_content')
@endsection