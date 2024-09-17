@extends('layouts.app')

@section('aside')	
@endsection

@section('content')	
<header class="mb-3 rel">
	<form action="{{ route('forums') }}" method="GET">
		<div class="row">	
			<div class="col pl-1 pr-1">
				<input type="search" id="search" data-table="forums" placeholder="Пошук форуму">
			</div>

			{{-- Сортування --}}
			@include('components.sort')	

			{{-- Фільтрування --}}
			{{-- <div class="col-auto pl-1 pr-1">					
				<button type="button" class="base-btn" id="filter_btn">Фільтрувати</button>					
			</div>
			<div class="w-100 hide" id="filter_container">
				<hr>
				<div class="text-end mb-2">						
					<button type="button" id="filter_clear_btn" class="ml-auto">Скинути опції</button>
					<button type="button" id="filter_hide_btn">Згорнути</button>								
				</div>	
				<div class="mb-3">
					<label for="" class="form-label">Тематика</label>
					<div class="base-select">
						<div class="select-box">
							<span class="selected-option d-flex align-items-center">
								@if (!request()->has('tematic') || request()->input('tematic') == 'all')
									Всі категорії
								@elseif (request()->input('tematic') == '')
									Загальна тематика
								@else
									{{ $selectedTematic->name }}
								@endif
							</span>
							<ul class="options hide">
								<li data-value="all" class="filter-option">Всі тематики</li>
								<li data-value="" class="filter-option">Загальна тематика</li>
								@foreach ($tematics as $tematic)
									<li data-value="{{ $tematic->id }}" class="filter-option">{{ $tematic->name }}</li>
								@endforeach
							</ul>
						</div>
						<input type="hidden" name="tematic" value="
							@if (!request()->has('tematic') || request()->input('tematic') == 'all')
								all
							@elseif (request()->input('tematic') == '')
								
							@else
								{{ $selectedTematic->id }}
							@endif
						">
					</div>     
				</div>
			</div> --}}
		</div>
	</form>
	<hr>
	{{-- @if (!request()->has('tematic') || request()->input('tematic') == 'all')
	
	@elseif (request()->input('tematic') == '')
		<b>Форуми за запитом: Загальна тематика</b>
		<hr>		
	@else
		<b>Форуми за запитом: {{$selectedTematic->name}}</b>
		<hr>
	@endif --}}
</header>		

	@foreach ($forums as $forum)
		<a href="{{route('forum',[$forum->id])}}">
			<div class="with-image-box row mb-3">	
				<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
					<div class="rel with-image-box-imgs">
						@if ($forum->img)						
							<img src="{{asset($forum->img)}}" alt="Зображення форуму">
						@else
							<div class="work-without-img">
								<span>-</span>						
							</div>
						@endif	
					</div>
				</div>				
				<div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
					<div class="pl-3 pr-3 w-100">								
						<h5>{{ $forum->name }}</h5>
						<hr>
						{{ Str::limit($forum->about, 160) }}
					</div>
				</div>	
			</div>
		</a>
	@endforeach
	{{ count($forums)==0? "Нема форумів.":"" }}

	@include('components.paginator', ["paginator" => $service->paginator])
@endsection
