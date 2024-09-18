@extends('layouts.admin')

@section('inner_content')

	<div class="header-box">
		<h1>Необроблені заявки</h1>
	</div>
		
	<div>
		@foreach ($unprocessed_requests as $item)
			<a href="{{ route('user-request.show', $item->id) }}" class="">
				<div class="light-box row mb-3">
					<div class="col">
						{{ $item->name }}
					</div>
					<div class="col-auto">
						{{ $item->created_at }}
					</div>
				</div>
			</a>
		@endforeach
		
		@if (count($unprocessed_requests)==0)
			<p>Немає таких.</p>
		@endif
	</div>

	<details class="mt-4">
		<summary><b>Оброблені заявки</b></summary>
		<div class="mt-2">
			@foreach ($processed_requests as $item)
				<a href="{{ route('user-request.show', $item->id) }}" class="">
					<div class="light-box row mb-3">
						<div class="col">
							{{ $item->name }}
						</div>
						<div class="col-auto">
							{{ $item->created_at }} / оброблено {{ $item->processed_at }}
						</div>
					</div>
				</a>
			@endforeach
			@if (count($processed_requests)==0)
				Немає таких.
			@endif
		</div>
	</details>

	<details class="mt-2">
		<summary><b>Відхилені заявки</b></summary>
		<div class="mt-2">
			@foreach ($rejected_requests as $item)
				<a href="{{ route('user-request.show', $item->id) }}" class="">
					<div class="light-box row mb-3">
						<div class="col">
							{{ $item->name }}
						</div>
						<div class="col-auto">
							{{ $item->created_at }} / відхилено {{ $item->processed_at }}
						</div>
					</div>
				</a>
			@endforeach
			@if (count($rejected_requests)==0)
				Немає таких.
			@endif
		</div>
	</details>
	

@endsection
	