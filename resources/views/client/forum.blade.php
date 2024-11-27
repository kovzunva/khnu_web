@extends('layouts.app')


@section('aside')	
	{{-- @if (Auth::user() && $forum->isUserMember(Auth::user()) && !$forum->isAdmin(Auth::user()))			
		<a href="/leave/forum/{{$forum->id}}">
			<button class="transparent-btn mb-3">
				Покинути форум
			</button>
		</a>
	@endif
	<h5>Члени форуму</h5>
	@foreach ($members as $member)
		<div class="mb-1">				
			@include('components.user-item',['user' => $member])
			@if ($forum->isAdmin($member))
				<span class="role">Адмін форуму</span>
			@endif
		</div>
	@endforeach --}}
@endsection


@section('content')	

	<header class="with-image-box row mb-3 rel">	
		<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
			<div class="rel with-image-box-imgs">
				@if ($forum && $img)						
					<img src="{{ asset($img) }}" alt="Зображення форуму">
				@else
					<div class="work-without-img square">
						<span>-</span>						
					</div>
				@endif	
			</div>
		</div>				
		<div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
			<div class="pl-3 pr-3 w-100">								
				<h5>{{ $forum->name }}</h5>
				<hr>
				{{ Str::limit($forum->about, 160) }}
			</div>
		</div>	
		<!-- Кнопка налаштувань -->		
		@if (Auth::user() && !Auth::user()->isBanned() && $forum->isAdmin(Auth::user()))				
			<div class="options-btn">
				<div class="custom-dropdown-btn">
					<img src="{{ asset('svg/elipsis.svg') }}" class="icon">
				</div>
				<div class="custom-dropdown-menu">
					<a class="dropdown-item " href="/forum/{{$forum->id}}/edit">Редагувати</a>
				</div>
			</div>
		@endif
	</header>

	<section>

		@if (Auth::user() && $forum->isUserMember(Auth::user()))	
		
			@if (!Auth::user()->isBanned())	
				<form method="POST" action="/comment/add" class="validate-form">
					@csrf
					<input type="hidden" name="item_id" value="{{ $forum->id }}">
					<input type="hidden" name="item_type" value="2"> {{-- Тип = чат --}}	
	
					<div class="form-group">
						<label for="comment"><h5>Додайте повідомлення:</h5></label>
						<div class="error-text hide" id="error_content">Заповніть поле</div>
						<textarea class="required" id="comment" name="content" rows="4"></textarea>
					</div>
					
					<div class="text-end mt-2">							
						<button type="submit" class="btn base-btn">Опублікувати</button>
					</div>
				</form>
			@else
				<div class="div-error mb-3">
					Вас забанено. Ви не можете писати повідомлення до {{Auth::user()->banned_until}}
				</div>
			@endif	
		@else
			<a href="/join/forum/{{$forum->id}}" class="transparent-btn mb-3 ">
				Приєднатися до форуму
			</a>
		@endif

		<!-- Виведення повідомлень -->
		@if ($forum->comments && count($forum->comments) > 0)			
			@foreach ($forum->comments as $comment)
				<hr>
				@include('components.comment',['type' => 2, 'item_id' => $forum->id])
			@endforeach
		@else				
			Ще нема повідомлень
		@endif
	</section>
	
@endsection


	