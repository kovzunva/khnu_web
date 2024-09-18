@extends('layouts.content-maker')

@section('inner_content')	

<div class="header-box">
    <h1>Форма {{ $person ? 'редагування' : 'додавання' }} персони</h1>
</div>

@if (!auth()->user()->hasPermission('content-make'))
    <div class="div-info mb-3">
        Цей матеріал буде непублічний і видимий лише для вас, поки його не затвердить Укладач вмісту.
    </div>
@endif

<form action="{{ $person ? route('person.edit',$person->id): route('person.add') }}" method="POST" class="validate-form">
    @csrf

    <div class="mb-3">
        <label for="" class="form-label">Ім'я</label>
        <input type="text" name="name" value="{{ $person ? $person->name : '' }}" class="required" data-required="Анонімна персона - це класно, але не для нас">
    </div>

    <!-- Блок для імен -->
    <div class="mb-3">
        <label for="p_alt_names" class="form-label">Альтернативні імена</label>
        <div class="input-group mb-4">
            <input type="text" class="input-with-btt enter_btn" id="p_alt_name_add">
            <button class="btt-with-input" type="button" id="btn_p_alt_name_add">Додати ім'я</button>
        </div>
            
        <div id="p_alt_name_container">
            @if ($person && $alt_names)
                @foreach ($alt_names as $alt_name)
                    <div class="el-inserted input-group mb-1">
                        <input readonly type="text" class="hide" name="p_alt_name[]" value="{{ $alt_name->id }}">
                        <input readonly type="text" class="form-control input-with-btt" value="{{ $alt_name->name }}">
                        <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Блок для типу персони --}}
    <div class="mb-3" id="person_type">
        <div class="at-least-one" data-error="Настирлива рекомендація вибрати хоч один варіант">
            <input type="checkbox" name="is_avtor" id="is_avtor" value="1" {{ $person && $person->is_avtor ? 'checked' : '' }}>
            <label for="is_avtor" class="form-label">Автор</label>
            <input type="checkbox" name="is_translator" id="is_translator" value="1" {{ $person && $person->is_translator ? 'checked' : '' }}>
            <label for="is_translator" class="form-label">Перекладач</label>
            <input type="checkbox" name="is_designer" id="is_designer" value="1" {{ $person && $person->is_designer ? 'checked' : '' }}>
            <label for="is_designer" class="form-label">Дизайнер</label>
            <input type="checkbox" name="is_illustrator" id="is_illustrator" value="1" {{ $person && $person->is_illustrator ? 'checked' : '' }}>
            <label for="is_illustrator" class="form-label">Ілюстратор</label>
        </div>
    </div>

    {{-- Дати --}}
    <div class="mb-3 ">
        <label for="" class="form-label">Дата (від)</label>
        <input type="text" class="me-2 input-date" placeholder="дд.мм.рррр" name="birthdate" maxlength="10"
        value="{{$person ? $birthdate : '' }}">
        <input type="checkbox" name="birthdate_n_e" value="1" id="birthdate_n_e"
        {{ $person && $person->birthdate<0 ? 'checked' : '' }}>
        <label for="birthdate_n_e">до н.е.</label>

        <label for="" class="form-label">Дата (до)</label>
        <input type="text" class="me-2 input-date" placeholder="дд.мм.рррр" name="deathdate" maxlength="10"
        value="{{$person ? $deathdate : '' }}">
        <input type="checkbox" name="deathdate_n_e" value="1" id="deathdate_n_e"
        {{ $person && $person->deathdate<0 ? 'checked' : '' }}>
        <label for="deathdate_n_e">до н.е.</label>
    </div>
    
    {{-- Біографія --}}
    <div class="mb-3">
        <label for="" class="form-label">Біографія</label>
        <textarea name="bio" class="text-editor">{{ $person ? $person->bio : '' }}</textarea>
    </div>

    {{-- Примітки --}}
    <div class="mb-3">
        <div class="row align-center">
            <div class="col">                                
                <label for="" class="form-label">Примітки</label>
            </div>
            <div class="col text-end">                                
                <button type="button" id="add_link_in_notes" class="mb-1">
                    Вставити посилання
                </button>
            </div>
        </div>
        <textarea name="notes" id="notes" rows="4">{{ $person ? $person->notes : '' }}</textarea>
    </div>

    {{-- Посилання --}}
    <div class="mb-3">
        <div class="row align-center">
            <div class="col">                                
                <label for="" class="form-label">Посилання</label>
            </div>
            <div class="col text-end">                                
                <button type="button" id="add_link_in_links" class="mb-1">
                    Вставити посилання
                </button>
            </div>
        </div>
        <textarea name="links" id="links" rows="4">{{ $person ? $person->links : '' }}</textarea>
    </div>      
    
    {{-- Картинка --}}
    <div class="mb-3">
        <label for="" class="form-label">Картинка</label>

        <div class="row">
            <div class="img_preview" id="container_img">                                   
                <img src="{{ $person && $img_edit? asset($img_edit):'' }}" id='edit_img' alt="-">
            </div>
            <div class="col">
                <div class="mb-3">
                    <input type="file" id="file_img_1" accept="image/*">
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="input-with-btt enter_btn" id="url_img_1" placeholder="URL зображення">
                    <button class="btt-with-input" type="button" id="btn_url_img_1">Додати</button>
                </div>
            </div>
        </div>

        <div class="container mb-3">
            <input type="hidden" name="img_pass" value="" id="img_pass">       
        </div>
    </div>       

    <div class='text-end'>
        <input type="submit" name="submit" class="base-btn" value="Зберегти">
    </div>
</form>     


@endsection
	
@section('styles')
    @vite(['resources/css/summernote-lite.min.css'])   
@endsection
	
@section('scripts')
    @vite(['resources/js/summernote-lite.min.js','resources/js/specified.js'])
@endsection