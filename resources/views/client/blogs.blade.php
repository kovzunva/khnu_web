@extends('layouts.app')


@section('aside')	
@endsection


@section('content')	

<header class="mb-3 rel">
	<form action="{{ route('blogs') }}" method="GET">
		<div class="row">	
			<div class="col pl-1 pr-1">
				<input type="search" id="search" data-table="blogs" placeholder="Пошук блогу">
			</div>

			{{-- Сортування --}}
			@include('components.sort')

			{{-- Фільтрування --}}
			<div class="col-auto pl-1 pr-1">					
				<button type="button" class="base-btn" id="filter_btn">Фільтрувати</button>					
			</div>
			<div class="w-100 hide" id="filter_container">
				<hr>
				<div class="text-end mb-2">						
					<button type="button" id="filter_clear_btn" class="ml-auto">Скинути опції</button>
					<button type="button" id="filter_hide_btn">Згорнути</button>								
				</div>	
				<div class="align-center gap-1 mb-3">
					<div class="base-select w-auto">
						<div class="select-box">
							<span class="selected-option d-flex align-items-center">
								@if (!request()->has('category') || request()->input('category') == 'all')
									Всі категорії
								@elseif (request()->input('category') == '')
									Інше
								@else
									{{ $service->filter->selectedCategory->name }}
								@endif
							</span>
							<ul class="options hide">
								<li data-value="all" class="filter-option">Всі категорії</li>
								<li data-value="" class="filter-option">Інше</li>
								@foreach ($categories as $category)
									<li data-value="{{ $category->id }}" class="filter-option">{{ $category->name }}</li>
								@endforeach
							</ul>
						</div>
						<input type="hidden" name="category" value="
							{{-- @if (!request()->has('category') || request()->input('category') == 'all')
								all
							@elseif (request()->input('category') == '')
								
							@else
								{{ $selectedCategory->id }}
							@endif --}}
							{{ request()->input('category') }}
						">
					</div>   
					<div>
						<label for="" class="form-label">Дата (від)</label>
						<input type="text" class="input-date" name="date_from" value="{{ $service->filter->selectedDateFrom }}">  
					</div>  
					<div>						
						<label for="" class="form-label">Дата (до)</label>
						<input type="text" class="input-date" name="date_to" value="{{ $service->filter->selectedDateTo }}"> 
					</div> 
				</div>
			</div>
		</div>
	</form>
	<hr>
	@if ($service->filter->filters)
		<b>Блоги за запитом: {{$service->filter->filters}}</b>
		<hr>
	@endif	
</header>		

	@foreach ($blogs as $blog)

	<div class="user-content-box mb-3 rel">
        <div class="user-part">
            @include('components.user-item-big',['user' => $blog->user])
            <div class="mb-1">{{ \Carbon\Carbon::parse($blog->created_at)->format('d.m.y H:i') }}</div>			
			<div class="d-flex align-center content-center gap-8">
				<div>
					<img src="{{ asset('svg/comment.svg') }}" class="small-icon">
					<span>{{ $blog->comments_count }}</span>
				</div>
				<div>
					<img src="{{ asset('svg/heart.svg') }}" class="small-icon">
					<span>{{ $blog->likes_count }}</span>
				</div>
			</div>			
        </div>
        <div class="content-part">
			<a href="{{route('blog',$blog->id)}}">
				<div class="subtitle2 mb-2">{{ $blog->name }}</div>            
				<div>{{ $service->TruncateText(strip_tags($blog->content), 300) }}</div>
			</a>
        
        </div>
    </div>

	@endforeach
	{{ count($blogs)==0 ? "Нема блогів за таким запитом":"" }}

	@include('components.paginator', ["paginator" => $service->paginator])

@endsection
	