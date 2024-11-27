@extends('layouts.content-maker')

@section('inner_content')	

    <div class="header-box row">
        <h1 class="col">Форма {{ $edition ? 'редагування' : 'додавання' }} видання</h1>
        @if ($edition)                
            <div class="col-auto align-center underline">
                <a href="{{ route('edition',$edition->id) }}" class="light-tippy" title="Переглянути">
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

    {{-- Імпорт --}}
    @if (!$edition) 
        <div>                                   
            <form action="{{ route('import.edition') }}" method="POST" id="import-form" class="validate-form">
                @csrf
                <label for="">Сервіси для імпорту: YAKABOO, Наш Формат, Vivat, Книгарня "Є"</label>
                <div class="input-group mb-3 mt-1">
                    <input type="text" class="input-with-btt enter-btn" id="url_import" placeholder="URL сторінки" value="https://www.yakaboo.ua/ua/muzichna-skrin-ka-tom-1-laskavo-prosimo-v-pandoriju.html?sc_content=26389_r1475v1876">
                    <button class="btt-with-input" type="button" id="btn_url_import">Імпортувати</button>
                </div>
                <div class="error-text hide" id="error_import">Помилка імпорту</div>
            </form>
            <div id="import_add"></div>
            <hr>
        </div>
    @endif

    {{-- Швидке додавання компонентів --}}
    <div>
        <details>
            <summary><b>Швидке додавання компонентів</b></summary>
            {{-- Видавництва --}}
            <form action="" method="POST" class="validate-form">
                <div class="mb-3 mt-1">
                    <label for="publishers" class="form-label">Видавництва</label>
                    <div class="error-text hide error">Виникла помилка з додаванням</div>
                    <div class="success-text hide success">Додано успішно</div>
                    <div class="error-text hide error_name">Заповніть це поле</div>
                    <div class="input-group mb-4">
                        <input type="text" class="input-with-btt enter-btn" name="name" placeholder="Введіть видавництво">
                        <button class="btt-with-input btn_publisher_add" type="button">Додати</button>
                    </div>
                </div>
            </form>
            {{-- Персони --}}         
            <form action="" method="POST" class="validate-form">
                @csrf         
                <div class="mb-3">               
                    <label for="persons" class="form-label">Персони</label>
                    <div class="error-text hide error">Виникла помилка з додаванням персони</div>
                    <div class="success-text hide success">Персону додано успішно</div>
                    <div class="error-text hide error_name">Заповніть це поле</div>
                    <div class="input-group mb-1">
                        <input type="text" class="input-with-btt enter-btn" name="name" placeholder="Введіть персону">
                        <button class="btt-with-input btn_person_add" type="button">Додати</button>
                    </div>
                    <div class="mb-1">
                        <input type="text" name="alt_name" placeholder="Альтернативне ім'я">
                    </div>
                    {{-- Блок для типу персони --}}
                    <div class="mb-3 at-least-one person_type">
                        <input type="checkbox" name="is_avtor" id="is_avtor" value="1" checked>
                        <label for="is_avtor" class="form-label">Автор</label>
                        <input type="checkbox" name="is_translator" id="is_translator" value="1">
                        <label for="is_translator" class="form-label">Перекладач</label>
                        <input type="checkbox" name="is_designer" id="is_designer" value="1">
                        <label for="is_designer" class="form-label">Дизайнер</label>
                        <input type="checkbox" name="is_illustrator" id="is_illustrator" value="1">
                        <label for="is_illustrator" class="form-label">Ілюстратор</label>
                    </div>
                </div>
            </form>
            {{-- Твори --}}                    
            <form action="" method="POST" class="validate-form" autocomplete="off">
                <div class="mb-3">
                    <label for="editions" class="form-label">Твори</label>
                    <div class="error-text hide error">Виникла помилка з додаванням</div>
                    <div class="success-text hide success">Додано успішно</div>
                    <div class="error-text hide error_name">Заповніть це поле</div>
                    <div class="input-group-big mb-4">
                        <input type="text" class="enter-btn mb-1" name="name" placeholder="Введіть твір">
                        <div class="mb-1">
                            <input type="text" name="alt_name" placeholder="Альтернативна назва">
                        </div>
                        <div class="mb-1">
                            <div class="base-select">
                                <div class="select-box">
                                    <span class="selected-option d-flex align-items-center">Виберіть жанр</span>
                                    <ul class="options hide">
                                    <li data-value="">Інше</li>
                                    @foreach ($genres as $genre)
                                        <li data-value="{{ $genre->id }}">{{ $genre->name }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" name="genre_id">
                            </div>                      
                        </div>
                        <div class="error-text hide error-avtor">Введіть автора</div>
                        <input type="text" class="mb-1 work-avtor-add">
                        <div class="work-avtors mt-1"></div>
                        <textarea class="mt-1" name="anotation" rows="4" placeholder="Введіть анотацію"></textarea>
                        <div class='text-end mt-1'>
                            <button class="btn_work_add" type="button">Додати</button>
                        </div>
                    </div>
                </div>
            </form>
        </details>
        <hr>
    </div>

    <form action="{{ $edition ? route('edition.edit',$edition->id): route('edition.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Назва</label>
            <input type="text" name="name" value="{{ $edition ? $edition->name : '' }}" class="required">
        </div>

        <!-- Блок для авторів -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Автор (-и)</label>
            <input type="text" id="avtor_edition">
                
            <div id="avtor_container" class="mt-3 no-empty">
                @if ($edition && $avtors)
                    @foreach ($avtors as $avtor)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="avtor[]" value="{{ $avtor->id }}">
                            <input readonly type="text" class="form-control input-with-btt" value="{{ $avtor->base_name.
                            ' (id = '.$avtor->id.')' }}">
                            <input readonly type="text" class="form-control" value="{{ $avtor->name }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Блок для видавництва -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Видавництво</label>
            <input type="text" id="publisher_edition">
                
            <div id="publisher_edition_container" class="mt-3 no-empty">
                @if ($edition)
                    <input readonly type="text" value="{{ $edition->publisher }}">
                    <input type="hidden" name="publisher_id" value="{{ $edition->publisher_id }}">
                @endif
            </div>
        </div>

        {{-- Мова --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Мова</label>
            <div class="base-select" id="language_select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">{{ $edition && $edition->language_name ? $edition->language_name : 'Інше' }}
                    </span>
                    <ul class="options hide">
                    <li data-value="">Інше</li>
                    @foreach ($languages as $language)
                        <li data-value="{{ $language->id }}">{{ $language->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="language_id" value="{{ $edition ? $edition->language_id : '' }}">
            </div>     
        </div>

        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Рік видання</label>
            <input type="text" name="year" class="number" value="{{$edition && $edition->year!=0 ? $edition->year : ''}}" maxlength="4"
            placeholder="рррр" autocomplete="off">
        </div>
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Тираж</label>
            <input type="text" name="size" class="number" value="{{$edition && $edition->size!=0 ?  $edition->size : ''}}" maxlength="8"
            autocomplete="off">
        </div>
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Кількість сторінок</label>
            <input type="text" name="pages" class="number" value="{{$edition && $edition->pages!=0 ?  $edition->pages : ''}}" maxlength="4"
            autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">ISBN</label>
            <input type="text" name="isbn" value="{{ $edition ? $edition->isbn : '' }}">
        </div>

        {{-- Тип обкладинки --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Тип обкладинки</label>
            <div class="base-select" id="type_of_cover_select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">{{ $edition && $edition->type_of_cover ? $edition->type_of_cover : 'Інше' }}
                    </span>
                    <ul class="options hide">
                    <li data-value="">Інше</li>
                    @foreach ($types_of_cover as $type_of_cover)
                        <li data-value="{{ $type_of_cover->id }}">{{ $type_of_cover->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="type_of_cover_id" value="{{ $edition ? $edition->type_of_cover_id : '' }}">
            </div>                      
        </div>

        {{-- Формат обкладинки --}}
        <div class="mb-3">
            <label for="" class="form-label">Формат обкладинки</label>
            <input type="text" name="format" value="{{ $edition ? $edition->format : '' }}">
        </div>

        <!-- Блок для дизайнерів -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Дизайнер (-и)</label>
            <input type="text" id="designer_edition">
                
            <div id="designer_container" class="mt-3">
                @if ($edition && $designers)
                    @foreach ($designers as $designer)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="designer[]" value="{{ $designer->id }}">
                            <input readonly type="text" class="form-control input-with-btt" value="{{ $designer->base_name.
                            ' (id = '.$designer->id.')' }}">
                            <input readonly type="text" class="form-control" value="{{ $designer->name }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Блок для ілюстраторів -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Ілюстратор (-и)</label>
            <input type="text" id="illustrator_edition">
                
            <div id="illustrator_container" class="mt-3">
                @if ($edition && $illustrators)
                    @foreach ($illustrators as $illustrator)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="illustrator[]" value="{{ $illustrator->id }}">
                            <input readonly type="text" class="form-control input-with-btt" value="{{ $illustrator->base_name.
                            ' (id = '.$illustrator->id.')' }}">
                            <input readonly type="text" class="form-control" value="{{ $illustrator->name }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Опис</label>
            <textarea name="about" rows="5">{{ $edition ? $edition->about : '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Примітки</label>
            <textarea name="notes" rows="2">{{ $edition ? $edition->notes : '' }}</textarea>
        </div>
        <div class="mb-4">
            <label for="" class="form-label">Посилання</label>
            <textarea name="links" rows="2">{{ $edition ? $edition->links : '' }}</textarea>
        </div>

        <!-- Блок для вмісту -->
        <div class="mb-4">
            <div class="header-box">Вміст</div>
            <input type="text" id="edition_item">
                
            <ul id="item_container" class="mt-3 no-empty">
                @if ($edition && $items)
                    @foreach ($items as $item)
                        <div class="el-inserted insert-item mb-3" data-item-id="{{$item->id}}">
                            <li>Елемент № <span class='item-number'>{{$item->number}}</span></li>
                            <input readonly type="hidden" name="item[{{$item->number}}][w_id]" value="{{$item->w_id}}">
                            <input readonly type="hidden" name="item[{{$item->number}}][id]" value="{{$item->id}}">
            
                            <div class="mb-2">
                                <label for="" class="form-label">Назва в виданні</label>
                                <input readonly type="text" class="form-control" name="item[{{$item->number}}][name]" value="{{$item->name}}">
                            </div>

                            <details>
                                <summary>Детальніше</summary>
            
                                <div class="mb-2 mt-2">
                                    <label for="" class="form-label">Назва в базі</label>
                                    <input readonly type="text" class="form-control" value="{{$item->base_name}}">
                                </div>
                
                                <div class="mb-2">
                                    <label for="" class="form-label">Додайте перекладача</label>
                                    <input type="text" class="form-control item_translator_add" data-it_id="{{$item->id}}">
                                    <div class="mt-2 translator_container">
                                        @foreach ($item->translators as $translator)
                                        <div class="el-inserted input-group mb-1">
                                            <input readonly type="hidden" name="translator[{{$item->id}}_{{$translator->tr_id}}][it_id]" value="{{$item->id}}">
                                            <input readonly type="hidden" name="translator[{{$item->id}}_{{$translator->tr_id}}][tr_id]" value="{{$translator->tr_id}}">
                                            <input readonly type="text" class="form-control input-with-btt" 
                                            value="{{$translator->base_name}} (id = {{$translator->tr_id}})">
                                            <input readonly type="text" class="form-control input-with-btt" 
                                            name="translator[{{$item->id}}_{{$translator->tr_id}}][name]" value="{{$translator->name}}">
                                            <button type="button" class="btt-with-input btn-remove-translator-inserted">Видалити</button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                
                                <div class="mb-2">
                                    <label for="" class="form-label">Сторінки</label>
                                    <input readonly type="text" class="form-control" name="item[{{$item->number}}][pages]" value="{{$item->pages}}">
                                </div>
                
                                <div class="mb-2">
                                    <label for="" class="form-label">Рівень </label>
                                    <input readonly type="text" class="number" name="item[{{$item->number}}][level]" value="{{$item->level}}">
                                </div>
                
                                <div class="mb-2">
                                    <label for="" class="form-label">Позиція</label>
                                    <input readonly type="text" class="number" name="item[{{$item->number}}][number]" value="{{$item->number}}">
                                </div>
                
                                <div class='text-end'>
                                    <button type="button" class="btn-edit-item">Редагувати</button>
                                    <button type="button" class="btn-remove-el-inserted">Видалити</button>
                                </div>
                            </details>
                        </div>
                    @endforeach 
                @endif
            </ul>
        </div>

        {{-- Зображення --}}
        <div>
            <div class="header-box">Зображення</div>
            @include('components.upload-img', ['img' => null, 'size' => 'відповідне до видання'])
            {{-- <div class="mb-3">
                <input type="file" id="file_img" accept="image/*">
            </div>
            <div class="input-group mb-3">
                <input type="text" class="input-with-btt enter-btn" id="url_img" placeholder="URL зображення">
                <button class="btt-with-input" type="button" id="btn_url_img">Додати</button>
            </div>

            <input type="hidden" name="main_img" value="{{ $edition ? $edition->main_img : '0' }}">
            <div class="container hide" id="img_passes">
            </div> --}}

            {{-- Виведення зображень --}}
            {{-- <div class="container">
                <div class="d-flex" id="container_img">
                    @if ($edition && $imgs_edit)
                    @foreach ($imgs_edit as $index => $img_edit)
                        <div class="edit-img-container rel">     
                            <img src="{{ asset($img_edit) }}" alt="{{ $edition->name }}">
                            <img src="{{ asset('svg/close.svg') }}" class="icon top-right-icon saved-img-del-btn pointer" data-img="{{ $img_edit }}" alt="Видалити зображенння" title="Видалити зображенння"> --}}
                            {{-- <div class="edit-img-items row">
                                <div class="col-auto p-1">                                        
                                    <input type="radio" name="is_main_img" id="is_main_img_{{ $index }}"
                                    {{$edition->main_img == $index ? 'checked' : '' }}>
                                    <label for="is_main_img_{{ $index }}">Головна</label>
                                </div>
                            </div> --}}
                        {{-- </div>
                    @endforeach
                    @endif
                </div>
            </div> --}}
        </div>
        

        <div class='content-end gap-1'>
            <input type="submit" name="submit" value="Зберегти">
            <input type="submit" name="submit" class="base-btn" value="Зберегти та переглянути">
        </div>
    </form>   

<script>
    var genres_for_work_item = @json($genres);
</script>
@vite(['resources/js/edition.js'])   

@endsection
	