@extends('layouts.content-maker')

@section('inner_content')

	<div class="header-box">
		<h1>Об'єднання дублю</h1>
	</div>

	<section>			
		<form action="{{ route('double.unite') }}" method="post" class="validate-form">
			@csrf
			<div class="mb-3">
				<label>Тип</label>
				<div class="base-select">
					<div class="select-box">
						<span class="selected-option d-flex align-items-center">Персона</span>
						<ul class="options hide">
							<li data-value="person">Персона</li>
							<li data-value="work">Твір</li>
							<li data-value="edition">Видання</li>
							<li data-value="publisher">Видавництво</li>
						</ul>
					</div>
					<input type="hidden" name="type" id="double_type" value="person">
				</div>                      
			</div>
			<div class="mb-3 insert-group">
				<label>Основний об'єкт (лишається)</label>
				<input type="text" class="double-search">
				<div class="mt-1 insert-container no-empty" data-name="main_id"></div>
			</div>
			<div class="mb-3 insert-group">
				<label>Дубльований об'єкт (видаляється)</label>
				<input type="text" class="double-search">
				<div class="mt-1 insert-container no-empty" data-name="id" data-no-empty="Додайте дубльований об'єкт"></div>
			</div>
			<div class="text-end">				
				<button class="base-btn confirm-link" data-message="Ви впевнені, що хочете об'єднати дубль?">Об'єднати дубль</button>
			</div>
		</form>
	</section>
		
	<section class="small-section">
		<header>
			<h5>Потенційні дублі</h5>
		</header>

		@foreach ($doubles as $item)
			<div class="light-box row mb-3">
				<div class="col d-flex align-items-center">
					@if (Route::has($item->type))
						<a href="{{ route($item->type, $item->id1) }}">{{ $item->name1 }}</a>
					@else
						{{ $item->name1 }}
					@endif
					<span class="ml-1 mr-1">/</span>
					@if (Route::has($item->type))
						<a href="{{ route($item->type, $item->id2) }}">{{ $item->name2 }}</a>
					@else
						{{ $item->name2 }}
					@endif
				</div>
				<div class="col-auto">
					<b class="mr-2">{{ $item->type }}</b>
					<form action="{{ route('double.add-not-a-double') }}" method="post" class="inline">
						@csrf
						<input type="hidden" name="id1" value="{{ $item->id1 }}">
						<input type="hidden" name="id2" value="{{ $item->id2 }}">
						<input type="hidden" name="type" value="{{ $item->type }}">
						<button title='Додати виняток "не дубль"'>x</button>
					</form>
				</div>
			</div>
		@endforeach
		
		@if (count($doubles)==0)
			<p>Не знайдено потенційних дублів.</p>
		@endif
	</section>

@endsection
	