@extends('layouts.app')



@section('aside')	

@endsection



@section('content')	

    <div class="header-box row">
        <h1 class="col">Форма {{ $forum ? 'редагування' : 'додавання' }} форуму</h1>
        @if ($forum)                
            <div class="col-auto align-center underline">
                <a href="{{ route('forum',$forum->id) }}" class="light-tippy" title="Переглянути">
                    <img src="{{ asset('svg/eye.svg') }}" alt="Переглянути" class="icon">
                </a>
            </div>
        @endif
    </div>

    <form action="/forum/{{ $forum ? $forum->id.'/edit' : 'add' }}" method="POST" id="forum-form"
        class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Назва</label>
            <input type="text" name="name" value="{{ $forum ? $forum->name : '' }}" class="required">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Опис</label>
            <textarea name="about" rows="4">{{ $forum ? $forum->about : '' }}</textarea>
        </div>

        {{-- Тематика форуму --}}
        <div class="mb-3">
            <label for="" class="form-label">Тематика</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">
                    {{ $forum && $forum->tematic ? $forum->tematic->name : 'Загальна тематика' }}
                    <ul class="options hide">
                    <li data-value="" class="tematic-option">Загальна тематика</li>
                    @foreach ($tematics as $tematic)
                        <li data-value="{{ $tematic->id }}" class="tematic-option">{{ $tematic->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="tematic_id" value="{{ $forum ? $forum->tematic_id : '' }}">
            </div> 

            {{-- @foreach ($tematics as $tematic)
            <div class="mb-3 tematic-block hide" id="tematic{{$tematic->id}}">
                <label for="w_alt_names" class="form-label">{{$tematic->item_name}}</label>
                <div class="error-text hide" id="error_publisher_edition_container">Виберіть {{$tematic->item_name}}</div>
                <input type="text" class="forum_item" data-tematic-search="{{$tematic->search}}" data-item-name="{{$tematic->item_name}}">
                    
                <div class="mt-3 no-empty forum-item-container">
                    @if (isset($forum->item))
                        <input readonly type="text" value="{{ $forum->item->name }}">
                        <input type="hidden" name="item_id" value="{{ $forum->item_id }}">
                    @endif
                </div>
            </div>
            @endforeach  --}}
        </div> 
        
        {{-- Картинка --}}
        <div class="mb-3">
            <label for="" class="form-label">Картинка</label>

            <div class="row">
                <div class="img_preview col-auto" id="container_img">                                   
                    <img src="{{ $forum && $img_edit? asset($img_edit):'' }}" id='edit_img' alt="-">
                </div>
                <div class="col">
                    <div class="mb-3">
                        <input type="file" id="file_img_1" accept="image/*">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="input-with-btt enter-btn" id="url_img_1" placeholder="URL зображення">
                        <button class="btn btn-outline-secondary btt-with-input" type="button" id="btn_url_img_1">Додати</button>
                    </div>
                </div>
            </div>

            <div class="container mb-3">
                <input type="hidden" name="img_pass" value="" id="img_pass">       
            </div>
        </div>     

        <div class='content-end gap-1'>
            <input type="submit" name="submit" value="Зберегти">
            <input type="submit" name="submit" class="base-btn" value="Зберегти та переглянути">
        </div>
    </form>    

@endsection
	