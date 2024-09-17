@extends('layouts.app')

@section('content')
    Вас вітає читацький сервіс «Щопочитайка»!

    @if ($works) 
        <hr class="mb-4">
        @foreach($works as $work)
            <a href="{{ route('work',$work->id) }}" class="">
                <div class="with-image-box row mb-3">	
                    <div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
                        <div class="rel with-image-box-imgs">					
                            <img src="{{ asset($work->img) }}" alt="{{ $work->name }}">
                        </div>
                    </div>				
                    <header class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
                        <div class="pl-3 pr-3">								
                            <h5>{{ $work->avtors }} «{{ $work->name }}»</h5>
                            {{ Str::limit($work->anotation, 160) }}
                            <div class="mt-1 mb-1">
                                <b>{{ $work->options_info }}</b>
                            </div>
                            <div class="rating-container"
                                @if (isset($work->rating)) data-rating="{{$work->rating}}"  @endif>
                                Рейтинг: {{isset($work->rating)? number_format($work->rating, 2, '.', ''):'0'}}
                            </div>
                        </div>
                    </header>	
                </div>
            </a>
        @endforeach
    @endif
    
    @if ($review)
        <hr class="mb-4 mt-5">
        <a href="{{route('work',$review->w_id)}}" class="">        
            <h4>Останній відгук ({{$review->work_avtors}} «{{$review->work}}»)</h4>
        </a>     
        <div class="item-box mb-3 rel" data-review-id="{{$review->id}}">
            <div class="row">
                <div class="col">
                    @include('components.user-item',['user' => $review->user]),
                    {{$review->created_at}} {{$review->is_public? '':'(чорновик)'}}

                    {{-- Текст відгуку --}}
                    <div class="to-expand mt-1">							
                            @foreach (explode("\n", $review->text) as $paragraph)
                                {!! nl2br(e($paragraph)) !!}
                            @endforeach
                    </div>
                </div>

                <div class="col-auto ">
                    <div class="flex-column h-100">	
                        <div class="col text-center">												
                            <button class="expand-button">
                                <img src="{{asset('/svg/angle-double-down.svg')}}" class="small-icon">
                            </button>	
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    @if ($blog)
        <hr class="mb-4 mt-5">
        <a href="{{route('blog',$blog->id)}}" class="">
            <h4>Останній блог</h4>
            <div class="light-box mb-3">
                <h3 class="me-5">{{ $blog->title }}</h3>
                
                <div class="pb-2">
                    {!! Str::limit(strip_tags($blog->content), 200) !!}
                    @if (strlen(strip_tags($blog->content)) > 200)
                        <span>Читати більше</span>
                    @endif
                </div>
                <div class="row">
                    <div class="col">
                        @include('components.user-item',['user' => $blog->user]),
                        <span>{{ $blog->created_at->format('d.m.Y H:i:s') }}</span>
                        <b class="ms-1">{{ $blog->category? '#'.$blog->category->name:'' }}</b>
                    </div>
                </div>
            </div>
        </a>	
    @endif
@endsection	