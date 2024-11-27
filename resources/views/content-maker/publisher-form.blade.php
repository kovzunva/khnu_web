@extends('layouts.content-maker')

@section('inner_content')	

    <div class="header-box row">
        <h1 class="col">Форма {{ $publisher ? 'редагування' : 'додавання' }} видавництва</h1>
        @if ($publisher)                
            <div class="col-auto align-center underline">
                <a href="{{ route('publisher',$publisher->id) }}" class="light-tippy" title="Переглянути">
                    <img src="{{ asset('svg/eye.svg') }}" alt="Переглянути" class="icon">
                </a>
            </div>
        @endif
    </div>

    @if (!auth()->user()->hasPermission('content-make'))
        <div class="div-info mb-3">
            Цей матеріал буде непублічний і видимий лише для вас, поки його не затвердить Укладач вмісту.
        </div>
    @endif
    
    <form action="{{$publisher ? route('publisher.edit',$publisher->id): route('publisher.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Назва</label>
            <input type="text" name="name" value="{{ $publisher ? $publisher->name : '' }}" class="required" data-error="Це якесь підозріле анонімне видавництво?..">
        </div>

        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Країна</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">{{ $publisher && $publisher->country_name ? $publisher->country_name : 'Інше' }}
                    </span>
                    <ul class="options hide">
                    <li data-value="">Інше</li>
                    @foreach ($countries as $country)
                        <li data-value="{{ $country->id }}">{{ $country->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="country_id" value="{{ $publisher ? $publisher->country_id : '' }}">
            </div>     
        </div>

        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Рік заснування</label>
            <input type="text" name="year" class="number" value="{{$publisher && $publisher->year!=0 ?  abs($publisher->year) : ''}}" maxlength="4" placeholder="рррр" autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Місто</label>
            <input type="text" name="city" value="{{ $publisher ? $publisher->city : '' }}">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Опис</label>
            <textarea name="bio" rows="3">{{ $publisher ? $publisher->about : '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Примітки</label>
            <textarea name="notes" rows="2">{{ $publisher ? $publisher->notes : '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Посилання</label>
            <textarea name="links" rows="2">{{ $publisher ? $publisher->links : '' }}</textarea>
        </div>         
    
        {{-- Картинка --}}
        <div class="mb-3">
            <div class="header-box">Зображення</div>
            @include('components.upload-img', ['img' => $publisher && $publisher->img_edit? asset($publisher->img_edit) : '', 'size' => '' ])
        </div>      

        <div class='content-end gap-1'>
            <input type="submit" name="submit" value="Зберегти">
            <input type="submit" name="submit" class="base-btn" value="Зберегти та переглянути">
        </div>
    </form>     

@endsection
	