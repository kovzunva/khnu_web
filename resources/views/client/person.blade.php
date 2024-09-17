@extends('layouts.app')

@section('aside')

@endsection

@section('content')	
	@if (!$person->is_public)
		<div class="div-info mb-3">
			Цей матеріал непублічний і видимий лише для обраних.
		</div>
	@endif

	{{-- Основна інформація --}}
	<section class="with-image-box row mb-3">	
		<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
			<div class="rel with-image-box-imgs">						
				@if ($person->img)						
					<img src="{{asset($person->img)}}" alt="Зображення персони">
				@else
					<div class="work-without-img square">
						<span>{{$person->name}}</span>						
					</div>
				@endif
				@if ($can_edit)
					<a href="{{ route('person.editForm', $person->id) }}">
						<img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
					</a>
				@endif
			</div>
		</div>				
		<header class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
			<div class="pl-3 pr-3">		
				<h1>{{$person->name}}</h1>

				@if ($person->alt_names)
					<p>{{$person->alt_names}}</p>
				@endif
				
				{{-- Тип персони --}}
				<p class="bold-500">
					@if ($person->is_avtor)
						#автор
					@endif
					@if ($person->is_translator)
						#перекладач
					@endif
					@if ($person->is_illustrator)
						#ілюстратор
					@endif
					@if ($person->is_designer)
						#дизайнер
					@endif
				</p>

				@if ($person->birthdate || $person->deathdate)
					<p>
						(
						@if ($person->birthdate)
							{{ $person->birthdate }}
						@else
							...
						@endif		
						@if ($person->deathdate)
						- {{ $person->deathdate }}
						@else
							...
						@endif
						)
					</p>
				@endif		
			</div>
		</header>	
	</section>

	{{-- Біографія --}}
	<section>
		@if ($person->bio && $person->bio!='<br>')				
			<h5>Біографія</h5>
			<div class="text-from-editor classic-links">
				{!!$person->bio!!}
			</div>
		@endif
	</section>

	{{-- Твори --}}
	@if ($person->works)
		<section class="small-section">
			<header>
				<h5>Твори</h5>
			</header>				
			<div>
				@foreach($person->works as $work)
					<a href="{{ route('work',$work->id) }}" class="light-box mb-2 bold-500">
						{{ $work->name }}
					</a>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Внесок як перекладача --}}
	@if ($person->translated)
		<section class="small-section">
			<header>
				<h5>Внесок як перекладача</h5>
			</header>
				
			<div>					
				@foreach($person->translated as $item)
					<div class="light-box mb-2 row">
						<div class="col">
							<a href="{{ route('work',$item->id) }}" class=" bold-500">
								{{ $item->avtors }} «{{ $item->name }}»
							</a>
						</div>
						<div class="col-auto">							
							<a href="{{ route('work',$item->id) }}" class="">
								{{ $item->publisher }}
								@if ($item->year)									
									, {{ $item->year }}р.
								@endif
							</a>
						</div>
						
					</div>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Внесок як ілюстратора --}}
	@if ($person->illustrated)
		<section class="small-section">
			<header>
				<h5>Внесок як ілюстратора</h5>
			</header>
				
			<div class="images-box">					
				@foreach($person->illustrated as $item)
					<div>
						<a href="{{ route('edition',$item->id) }}">							
							<img src="{{ asset($item->img) }}">
							@if ($item->year)
								<div>{{ $item->year }}р.</div>
							@endif
						</a>					
					</div>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Внесок як дизайнера --}}
	@if ($person->designed)
		<section class="small-section">
			<header>
				<h5>Внесок як дизайнера</h5>
			</header>
				
			<div class="images-box">					
				@foreach($person->designed as $item)
					<div>
						<a href="{{ route('edition',$item->id) }}">							
							<img src="{{ asset($item->img) }}">
							@if ($item->year)
								<div>{{ $item->year }}р.</div>
							@endif
						</a>					
					</div>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Примітки --}}
	<section>
		@if ($person->notes)					
			<div class="mb-3">
				<h5>Примітки</h5>
				{!!$person->notes!!}
			</div>
		@endif
		
		@if ($person->links)					
			<div class="mb-3">
				<h5>Посилання</h5>
				<ul>						
					@foreach(explode(PHP_EOL, $person->links) as $link)
						<li>{!! $link !!}</li>
					@endforeach
				</ul>
			</div>
		@endif
	</section>

	
@endsection


	