@extends('layouts.app')

@section('aside')	  
@endsection

@section('content')	
	@if (!$work->is_public)
		<div class="div-info mb-3">
			Цей матеріал непублічний і видимий лише для обраних.
		</div>
	@endif

	{{-- Основна інформація --}}
	<section class="with-image-box row mb-3">	
		<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
			<div class="rel with-image-box-imgs">
				@if ($work->img)						
					<img src="{{asset($work->img)}}" alt="Зображення видання">
				@else
					<div class="work-without-img">
						<span>{!! $work->avtors !!} «{{$work->name}}»</span>						
					</div>
				@endif
				@if ($can_edit)
					<a href="{{ route('work.editForm', $work->id) }}">
						<img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
					</a>
				@endif
			</div>
		</div>				
		<header class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center rel">	
			<div class="pl-3 pr-3 w-100">	
				<div class="row">
					<div class="col">
						<h1>{{$work->name}}</h1>						
					</div>	
					{{-- Поличка --}}
					<div class="col-auto">
						@if (Auth::user())
							<div class="open-select open-select-btn shelves-btn">
								<div class="select-box">
									<span class="open-select-title d-flex align-items-center light-tippy" title="Книжкові полички">
										@if ($work->shelves)
											<img src="{{ asset("svg/shelves.svg") }}" class="icon small-icon hover-scale">
										@else										
											<img src="{{ asset("svg/shelves-empty.svg") }}" class="icon small-icon hover-scale">
										@endif
									</span>
									<ul class="options hide">
									<li>
										<form action="{{ route('work-to-shelf',$work->id) }}" method="post">	
											@csrf										
											<div class="input-group">
												<input type="text" class="input-with-btt" name="name" placeholder="Додайте нову полицю">
												<button class="btt-with-input">+</button>
											</div>
										</form>
										<hr>
									</li>
									@foreach ($shelves as $shelf)
										<li class="mb-1">
											<form action="{{ route('work-to-shelf',$work->id) }}" method="post">	
												@csrf										
												<div class="input-group">
													<input type="hidden" name="sh_id" value="{{ $shelf->id }}">
													<input type="text" class="input-with-btt {{ $shelf->is_added? 'accent-input':'' }}" 
													value="{{ $shelf->name }}" readonly>
													<button class="btt-with-input">
														{{ $shelf->is_added? '-':'+' }}
													</button>
												</div>
											</form>										
										</li>
									@endforeach
									</ul>
								</div>
							</div>   
						@endif
					</div>	
				</div>	
				<div class="mb-2"><b>{!! $work->avtors !!}</b></div>				
				<div class="mb-1">Середній рейтинг: <span id="average_rating">{{ number_format($work->average_rating, 2, '.', '') }}</span></div>

				@if (Auth::user())
					{{-- Оцінка --}}
					<div class="rating-container to-rate-container mb-2 @if (isset($work->rating)) has-rating @endif" data-work-id="{{$work->id}}"
						@if (isset($work->rating)) data-rating="{{$work->rating}}"  @endif>
						<div class="d-flex ">
							@include('components.rating-clickable')
							<div class="@if (!isset($work->rating)) hide @endif" id="btn-cancel-rate-group">
								<img src="{{ asset("svg/cancel.svg") }}" class="icon op-icon pointer light-tippy" id="btn-cancel-rate" title="Скасувати оцінку">													
							</div>							
						</div>					
					</div>

					{{-- Дата прочитання --}}
					<div class="active-group d-flex gap-5 ">
						<input type="text" name="date" class="input-date anti-base bottom-border-input w-100" placeholder="дд.мм.рррр" id="input-date-read" value="{{ $work->date_read }}">
						<img src="{{ asset("svg/confirm.svg") }}" class="icon small-icon pointer hover-scale light-tippy" id="btn-date-read" data-work-id="{{$work->id}}" title="Встановити дату прочитання">
						<img src="{{ asset("svg/cancel.svg") }}" class="icon op-icon pointer mt-minus-5 @if (!isset($work->date_read)) hide @endif light-tippy" id="btn-cancel-date-read" data-work-id="{{$work->id}}" title="Скасувати дату прочитання">
					</div>		
					 
				@endif
			</div>
		</header>	
	</section>

	{{-- Загальна інформація --}}
	@if ($work->alt_names || $work->genre || $work->language || $work->year)			
		<section class="small-section">
			<header><h5>Загальна інформація</h5></header>
			
			@if ($work->alt_names)
				<p>Альтернативні назви: {{$work->alt_names}}</p>
			@endif

			@if ($work->genre)
				<p>Жанр: {{ $work->genre }}</p>
			@endif

			@if ($work->language)
				<p>Мова написання: {{ $work->language }}</p>
			@endif

			@if ($work->year)
				<p>Рік написання: {{$work->year}}</p>
			@endif

		</section>
	@endif

	{{-- Анотація --}}
	@if ($work->anotations)
		<section class="small-section">
			@if (count($work->anotations)==1)
				<header><h5>Анотація</h5></header>
				<p>{{ $work->anotations[0]->text }}</p>
			@else
				<header><h5>Анотації</h5></header>
				@foreach ($work->anotations as $anotation)
					<hr>
					{{$anotation->text}}					
				@endforeach
			@endif
		</section>
	@endif

	{{-- Класифікатор --}}	
	<section class="small-section">
		<header class="row">
			<div class="col-auto">
				<h5 class="has-info" title="Характеристики, за якими класифіковано твір (відсотки => скільки користувачів виставили опцію)">Класифікатор</h5>
			</div>
			<div class="col"></div>
			@if (Auth::user())
				<div class="col-auto">
					<button class="base-btn small-btn show-btn" data-show-id="form-classificate" title="Класифікувати твір">+</button>
				</div>
			@endif
		</header>
		@if (isset($work->classificator_groups))
			<ul>					
				@foreach ($work->classificator_groups as $group)
				<li class="">
					<b>{{$group->name}}</b>: {{$group->options}}
				</li>
				@endforeach
			</ul>
		@else
			<p>Твір ще ніхто не класифікував.</p>
		@endif
		
		{{-- Класифікувати --}}
		@if (Auth::user())
			<form method="POST" action="{{ route('work.classificate', $work->id) }}" id="form-classificate" class="validate-form hide">
				@csrf

				{{-- Групи класифікації --}}
				@foreach ($service->classificator_groups as $group_name => $group)
					<b>{{$group_name}}</b>
					<ul>
					
					{{-- Опції --}}
					@foreach ($group->options as $option)
					<li class="option-list"
					{{$option->change_id? 'data-change-id='.$option->change_id.'':''}}
					{{$option->change? 'data-change='.$option->id.'':''}}>								
						<input type="{{$option->radio? 'radio':'checkbox'}}" 
						name="opt{{$option->radio? '_radio'.$group_name:$option->id}}"
						id="opt{{$option->id}}"
						data-group="{{$group_name}}" value="{{$option->id}}"
						{{$option->is_selected? 'checked':''}}>	
						<label for="opt{{$option->id}}">{{$option->name}}</label>
	
						{{-- Підопції --}}
						@if (isset($option->suboptions))
							<ul>
							@foreach ($option->suboptions as $option)
							<li class="option-list"
							{{$option->change_id? 'data-change-id='.$option->change_id.'':''}}
							{{$option->change? 'data-change='.$option->id.'"':''}}>												
								<input type="{{$option->radio? 'radio':'checkbox'}}" 
								name="opt{{$option->id}}" id="opt{{$option->id}}"
								data-group="{{$group_name}}" value="{{$option->id}}"
								{{$option->is_selected? 'checked':''}}>
								<label for="opt{{$option->id}}">{{$option->name}}</label>
							</li>
							@endforeach
							</ul>
						@endif
					</li>
					@endforeach
					</ul>
				@endforeach

				<div class="text-end">
					<button type="button" class="hide-btn" data-hide-id="form-classificate">Згорнути</button>
					<button class="base-btn">Класифікувати</button>
				</div>
			</form>
		@endif
	</section>

	{{-- Примітки і посилання  --}}
	<section>
		@if ($work->notes)					
			<div class="mb-3">
				<h5>Примітки</h5>
				{!!$work->notes!!}
			</div>
		@endif
		
		@if ($work->links)					
			<div class="mb-3">
				<h5>Посилання</h5>
				<ul>						
					@foreach(explode(PHP_EOL, $work->links) as $link)
						<li>{!! $link !!}</li>
					@endforeach
				</ul>
			</div>
		@endif
	</section>
	
	{{-- Нижня навігація --}}
	<section class="choice-section scroll-to" id="work_tabs">
		<header class="choice-header">
			<button class="show-content-btn change-link {{ $tab=='reviews'? 'selected-btn':'' }}" data-target="reviews" data-change-link="tab=reviews">Відгуки</button>
			<button class="show-content-btn change-link {{ $tab=='quotes'? 'selected-btn':'' }}" data-target="quotes" data-change-link="tab=quotes">Цитати</button>
			<button class="show-content-btn change-link {{ $tab=='editions'? 'selected-btn':'' }}" data-target="editions" data-change-link="tab=editions">Видання</button>
			{{-- <button class="show-content-btn" id="show_similars">Схожі твори</button> --}}
		</header>

		{{-- Відгуки --}}
		<div class="to-show-content {{ $tab=='reviews'? '':'hide' }}" id="reviews">

			@if (Auth::user() && Auth::user()->isBanned())
				<div class="div-error mb-3">
					Вас забанено. Ви не можете створювати публічні відгуки до {{Auth::user()->banned_until}}
				</div>
			@endif

			{{-- Додати відгук --}}
			@if (Auth()->user())
				<form method="POST" action="{{ route('review.add') }}" class="validate-form active-group">
					@csrf
					<input type="hidden" name="w_id" value="{{ $work->id }}">

					<div class="form-group mb-2">
						<div class="error-text hide" id="error_text">Заповніть поле</div>
						<textarea class="required resizeble" name="text" rows="2" data-start-rows="2" data-focus-rows="12" placeholder="Напишіть відгук... (не хочете - не пишіть)"></textarea>
					</div>

					<div class="row">
						<div class="col">
							<button type="button" class="round-icon-btn spoiler-add-btn" title="Вставити спойлер">
								<img src="{{ asset('/svg/spoiler.svg') }}" class="icon" alt="Вставити спойлер">
							</button>
						</div>
						<div class="col-auto">	
							@if (Auth::user()->isBanned())
								<span  class="ms-1 me-1">Не публічно</span>
							@else							
								<input type="checkbox" name="is_public" id="is_public" checked>
								<label for="is_public">Публічно</label>	
							@endif

							<button type="submit" class="btn base-btn">Додати відгук</button>
						</div>
					</div>					
				</form>
			@endif

			{{-- Відгуки --}}
			@if ($work->reviews)
				<section class="small-section">
					@foreach ($work->reviews as $review)
						<div class="user-content-box mb-3 rel" data-review-id="{{$review->id}}" id="review_{{ $review->id }}">
							<div class="user-part">
								@include('components.user-item-big',['user' => $review->user])

								{{-- Оцінка --}}
								@if ($review->rating)										
									<div class="w-125 mt-1 mb-2">
										@include('components.rating', ['rating' => $review->rating])						
									</div>
								@endif

								<div class="mb-1">{{ \Carbon\Carbon::parse($review->created_at)->format('d.m.y H:i') }}</div>
								<div>{{$review->is_public? '':'(чорновик)'}}</div>								
							</div>
							<div class="content-part">
								<div class="with-spoilers" id="review_text_{{ $review->id }}">
									@if (Str::length($review->text) > 500)
										<div class="truncated-text">
											<div class="short-text">
												{{ $service->TruncateText($review->text, 500) }}
												<a href="#" class="show-more bold italic">розгорнути</a>
											</div>
											<div class="full-text hide">
												{!! nl2br(e($review->text)) !!}
												<a href="#" class="show-less bold italic">згорнути</a>
											</div>
										</div>
									@else
										<div>{!! nl2br(e($review->text)) !!}</div>										
									@endif
								</div>

								{{-- Редагування --}}	
								@if (auth()->check() && $review->user_id==auth()->user()->id) 
									<form class="hide mt-2 w-100 active-group" action="{{ route('review.edit', $review->id) }}" method="POST" id="review_edit_form_{{ $review->id }}">
										@csrf
										<input type="hidden" name="w_id" value="{{$work->id}}">

										<div class="error-text hide" id="error_text">Заповніть поле</div>
										<textarea class="required" name="text" rows="12">{{ $review->text }}</textarea>

										<div class="row mt-2">
											<div class="col">												
												<button type="button" class="round-icon-btn spoiler-add-btn" title="Вставити спойлер">
													<img src="{{ asset('/svg/spoiler.svg') }}" class="icon" alt="Вставити спойлер">
												</button>
											</div>
											<div class="col-auto d-flex gap-1 align-center">
												@if (Auth::user()->isBanned())
													<span  class="ms-1 me-1">Не публічно</span>
												@else	
													<div>				
														<input  name="is_public" id="is_public{{$review->id}}" {{$review->is_public? 'checked type=hidden' : 'type=checkbox' }} >
														<label for="is_public{{$review->id}}">Публічно</label>	
													</div>
												@endif
												<div>
													<button type="button" class="hide-btn show-btn" data-hide-id="review_edit_form_{{ $review->id }}" data-show-id="review_text_{{ $review->id }}">Скасувати</button>
													<button type="submit" name="submit" class="btn base-btn">Зберегти</button>
												</div>
											</div>
										</div>
									</form>
								@endif
							</div>
								
							<!-- Кнопка налаштувань -->
							@if (auth()->check() && $review->user_id==auth()->user()->id)															
								<div class="options-btn">
									<div class="custom-dropdown-btn">
										<img src="{{asset('/svg/elipsis.svg')}}" class="icon">
									</div>
									<div class="custom-dropdown-menu">
										<a class="dropdown-item show-btn hide-btn" data-hide-id="review_text_{{ $review->id }}" data-show-id="review_edit_form_{{ $review->id }}">Редагувати</a>
										<a class="dropdown-item confirm-link" href="{{ route('review.del', $review->id) }}" data-message="Ви впевнені, що хочете видалити цей відгук?">Видалити</a>
									</div>
								</div>	
							<!-- Кнопка налаштувань для модератора -->	
							@elseif (auth()->check() && auth()->user()->hasPermission('moderate'))												
								<div class="options-btn">
									<div class="custom-dropdown-btn">
										<img src="{{asset('/svg/elipsis.svg')}}" class="icon">
									</div>
									<div class="custom-dropdown-menu">
										<a class="dropdown-item input-link" href="{{ route('review.del', $review->id) }}" data-message="Введіть причину видалення:">Видалити</a>
									</div>
								</div>
							@endif	
						</div>
					@endforeach
					
					@include('components.paginator', ["paginator" => $work->reviewService->paginator])
				</section>			
			@endif
			
			@if (!$work->reviews)
				<p>Нема відгуків.</p>
			@endif

		</div>

		{{-- Цитати --}}
		<div class="to-show-content {{ $tab=='quotes'? '':'hide' }}" id="quotes">		
			@if (Auth::user() && Auth::user()->isBanned())
				<div class="div-error mb-3">
					Вас забанено. Ви не можете додавати цитати до {{Auth::user()->banned_until}}
				</div>
			@endif

			{{-- Додати цитату --}}
			@if (Auth()->user() && !Auth::user()->isBanned())	
				<form method="POST" action="{{ route('quote.add') }}" class="validate-form mt-1">
					@csrf
					<input type="hidden" name="w_id" value="{{ $work->id }}">

					<div class="form-group mb-3">
						<div class="error-text hide" id="error_text">Заповніть поле</div>
						<textarea class="required resizeble" name="text" rows="2" data-start-rows="2" data-focus-rows="5" maxlength="500" placeholder="Наклацайте цитату..."></textarea>
					</div>
					
					<div class="text-end">
						<button type="submit" class="btn base-btn">Додати цитату</button>
					</div>
				</form>
			@endif

			{{-- Цитати --}}
			@if ($work->quotes)
				<section class="small-section">
					@foreach ($work->quotes as $quote)
						<div class="user-content-box mb-3 rel" data-quote-id="{{$quote->id}}" id="quote_{{ $quote->id }}">
							<div class="user-part">
								@include('components.user-item-big',['user' => $quote->user])

								<div class="mb-1">{{ \Carbon\Carbon::parse($quote->created_at)->format('d.m.y H:i') }}</div>							
							</div>
							<div class="content-part">
								<div class="with-spoilers" id="quote_text_{{ $quote->id }}">
									{!! nl2br(e($quote->text)) !!}
								</div>

								{{-- редагування --}}	
								@if (auth()->check() && $quote->user_id==auth()->user()->id)
									<form class="hide mt-2 w-100" action="{{ route('quote.edit', $quote->id) }}" method="POST" id="quote_edit_form_{{ $quote->id }}">
										@csrf
										<input type="hidden" name="w_id" value="{{$work->id}}">
										<div class="error-text hide" id="error_text">Заповніть поле</div>
										<textarea class="required" name="text" rows="5" maxlength="500">{{ $quote->text }}</textarea>				
										<div class="text-end mt-2">			
											<button type="button" class="hide-btn show-btn" data-hide-id="quote_edit_form_{{ $quote->id }}" data-show-id="quote_text_{{ $quote->id }}">Скасувати</button>
											<button type="submit" name="submit" class="btn base-btn">Зберегти</button>
										</div>
									</form>
								@endif
							</div>
								
							<!-- Кнопка налаштувань -->
							@if (auth()->check() && $quote->user_id==auth()->user()->id)															
								<div class="options-btn">
									<div class="custom-dropdown-btn">
										<img src="{{asset('/svg/elipsis.svg')}}" class="icon">
									</div>
									<div class="custom-dropdown-menu">
										<a class="dropdown-item show-btn hide-btn" data-hide-id="quote_text_{{ $quote->id }}" data-show-id="quote_edit_form_{{ $quote->id }}">Редагувати</a>
										<a class="dropdown-item confirm-link" href="{{ route('quote.del', $quote->id) }}" data-message="Ви впевнені, що хочете видалити цю цитату?">Видалити</a>
									</div>
								</div>	
							<!-- Кнопка налаштувань для модератора -->	
							@elseif (auth()->check() && auth()->user()->hasPermission('moderate'))												
								<div class="options-btn">
									<div class="custom-dropdown-btn">
										<img src="{{asset('/svg/elipsis.svg')}}" class="icon">
									</div>
									<div class="custom-dropdown-menu">
										<a class="dropdown-item input-link" href="{{ route('quote.del', $quote->id) }}" data-message="Введіть причину видалення:">Видалити</a>
									</div>
								</div>	
							@endif	
						</div>
					@endforeach
					
					@include('components.paginator', ["paginator" => $work->quoteService->paginator])
				</section>			
			@endif
			
			@if (!$work->quotes)
				<p>Нема цитат.</p>
			@endif
		</div>

		{{-- Видання --}}
		<div class="to-show-content {{ $tab=='editions'? '':'hide' }}" id="editions">

			@if ($work->editions)				
				<div class="images-box">					
					@foreach($work->editions as $edition)
						<div>
							<a href="{{ route('edition',$edition->id) }}">
								<img src="{{ asset($edition->img) }}" alt="{{ $edition->name }}" class="accent-img">
								@if ($edition->year)
									<div class="mt-1">{{ $edition->year }} р.</div>
								@endif
							</a>
						</div>
					@endforeach
				</div>
			@else
				Нема видань.
			@endif
		</div>

		{{-- Схожі твори --}}
		{{-- <div class="to-show-content" id="show_similars_content">
			
			@if (Auth()->user())
				<form method="POST" action="{{ route('similar.add') }}" class="validate-form mt-1 active-group">
					@csrf
					<input type="hidden" name="w_id" value="{{ $work->id }}">

					<div class="mb-3">
						<label for="w_alt_names" class="form-label">Твір</label>
						<input type="text" id="publisher_edition">
							
						<div id="publisher_edition_container" class="mt-3 no-empty">
							@if ($edition)
								<input readonly type="text" value="{{ $edition->publisher }}">
								<input type="hidden" name="publisher_id" value="{{ $edition->publisher_id }}">
							@endif
						</div>
					</div>

					<div class="form-group mb-2">
						<label for="">Чим саме подібний твір</label>
						<textarea class="required" name="text" rows="2"></textarea>
					</div>

					<div class="text-end">
						<button type="submit" class="btn base-btn">Додати відгук</button>
					</div>
					
				</form>
			@endif

			@foreach($work->similars as $similar)
				<a href="{{ route('work',$similar->id) }}" class="">
					<div class="with-image-box row mb-3">	
						<div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
							<div class="rel with-image-box-imgs">
								@if ($similar->img)						
									<img src="{{asset($similar->img)}}" alt="Зображення видання">
								@else
									<div class="work-without-img">
										<span>{!! $similar->avtors !!} «{{$similar->name}}»</span>						
									</div>
								@endif	
							</div>
						</div>				
						<div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
							<div class="pl-3 pr-3">								
								<h5>{{ $similar->avtors }} «{{ $similar->name }}»</h5>
								{{ Str::limit($similar->anotation, 160) }}
								<div class="mt-1 mb-1">
									<b>{{ $similar->options_info }}</b>
								</div>
								<div class="rating-container"
									@if (isset($similar->rating)) data-rating="{{$work->rating}}"  @endif>
									Рейтинг: {{isset($work->rating)? number_format($work->rating, 2, '.', ''):'0'}}
								</div>
							</div>
						</div>	
					</div>
				</a>
			@endforeach
			{{ count($work->similars)==0 ? "Ще не додано схожих творів.":"" }}
		</div> --}}

	</section>
	
@endsection	