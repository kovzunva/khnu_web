@extends('layouts.app')

@section('content')

	<section class="small-section">
		<header><h5>Заявка від {{ $user_request->created_at}}</h5></header>
	</section>

	<section class="small-section">
		<header><h5>Тема заявки</h5></header>
		{{ $user_request->name}}
	</section>

	<section class="small-section">
		<header><h5>Текст заявки</h5></header>
		{{ $user_request->text}}
	</section>
	
	<section class="small-section">
		<header><h5>Статус</h5></header>
		<b>
			@if ($user_request->status==null)
				Очікує на обробку
			@else
				{{ $user_request->status==1? 'Оброблено':'Відхилено'}} {{ $user_request->processed_at}}
			@endif			
		</b>
	</section>

	@if ($user_request->status!=null)
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
	