@extends('layouts.profile')

@section('page_title', $title)

@section('inner_content')
			
	@foreach ($ratings as $rating)
		<a href="{{ route('work', $rating->w_id) }}" class="light-box mb-3  row">
			<div class="col">
				{{ $rating->w_avtors }} «{{ $rating->w_name }}»
			</div>
			<div class="col-auto">
				<b>Оцінка: {{ $rating->value }}</b>
			</div>
		</a>
	@endforeach

@endsection
