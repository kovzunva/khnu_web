@extends('layouts.admin')

@section('inner_content')	

    <div class="header-box">
        <h1>Класифікатор</h1>
    </div>

    {{-- Групи --}}
    <details id="classificator_group_details">
        <summary><b>Групи опцій</b></summary>
        <form action="{{route('classificator-group.add')}}" method="POST"
            class="validate-form">
            @csrf

            <div class="mb-3 mt-1">
                <div class="input-group mb-1">
                    <input type="text" class="input-with-btt" name='name'>
                    <button class="btt-with-input" type="submit">Додати</button>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Індекс</label>
                    <input type="text" class="number" name="sort_index" value="{{count($classificator_groups)+1}}">
                    <input type="checkbox" name="radio" id="radio" value="1">
                    <label for="radio">Радіо</label>
                </div>
            </div>
        </form>  
        <hr class="mt-3 mb-4">          
            
        @foreach ($classificator_groups as $group)
        <form action="{{route('classificator-group.edit',$group->id)}}" method="POST"
            class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input type="text" class="form-control input-with-btt" value="{{ $group->name }}" name="name">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['classificator_group',$group->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$group->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Індекс</label>
                <input type="text" class="number" name="sort_index" value="{{$group->sort_index}}">
                <input type="checkbox" name="radio" id="radio{{$group->id}}" {{$group->radio? 'checked':''}} value='1'>
                <label for="radio{{$group->id}}">Радіо</label>
            </div>
        </form>
        @endforeach
    </details>

    {{-- Опції по групах --}}
    <section class="small-section">
        <header><h2>Опції по групах</h2></header>

        @foreach ($groups as $group_name => $group)
        <details id="classificator_option{{$group->id}}_details">
            <summary><b>{{$group_name}}</b></summary>

            {{-- Додати опцію --}}
            <form action="{{route('classificator-option.add')}}" method="POST"
            class="validate-form">
                @csrf

                <div class="mb-3 mt-1">
                    <div class="input-group mb-1">
                        <input type="hidden" name="group_id" value="{{$group->id}}">
                        <input type="text" class="input-with-btt" name='name'>
                        <button class="btt-with-input" type="submit">Додати</button>
                    </div>
                    <div class="mb-1 row">    
                        <div class="col-auto">                                
                            <label for="" class="form-label">Індекс</label>
                            <input type="text" class="number" name="sort_index">                       
                            <input type="checkbox" name="change" id="change_add{{$group->id}}" value='1'>
                            <label for="change_add{{$group->id}}">Вплив</label> 
                        </div>
                        <div class="col">                                    
                            {{-- Батьківська опція --}}
                            <div class="base-select">
                                <div class="select-box">
                                <span class="selected-option d-flex align-items-center">-</span>
                                <ul class="options hide">
                                    <li data-value="">-</li>
                                    @foreach ($classificator_options_change as $option_change)
                                        <li data-value="{{ $option_change->id }}">{{ $option_change->name }}</li>
                                    @endforeach
                                </ul>
                                </div>
                                <input type="hidden" name="change_id">
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
            <hr>  
            
            {{-- Опції --}}
            @foreach ($group->options as $option)
            <form action="{{route('classificator-option.edit',$option->id)}}" method="POST"
                class="validate-form">
                @csrf
                <div class="el-inserted input-group mb-1 mt-1">
                    <input type="text" class="form-control input-with-btt" value="{{ $option->name }}" name="name">
                    <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                    <a class=" confirm-link" href="{{route('category.del',['classificator_option',$option->id])}}"
                    data-message="Ви впевнені, що хочете видалити категорію «{{$option->name}}»?">
                        <button type="button" class="btt-with-input">Видалити</button>                            
                    </a>
                </div>
                <div class="mb-1 row">    
                    <div class="col-auto">                                
                        <label for="" class="form-label">Індекс</label>
                        <input type="text" class="number" name="sort_index" value="{{$option->sort_index}}">                       
                        <input type="checkbox" name="change" id="change{{$option->id}}" {{$option->change? 'checked':''}} value='1'>
                        <label for="change{{$option->id}}">Вплив</label> 
                    </div>
                    <div class="col">                                    
                        {{-- Батьківська опція --}}
                        <div class="base-select">
                            <div class="select-box">
                            <span class="selected-option d-flex align-items-center">
                                {{ $option->change_option ? $option->change_option:'-' }}</span>
                            <ul class="options hide">
                                <li data-value="">-</li>
                                @foreach ($classificator_options_change as $option_change)
                                    <li data-value="{{ $option_change->id }}">{{ $option_change->name }}</li>
                                @endforeach
                            </ul>
                            </div>
                            <input type="hidden" name="change_id" value="{{  $option->change_id ? $option->change_id : '' }}">
                        </div> 
                    </div>
                </div>
                {{-- Група опції --}}
                <div class="mb-3">
                    <div class="base-select">
                        <div class="select-box">
                        <span class="selected-option d-flex align-items-center">{{ $option->group_name ? $option->group_name : '-' }}
                            </span>
                        <ul class="options hide">
                            @foreach ($classificator_groups as $group)
                                <li data-value="{{ $group->id }}">{{ $group->name }}</li>
                            @endforeach
                        </ul>
                        </div>
                        <input type="hidden" name="group_id" value="{{  $option->group_id ? $option->group_id : '8' }}">
                    </div>     
                </div>
            </form>

                {{-- Підопції --}}
                @if (isset($option->suboptions))
                    <ul>
                    @foreach ($option->suboptions as $option)
                    <li>
                    <hr>
                    <form action="{{route('classificator-option.edit',$option->id)}}" method="POST"
                        class="validate-form">
                        @csrf
                        <div class="el-inserted input-group mb-1 mt-1">
                            <input type="text" class="form-control input-with-btt" value="{{ $option->name }}" name="name">
                            <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                            <a class=" confirm-link" href="{{route('category.del',['classificator_option',$option->id])}}"
                            data-message="Ви впевнені, що хочете видалити категорію «{{$option->name}}»?">
                                <button type="button" class="btt-with-input">Видалити</button>                            
                            </a>
                        </div>
                        <div class="mb-1 row">    
                            <div class="col-auto">                                
                                <label for="" class="form-label">Індекс</label>
                                <input type="text" class="number" name="sort_index" value="{{$option->sort_index}}">                       
                                <input type="checkbox" name="change" id="change{{$option->id}}" {{$option->change? 'checked':''}} value='1'>
                                <label for="change{{$option->id}}">Вплив</label> 
                            </div>
                            <div class="col">                                    
                                {{-- Батьківська опція --}}
                                <div class="base-select">
                                    <div class="select-box">
                                    <span class="selected-option d-flex align-items-center">{{ $option->change_option ? $option->change_option :
                                    '-' }}</span>
                                    <ul class="options hide">
                                        <li data-value="">-</li>
                                        @foreach ($classificator_options_change as $option_change)
                                            <li data-value="{{ $option_change->id }}">{{ $option_change->name }}</li>
                                        @endforeach
                                    </ul>
                                    </div>
                                    <input type="hidden" name="change_id" value="{{  $option->change_id ? $option->change_id : '' }}">
                                </div> 
                            </div>
                        </div>
                        {{-- Група опції --}}
                        <div class="mb-3">
                            <div class="base-select">
                                <div class="select-box">
                                <span class="selected-option d-flex align-items-center">{{ $option->group_name ? $option->group_name : '-' }}
                                    </span>
                                <ul class="options hide">
                                    @foreach ($classificator_groups as $group)
                                        <li data-value="{{ $group->id }}">{{ $group->name }}</li>
                                    @endforeach
                                </ul>
                                </div>
                                <input type="hidden" name="group_id" value="{{  $option->group_id ? $option->group_id : '8' }}">
                            </div>     
                        </div>
                    </form>                                
                    </li>
                    @endforeach
                    </ul>
                @endif
                <hr>
            @endforeach
        </details>
        @endforeach
    </section>

    {{-- <form action="{{route('expert-system.question-post')}}" method="POST" id="question-form"
        class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="questions" class="form-label">Запитання</label>
            <div class="mb-4">
                <textarea name="question" rows="2"></textarea>
                <div class="mt-2 mb-2">
                    <label for="" class="form-label">Гілка</label>                            
                    <div class="base-select">
                        <div class="select-box">
                            <span class="selected-option d-flex align-items-center">Нейтральна
                            <i class="fa-solid fa-angle-down ml-auto"></i></span>
                            <ul class="options hide">
                            <li data-value="">Нейтральна</li>
                            @foreach ($rezults as $rezult)
                                <li data-value="{{ $rezult->id }}">{{ $rezult->name }} фентезі</li>
                            @endforeach
                            </ul>
                        </div>
                        <input type="hidden" name="question_line_id" value="">
                    </div>                      
                </div>
                <div class="text-end">
                    <button class="btt-with-input" type="submit">Додати</button>
                </div>
            </div>
                
            <div id="question_container">
                @php
                    $previousLineId = null;
                @endphp

                @if (!$questions[0]->line_id)
                    <h3>Гілка: Нейтральна</h3>
                @endif

                @foreach ($questions as $question)
                    @if ($question->line_id != $previousLineId)
                        @if ($previousLineId !== null)
                            </div>
                        @endif
                        <h3>Гілка: {{ $question->line_id ? $question->line.' фентезі' : "Нейтральна" }}</h3>
                        <div class="question-group">
                    @endif

                    <div class="el-inserted input-group-big mb-4">
                        <input readonly type="text" class="hide" name="question_id[]" value="{{ $question->id }}">
                        <textarea rows="1" readonly="true">{{ $question->text }}</textarea>
                        <div class='text-end'>
                            <a href="{{ route('expert-system.question-edit-form',$question->id) }}" 
                                class="btt-with-input" type="button">
                                Редагувати</a>
                            <button type="submit" class="btt-with-input btn-remove-el-inserted-kpz">
                                Видалити</button>
                        </div>
                    </div>

                    @php
                        $previousLineId = $question->line_id;
                    @endphp
                @endforeach

                @if ($previousLineId !== null)
                    </div>
                @endif

            </div>
        </div>
    </form> --}}

    <script>
    let detailsElement = document.getElementById("{{ session('table') }}_details");
    </script>
@endsection
	