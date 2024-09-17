@extends('layouts.app')

@section('content')	

<header class="mb-3 rel">
	<form action="{{ route('profiles') }}" method="GET">
		<div class="row">	
			<div class="col pl-1 pr-1">
				<input type="search" id="search" data-table="profiles" placeholder="Пошук користувача">
			</div>

			{{-- Сортування --}}
			@include('components.sort')	

			<div class="col-auto pl-1 pr-1">					
				<a href="{{ route('recommendations') }}" class="base-btn ">Рекомендації книг</a>					
			</div>			
		</div>
	</form>
	<hr>
</header>	

	@foreach ($profiles as $profile)
    	<a href="{{ route('profile', $profile->id)}}" class=""> 
			<div class="light-box mb-3 row">
				<div class="col">
					@include('components.user-item',['user' => $profile, 'another_link' => 1])
				</div>
				@if (auth()->check())						
					<div class="col-auto align-center">
						<b>
							@if (auth()->user()->id!=$profile->id)								
								Орієнтирність: {{ $profile->orienter }}
							@else
								(це ви, якщо що)
							@endif
						</b>
					</div>
				@endif
			</div>
		</a>
	@endforeach

	@include('components.paginator', ["paginator" => $service->paginator])

@endsection
	