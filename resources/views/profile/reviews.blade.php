@extends('layouts.profile')

@section('page_title', 'Відгуки')

@section('inner_content')

    @if ($profile==Auth::user() && Auth::user()->isBanned())
        <div class="div-error mb-3">
            Вас забанено. Ви не можете створювати публічні відгуки до {{Auth::user()->banned_until}}
        </div>
    @endif

    @foreach ($reviews as $review)
        <div class="user-content-box mb-3 rel" data-review-id="{{$review->id}}" id="review_{{ $review->id }}">
            <div class="user-part">
                <a href="{{ route('work',$review->w_id) }}" class="">	
                    <img src="{{ asset($review->w_img) }}" alt="{{ $review->w_name }}" class="rounded-image">
                </a>

                {{-- Оцінка --}}
                @if ($review->w_rating)										
                    <div class="w-125 mt-1 mb-2">
                        @include('components.rating', ['rating' => $review->w_rating])						
                    </div>
                @endif
                @if (Auth::user() && $profile->id != Auth::user()->id && $review->my_w_rating)
                    <div class="mb-2">Ваша оцінка: {{$review->my_w_rating->value}}</div>
                @endif

                <div class="mb-1">{{ \Carbon\Carbon::parse($review->created_at)->format('d.m.y H:i') }}</div>
                <div>{{$review->is_public? '':'(чорновик)'}}</div>								
            </div>
            <div class="content-part">
                <div class="with-spoilers" id="review_text_{{ $review->id }}">
                    @if (Str::length($review->text) > 800)
                        <div class="truncated-text">
                            <div class="short-text">
                                {{ $service->TruncateText($review->text, 800) }}
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
                        <input type="hidden" name="w_id" value="{{$review->w_id}}">

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
    
@endsection	