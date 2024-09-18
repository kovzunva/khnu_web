@extends('layouts.content-maker')

@section('inner_content')

	<div class="header-box">
		<h1>Матеріали для затвердження</h1>
	</div>
		
	<div>
		@foreach ($items as $item)
			<a href="{{ route($item->type.'.editForm', $item->id) }}" class="">
				<div class="light-box row mb-3">
					<div class="col">{{ $item->name }}</div>
					<div class="col-auto"><b>{{ $item->type }}</b> | {{ $item->created_at }}</div>
				</div>
			</a>
		@endforeach
		
		@if (count($items)==0)
			<p>Нема таких (сайт схожий на пустелю, активності нема ніякої).</p>
		@endif
	</div>

@endsection
	