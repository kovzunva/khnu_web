@extends('layouts.app')

@section('content')	

	<div class="header-box">
		<h1>Довідкові статті</h1>
	</div>
		
	<div>

		@foreach ($items as $item)
			<a href="{{ route('faq',$item->id) }}" class="">
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
	