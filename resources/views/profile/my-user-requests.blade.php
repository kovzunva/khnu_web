@extends('layouts.profile')

@section('page_title', 'Заявки')

@section('inner_content')
	<a href="{{ route('user-request.form') }}" class="transparent-btn mb-3 ">
		Форма зворотного зв'язку
	</a>
		
	@foreach ($user_requests as $item)
			<a href="{{ route('user-request.show-status', $item->id) }}" class="">
				<div class="light-box mb-3">
					<div class="row">							
						<div class="col">
							<b>{{ $item->name }}</b>
						</div>
						<div class="col-auto">
							{{ $item->created_at }}
						</div>
					</div>
					<hr>
					<div class="row">		
						<div class="col">								
							@if ($item->status==null)
								Очікує на обробку
							@else
								@if ($item->status==1)
									Оброблено
								@else
									Відхилено
								@endif
							@endif	
						</div>				
						@if ($item->status!=null)
							<div class="col-auto">
								{{ $item->processed_at }}
							</div>
						@endif
					</div>
				</div>
			</a>
		@endforeach

@endsection
