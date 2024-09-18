@extends('layouts.admin')

@section('inner_content')	

	<div class="header-box row align-center">
		<h1 class="col">{{$item_title}} з незаповненими даними</h1>
		<div class="col-auto">
			<a href="/content-maker/{{$item_type}}" class="btn-with-icon">
				<img src="{{ asset('svg/plus.svg') }}" alt="">
				Додати {{$item_add}}
			</a>
		</div>
	</div>
		
	<div>
		@foreach ($items as $item)
			<a href="/content-maker/{{$item_type}}/{{$item->id}}" class="">
				<div class="light-box mb-3">
					{{ $item->name }}
				</div>
			</a>
		@endforeach
		
		@if (!$items)
			<p>Диво дивне, нема таких.</p>
		@endif
	</div>	

@endsection
	