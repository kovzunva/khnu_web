@extends('layouts.app')

@section('aside')
@endsection

@section('content')	

	{{-- Основна інформація --}}
	<section class="with-image-box row mb-3">	
		<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
			<div class="rel with-image-box-imgs">						
				@if ($edition->img)						
					<img src="{{asset($edition->img)}}" alt="Зображення видання">
				@else
					<div class="work-without-img">
						<span>{!! $edition->avtors !!} «{{$edition->name}}»</span>						
					</div>
				@endif
				@if ($can_edit)
					<a href="{{ route('edition.editForm', $edition->id) }}">
						<img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
					</a>
				@endif
			</div>
		</div>				
		<header class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
			<div class="pl-3 pr-3">		
				<h2>{{$edition->name}}</h2>
				<h5>{!! $edition->avtors !!}</h5>
				<p>
					<a href="{{ route('publisher',$edition->publisher_id) }}">{{$edition->publisher}}</a>@if ($edition->language), мова {{$edition->language}}
					@endif<span></span>
					@if ($edition->year)
						, {{$edition->year}}р.
					@endif
					@if ($edition->type_of_cover)
						, обкладинка {{$edition->type_of_cover}}
					@endif
					@if ($edition->type_of_cover)
						, формат обкладинки {{$edition->format}}
					@endif
					@if ($edition->pages)
						, {{$edition->pages}}ст.
					@endif
					@if ($edition->size), тираж {{$edition->size}}
					@endif
				</p>
		
				@if ($edition->isbn)				
					<p>ISBN: {{$edition->isbn}}</p>
				@endif
			</div>
		</header>	
	</section>

	<section class="small-section">
		<header><h5>Опис</h5></header>
		<p>{{ $edition->about }}</p>
	</section>

	<section class="small-section">
		<header>
			<h5>Вміст</h5>
		</header>
			
		@if ($edition->items)					
			<div class="mb-3">
				<ul>						
					@foreach($edition->items as $item)
						<li>
							<a href="{{ route('work',$item->w_id) }}">{{ $item->name }}</a>
							@if ($item->translators)
								(переклад: {!!$item->translators!!})
							@endif
						</li>
					@endforeach
				</ul>
			</div>
		@else
			<p>Ще нема вмісту</p>
		@endif

	</section>

	<section>
		@if ($edition->notes)					
			<div class="mb-3">
				<h5>Примітки</h5>
				{!!$edition->notes!!}
			</div>
		@endif
		
		@if ($edition->links)					
			<div class="mb-3">
				<h5>Посилання</h5>
				<ul>						
					@foreach(explode(PHP_EOL, $edition->links) as $link)
						<li>{!! $link !!}</li>
					@endforeach
				</ul>
			</div>
		@endif
	</section>

	
@endsection


	