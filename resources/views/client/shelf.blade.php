@extends('layouts.app')

@section('content')

    <div class="header-box rel">
        <h1>Книжкова полиця «{{ $shelf->name }}»</h1>
        <div class="row align-center">
            <div class="col">
                <div>книг: {{ count($shelf->works) }}</div>
            </div>
            <div class="col-auto">
                @include('components.user-item',['user' => $shelf->user])
            </div>
        </div>
        {{-- Кнопка налаштувань --}}
        <div class="options-btn">
            <div class="custom-dropdown-btn">
                <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
            </div>
            <div class="custom-dropdown-menu">
                <a class="dropdown-item confirm-link" href="{{ route('shelf.del', [$shelf->id]) }}"
                    data-message="Ви впевнені, що хочете видалити книжкову полицю?">Видалити полицю</a>
            </div>
        </div>
    </div>

    @if (Auth::user() && Auth::user()->id==$shelf->user_id )            
        <form action="{{route('shelf.edit', $shelf->id)}}" method="POST" class="validate-form mb-1">
            @csrf
            <div class="input-group">
                <input type="text" class="input-with-btt" name='name' value="{{ $shelf->name }}">
                <button class="btt-with-input" name="submit" type="submit">Перейменувати</button>
            </div>
        </form>
        <div class="mb-3">            
            <form action="{{route('shelf.add-work')}}" method="POST" class="validate-form mb-1">
                @csrf   
                <input type="hidden" name="sh_id" value="{{ $shelf->id }}">         
                <input type="search" name="w_id" id="search_works_for_shelf" placeholder="Додайте книгу на полицю">
            </form>
        </div>
    @endif 

    @foreach ($shelf->works as $work)
    <div class="with-image-box row mb-3 rel">	
        <div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
            <div class="rel with-image-box-imgs">				
                <a href="{{ route('work',$work->id) }}" class="">	
                    <img src="{{ asset($work->img) }}" alt="{{ $work->name }}">
                </a>
            </div>
        </div>				
        <div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
            <div class="pl-3 pr-3 w-100">	

                {{-- Основна інфа --}}
                <h5>{{ $work->avtors }} «{{ $work->name }}»</h5>
                <div class="to-expand mt-1">							
                    @foreach (explode("\n", $work->anotation) as $paragraph)
                        {!! nl2br(e($paragraph)) !!}
                    @endforeach
                </div>

                {{-- Додаткова інфа --}}
                <hr>
                <div class="row">
                    <div class="col">   
                        @if ($work->rating)
                            <b>Оцінка {{$shelf->user->name}}: {{$work->rating}}</b>
                        @endif                         
                        @if (Auth::user() && $shelf->user_id != Auth::user()->id && $work->my_rating)
                            <b>| Ваша оцінка: {{$work->my_rating->value}}</b>
                        @endif
                    </div>
                </div>
                
                {{-- Кнопка налаштувань --}}
                <div class="options-btn">
                    <div class="custom-dropdown-btn">
                        <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
                    </div>
                    <div class="custom-dropdown-menu">
                        <a class="dropdown-item confirm-link" href="{{ route('shelf.del-work', [$shelf->id, $work->id]) }}"
                            data-message="Ви впевнені, що хочете прибрати книгу з полиці?">Прибрати з полиці</a>
                    </div>
                </div>
            </div>
        </div>	
    </div>
    @endforeach

    @if (!$shelf->works)
        Полиця пуста.
    @endif
    
@endsection	