@extends('layouts.profile')

@section('page_title', 'Сповіщення')

@section('inner_content')

	<section>

		@if (Auth::user()->unreadNotificationsCount()>1)	
			<a href="{{route('notifications.markAllAsRead')}}" class="transparent-btn  mb-3">Позначити всі прочитаними</a>
		@endif	

		@if ($notifications->count()>0)			
			@foreach ($notifications as $notification)
				<a href="{{ $notification->link }}" class="notification-btn light-box  mb-2
					@if ($notification->read_at) read @endif"
					data-notification-id="{{ $notification->id }}">		
					<div class="row">
						<div class="col-auto align-center pr-2">							
							{!! $notification->getIcon() !!}	
						</div>
						<div class="col">					
							{{ $notification->message }}
						</div>	
						@if ($notification->read_at)
							<div class="col-auto">							
								<span class="ml-auto"><i class="fa-solid fa-check-double"></i></span> 
							</div>	
						@endif
					</div>	
				</a>
			@endforeach
		@else
			<p>Нема сповіщень</p>
		@endif	

	</section>

@endsection
