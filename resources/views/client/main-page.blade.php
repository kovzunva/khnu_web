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
@endsection	