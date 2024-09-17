@extends('layouts.app')

@section('aside')

@endsection

@section('content')	
	@if (!$publisher->is_public)
		<div class="div-info mb-3">
			Цей матеріал непублічний і видимий лише для обраних.
		</div>
	@endif

	{{-- Основна інформація --}}
	<section class="with-image-box row mb-3">	
		<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
			<div class="rel with-image-box-imgs">						
				{{-- @if ($publisher->img)						
					<img src="{{asset($publisher->img)}}" alt="Логотип видавництва">
				@else --}}
					<div class="work-without-img square">
						<span>{{$publisher->name}}</span>						
					</div>
				{{-- @endif --}}
				@if ($can_edit)
					<a href="{{ route('publisher.editForm', $publisher->id) }}">
						<img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
					</a>
				@endif
			</div>
		</div>				
		<header class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
			<div class="pl-3 pr-3">		
				<h2>{{$publisher->name}}

				@if ($publisher->country_name), {{$publisher->country_name}}
				@endif<span></span>
				@if ($publisher->city)
					, м. {{$publisher->city}}
				@endif
				@if ($publisher->year)
					, {{$publisher->year}}р.
				@endif
			</div>
		</header>	
	</section>

	<section class="small-section">
		@if ($publisher->about && $publisher->about!='<br>')				
			<h5>Про видавництво</h5>
			<p>
				{{ $publisher->about }}
			</p>
		@endif
	</section>

	{{-- Видання --}}
	<section class="small-section">
		<header>
			<h5>Видання</h5>
		</header>
		@if ($publisher->editions)
				
			<div class="images-box">						
				@foreach($publisher->editions as $edition)
					<div>
						<a href="{{ route('edition',$edition->id) }}">
							<img src="{{ asset($edition->img) }}">
							@if ($edition->year)
								<div class="mt-1">{{ $edition->year }} р.</div>
							@endif
						</a>
					</div>
				@endforeach
			</div>
		@else
			Нема видань.
		@endif
	</section>

	<section>
		@if ($publisher->notes)					
			<div class="mb-3">
				<h5>Примітки</h5>
				{!!$publisher->notes!!}
			</div>
		@endif
		
		@if ($publisher->links)					
			<div class="mb-3">
				<h5>Посилання</h5>
				<ul>						
					@foreach(explode(PHP_EOL, $publisher->links) as $link)
						<li>{!! $link !!}</li>
					@endforeach
				</ul>
			</div>
		@endif
	</section>

	
@endsection


	