@extends('layouts.admin')

@section('inner_content')

	<div class="header-box row">
		<div class="col">
			<h1>Заявка від {{ $user_request->created_at}}</h1>
		</div>
		<div class="col-auto">
			@include('components.user-item',['user' => $user_request->user])
		</div>
	</div>

	<section class="small-section">
		<header><h5>Тема заявки</h5></header>
		{{ $user_request->name}}
	</section>

	<section class="small-section">
		<header><h5>Текст заявки</h5></header>
		{{ $user_request->text}}
	</section>
	
	@if ($user_request->status==null)
		<section class="small-section">
			<header><h5>Оброблення заявки</h5></header>
			<form action="{{ route('user-request.process', $user_request->id) }}" method="post">
				@csrf
				<textarea name="responce" placeholder="Відповідь адміністратора"></textarea>
				<div class="text-end">							
					<button class="base-btn">Обробити</button>
				</div>
			</form>
		</section>

		<section class="small-section">
			<header><h5>Відхилення заявки</h5></header>
			<div class="text-end">
				<form action="{{ route('user-request.reject', $user_request->id) }}" method="post" class="inline">
					@csrf
					<textarea name="responce" placeholder="Причина відхилення"></textarea>
					<button class="confirm-link" data-message="Ви впевнені, що хочете відхилити заявку?">Відхилити</button>
				</form>
			</div>
		</section>

	@else
		<section class="small-section">
			<header><h5>Статус</h5></header>
			<b>{{ $user_request->status==1? 'Оброблено':'Відхилено'}} {{ $user_request->processed_at}}</b>
		</section>
		<section class="small-section">
			<header><h5>Адміністратор</h5></header>
			@include('components.user-item',['user' => $user_request->user_processed])
		</section>
		
		@if ($user_request->status==1)
			<section class="small-section">
				<header><h5>Відповідь адміністратора</h5></header>
				{{ $user_request->responce }}
			</section>
		@endif
	@endif
	
	

@endsection
	