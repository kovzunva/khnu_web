@extends('layouts.app')

@section('content')	

	<div class="header-box rel">
		<h1>{{$faq->name}}</h1>
		<hr>
		<div><b>{{$faq->keywords}}</b></div>

		{{-- Кнопка налаштувань --}}
		@if (auth()->user() && auth()->user()->hasPermission('admin'))
			<div class="options-btn">					
				<div class="custom-dropdown-btn">
					<img src="{{ asset('svg/elipsis.svg') }}" class="icon">
				</div>
				<div class="custom-dropdown-menu">
					<a class="dropdown-item " href="{{route('faq.editForm',$faq->id)}}">Редагувати</a>
				</div>
			</div>
		@endif	
	</div>

	<div class="text-from-editor mt-3 classic-links">
		{!! $faq->content !!}
	</div>

	<div class="row">
		<div class="col-auto">
			@if ($previous)
				<a href="{{ route('faq',$previous->id) }}" class="">
					<span class="transparent-btn w-275">
						<img src="{{ asset('svg/arrow-left.svg') }}" class="icon">
						{{ Str::limit(strip_tags($previous->name), 19)}}
					</span>					
				</a>
			@endif
		</div>
		<div class="col"></div>
		<div class="col-auto">
			@if ($next)
				<a href="{{ route('faq',$next->id) }}" class="">
					<span class="transparent-btn w-275 text-end">
						{{ Str::limit(strip_tags($next->name), 19)}}
						<img src="{{ asset('svg/arrow-right.svg') }}" class="icon">
					</span>					
				</a>
			@endif
		</div>
	</div>

@endsection


	