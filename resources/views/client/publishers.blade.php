@extends('layouts.app')

@section('content')	

	<header class="mb-3 rel">
		<form action="{{ route('publishers') }}" method="GET">
			<div class="row">	
				<div class="col pl-1 pr-1">
					<input type="search" id="search" data-table="publishers" placeholder="Пошук персони">
				</div>

				{{-- Сортування --}}
				@include('components.sort')	

				{{-- Фільтрування --}}
				{{-- <div class="col-auto pl-1 pr-1">					
					<button type="button" class="base-btn" id="filter_btn">Фільтрувати</button>					
				</div>
				<div class="w-100 hide" id="filter_container">
					<hr>
					<div class="row">
						<div class="col mt-2">	
							<input type="checkbox" name="is_avtor" id="is_avtor" value="1" {{ request()->input('is_avtor') ? 'checked' : '' }}>
							<label for="is_avtor" class="form-label">Автор</label>
							<input type="checkbox" name="is_translator" id="is_translator" value="1" {{ request()->input('is_translator') ? 'checked' : '' }}>
							<label for="is_translator" class="form-label">Перекладач</label>
							<input type="checkbox" name="is_designer" id="is_designer" value="1" {{ request()->input('is_designer') ? 'checked' : '' }}>
							<label for="is_designer" class="form-label">Дизайнер</label>
							<input type="checkbox" name="is_illustrator" id="is_illustrator" value="1" {{ request()->input('is_illustrator') ? 'checked' : '' }}>
							<label for="is_illustrator" class="form-label">Ілюстратор</label>							
						</div>		
						<div class="col-auto">							
							<button type="button" id="filter_hide_btn">Згорнути</button>
						</div>								
					</div>
				</div> --}}
			</div>
		</form>
		<hr>
		{{-- @if ($publisher_types)
			<b>Видавництва за типом: {{$publisher_types}}</b>
			<hr>
		@endif	 --}}
	</header>	
	
	@foreach ($publishers as $publisher)
		<a href="{{ route('publisher',$publisher->id) }}" class="">
			<div class="light-box mb-3">						
				<h5 class="small-title">{{ $publisher->name }}</h5>
			</div>		
		</a>
	@endforeach
	{{ count($publishers)==0 ? "Нема видавництв за таким запитом":"" }}

	@include('components.paginator', ["paginator" => $service->paginator])

@endsection
	