@extends('layouts.admin')

@section('inner_content')	

	<div class="header-box row align-center">
		<h1 class="col">Довідкові статті</h1>
		<div class="col-auto">
			<a href="{{ route('faq.form') }}" class="btn-with-icon">
				<img src="{{ asset('svg/plus.svg') }}" alt="">
				Додати довідкову статтю
			</a>
		</div>
	</div>
		
	<div>

		@foreach ($items as $item)
			<a href="{{ route('faq.edit',$item->id) }}" class="">
				<div class="light-box mb-3">
					{{ $item->name }}
				</div>
			</a>
		@endforeach
		
		@if (count($items)==0)
			<p>Нуль, чисто, безодня, порожнеча.</p>
		@endif
	</div>	

@endsection
	