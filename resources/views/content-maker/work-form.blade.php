@extends('layouts.content-maker')

@section('inner_content')	

    <div class="header-box row">
        <h1 class="col">Форма {{ $work ? 'редагування' : 'додавання' }} твору</h1>
        @if ($work)                
            <div class="col-auto align-center underline">
                <a href="{{ route('work',$work->id) }}" class="light-tippy" title="Переглянути">
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
    
    <form action="{{ $work ? route('work.edit',$work->id): route('work.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Назва</label>
            
            <input type="text" name="name" value="{{ $work ? $work->name : '' }}" class="required">
        </div>

        <!-- Блок для назв -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Альтернативні назви</label>
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt enter-btn" id="w_alt_name_add">
                <button class="btt-with-input" type="button" id="btn_w_alt_name_add">Додати назву</button>
            </div>
                
            <div id="w_alt_name_container">
                @if ($work && $alt_names)
                    @foreach ($alt_names as $alt_name)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="w_alt_name[]" value="{{ $alt_name->id }}">
                            <input readonly type="text" class="input-with-btt" value="{{ $alt_name->name }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Блок для авторів -->
        <div class="mb-3">
            <label for="w_alt_names" class="form-label">Автор (-и)</label>
            <input type="text" id="avtor_work">
                
            <div id="avtor_work_container" class="mt-3 no-empty">
                @if ($work && $avtors_work)
                    @foreach ($avtors_work as $avtor_work)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="avtor_work[]" value="{{ $avtor_work->id }}">
                            <input readonly type="text" class="input-with-btt" value="{{ $avtor_work->name.
                            ' (id = '.$avtor_work->id.')' }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Жанр --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Жанр</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">{{ $work && $work->genre ? $work->genre : 'Інше' }}</span>
                    <ul class="options hide">
                    <li data-value="">Інше</li>
                    @foreach ($genres as $genre)
                        <li data-value="{{ $genre->id }}">{{ $genre->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="genre_id" value="{{ $work ? $work->genre_id : '' }}">
            </div>                      
        </div>

        {{-- Мова написання --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Мова написання</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">{{ $work && $work->language ? $work->language : 'Інше' }}</span>
                    <ul class="options hide">
                    <li data-value="">Інше</li>
                    @foreach ($languages as $language)
                        <li data-value="{{ $language->id }}">{{ $language->name }}</li>
                    @endforeach
                    </ul>
                </div>
                <input type="hidden" name="language_id" value="{{ $work ? $work->language_id : '' }}">
            </div>     
        </div>

        {{-- Рік написання --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Рік написання</label>
            <input type="text" name="year" class="number" value="{{$work && $work->year!=0 ?  abs($work->year) : ''}}" maxlength="4"
            placeholder="рррр" autocomplete="off">
            <input type="checkbox" name="year_n_e" value="1" id="year_n_e"
            {{ $work && $work->year<0 ? 'checked' : '' }}>
            <label for="year_n_e">до н.е.</label>
        </div>

        <!-- Блок для циклів -->
        <div class="mb-3">
            <label class="form-label">Входить в цикли</label>
            <input type="text" id="work_cycle">
                
            <div id="work_cycle_container" class="mt-3">
                @if ($work && $work_cycles)
                    @foreach ($work_cycles as $work_cycle)
                        <div class="el-inserted input-group mb-1">
                            <input readonly type="text" class="hide" name="work_cycle[]" value="{{ $work_cycle->id }}">
                            <input readonly type="text" class="input-with-btt" value="{{ $work_cycle->name }}">
                            <button type="button" class="btt-with-input btn-remove-el-inserted">Видалити</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Блок для анотацій -->
        <div class="mb-3">
            <label for="anotation" class="form-label">Анотації</label>
            <div class="input-group-big mb-3">
                <textarea name="notes" rows="3" id="anotation_add">{{ $work ? $work->notes : '' }}</textarea>
                <div class='text-end mt-1'>
                    <button type="button" id="btn_anotation_add">Додати анотацію</button>
                </div>
            </div>
                
            <div id="anotation_container">
                @if ($work && $anotations)
                    @foreach ($anotations as $anotation)
                        <div class="el-inserted input-group-big mb-2">
                            <input readonly type="text" class="hide" name="anotation_id[]" value="{{ $anotation->id }}">
                            <textarea rows="3" name="anotation[]" readonly="true">{{ $anotation->text }}</textarea>
                            <div class='text-end'>
                                <button class="btn-edit-el-inserted" type="button">
                                    Редагувати</button>
                                <button type="button" class="btn-remove-el-inserted">
                                    Видалити</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Примітки</label>
            <textarea name="notes" rows="2">{{ $work ? $work->notes : '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Посилання</label>
            <textarea name="links" rows="2">{{ $work ? $work->links : '' }}</textarea>
        </div>   

        {{-- Видання для обкладинки --}}
        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Видання для обкладинки</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">
                    {{ $work && $work->main_edition_name ? $work->main_edition_name : 'Перше додане' }}</span>
                    <ul class="options hide">
                    <li data-value="">Перше додане</li>
                    @if (isset($work_editions))                                    
                        @foreach ($work_editions as $edition)
                            <li data-value="{{ $edition->id }}">{{ $edition->name }} ({{ $edition->year }}р.)</li>
                        @endforeach
                    @endif
                    </ul>
                </div>
                <input type="hidden" name="main_edition" value="{{ $work ? $work->main_edition : '' }}">
            </div>     
        </div>             

        <div class='content-end gap-1'>
            <input type="submit" name="submit" value="Зберегти">
            <input type="submit" name="submit" class="base-btn" value="Зберегти та переглянути">
        </div>
    </form>    

@endsection
	