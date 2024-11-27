@extends('layouts.app')

@section('content')
    
    <div class="gradient-line"></div>
    <div class="row">
        <div class="col">
            <div class="main-quote-container">
                <div class="main-quote-inner-container">
                    <div>Будинок, в якому немає книг, подібний до тіла, позбавленого душі </div>
                    <div class="text-end">(Цицерон)</div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <h1 class="mt-5 main-h1">Щопочитайка</h1>
            <div class="content-end">
                <p class="max-width-350 mt-0">читацький сервіс для обговорення, оцінювання та підбору книг для прочитання</p>
            </div>
        </div>
    </div>    

    @if ($works) 
        <section class="small-section mt-5 pt-5">
            <header>
                <h2>Найбільш рейтингові книги:</h2>
            </header>
            @foreach($works as $work)
                <a href="{{ route('work',$work->id) }}" class="">
                    <div class="with-image-box row mb-3">	
                        <div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
                            <div class="rel with-image-box-imgs">					
                                <img src="{{ asset($work->img) }}" alt="{{ $work->name }}">
                            </div>
                        </div>				
                        <div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
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
                        </div>	
                    </div>
                </a>
            @endforeach
        </section>
    @endif
    
@endsection	