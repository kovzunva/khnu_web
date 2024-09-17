@extends('layouts.app')

@section('content')	

	<header class="mb-3 rel">
		<form action="{{ route('persons') }}" method="GET">
			<div class="row">	
				<div class="col pl-1 pr-1">
					<input type="search" id="search" data-table="persons" placeholder="Пошук персони">
				</div>

				{{-- Сортування --}}
				@include('components.sort')

				{{-- Сортування --}}
				{{-- <div class="col-auto pl-1 pr-1">	
					<div class="row">							
						<div class="col">
							<div class="base-select w-auto">
								<div class="select-box">
								<span class="selected-option d-flex align-items-center">{{ $sort_name ? $sort_name : 'За іменем' }}</span>
								<ul class="options hide">
									<li data-value="1" class="change-to-submit">За іменем</li>
									<li data-value="2" class="change-to-submit">За датою додавання</li>
								</ul>
								</div>
								<input type="hidden" name="sort_id" value="{{ request()->input('sort_id') ? $sort_id : '1' }}">
							</div> 
						</div>						
						<div class="col-auto">
							<div class="input-group">
								<input type="radio" name="sort_direction" id="sort_direction_asc" value="ASC" {{$sort_direction=="ASC"? 'checked':''}}
								class="radio-sort change-to-submit">
								<label for="sort_direction_asc" class="label-sort">⭡</label>
								<input type="radio" name="sort_direction" id="sort_direction_desc" value="DESC" {{$sort_direction=="DESC"? 'checked':''}}
								class="radio-sort change-to-submit">
								<label for="sort_direction_desc" class="label-sort">⭣</label>
							</div>
						</div>
					</div>  
				</div>		 --}}

				{{-- Фільтрування --}}
				<div class="col-auto pl-1 pr-1">					
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
				</div>
			</div>
		</form>
		<hr>
		@if ($service->filter->person_types)
			<b>Персони за типом: {{$service->filter->person_types}}</b>
			<hr>
		@endif	
	</header>	
	
	@foreach ($items as $item)
		<a href="{{ route('person',$item->id) }}" class="">
			<div class="light-box mb-3">						
				<h5 class="small-title">{{ $item->name }}</h5>
				<b>						
					@if ($item->is_avtor)
						#автор
					@endif
					@if ($item->is_translator)
						#перекладач
					@endif
					@if ($item->is_illustrator)
						#ілюстратор
					@endif
					@if ($item->is_designer)
						#дизайнер
					@endif
				</b>
				@if ($item->bio)				
					<div class="mt-1 weight-400">
						{{ $service->TruncateText(strip_tags($item->bio), 160) }}
					</div>
				@endif	
			</div>		
		</a>
	@endforeach
	{{ count($items)==0 ? "Нема персон за таким запитом":"" }}

	@include('components.paginator', ["paginator" => $service->paginator])

@endsection
	