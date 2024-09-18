@extends('layouts.admin')

@section('inner_content')

    <div class="header-box">
        <h1>Редагування запитання</h1>
    </div>

    <form action="{{route('expert-system.question-edit',$question->id)}}" method="POST" id="question-form"
        class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="questions" class="form-label">Запитання</label>
            <div class="mb-4">
                <textarea name="question_text" rows="2">{{$question->text}}</textarea>
                <div class="mb-2 mt-1">
                    <label for="" class="form-label">Гілка</label>
                    <div class="base-select">
                        <div class="select-box">
                            <span class="selected-option d-flex align-items-center">
                            {{$question->line? $question->line.' фентезі' : 'Нейтральна'}} 
                            <i class="fa-solid fa-angle-down ml-auto"></i></span>
                            <ul class="options hide">
                            <li data-value="">Нейтральна</li>
                            @foreach ($rezults as $rezult)
                                <li data-value="{{ $rezult->id }}">{{ $rezult->name }} фентезі</li>
                            @endforeach
                            </ul>
                        </div>
                        <input type="hidden" name="question_line_id" value="{{$question->line_id? $question->line_id: '' }}">
                    </div>                      
                </div>
                <div class="text-end">
                    <button type="submit" name="submit">Внести зміни</button>
                </div>
            </div>
        </div>
    </form>

    <hr>
    <form action="{{route('expert-system.answer-post',$question->id)}}" method="POST" id="answer-form"
        class="validate-form">
        @csrf

        <div class="mb-3">
            <div class="mb-4">
                <div class="mb-3">
                    <label for="" class="form-label">Відповідь</label>
                    <textarea name="answer_text" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Результат</label>
                    <div class="base-select">
                        <div class="select-box">
                            <span class="selected-option d-flex align-items-center">{{$rezults[0]->name}} фентезі
                            <i class="fa-solid fa-angle-down ml-auto"></i></span>
                            <ul class="options hide">
                            @foreach ($rezults as $rezult)
                                <li data-value="{{ $rezult->id }}">{{ $rezult->name }} фентезі</li>
                            @endforeach
                            </ul>
                        </div>
                        <input type="hidden" name="answer_rezult_id" value="{{$rezults[0]->id}}">
                    </div>                      
                </div>

                <div>
                    <label for="" class="form-label">Наближення до результату</label>
                    <input type="text" name="answer_size" value="1" class="number-dot">
                </div>

                <div class="text-end">
                    <button type="submit">Додати</button>
                </div>
            </div>
    </form>
            

    <form action="{{route('expert-system.answer-post',$question->id)}}" method="POST" id="answer-form"
        class="validate-form">
        @csrf
            <h3>Відповіді</h3>
            <hr>
            <div id="answer_container">
                @foreach ($answers as $answer)
                    <div class="el-inserted input-group-big mb-3">
                        <input readonly type="text" class="hide" name="answer_id[]" value="{{ $answer->id }}">
                        <textarea rows="1" readonly="true">{{ $answer->text }}</textarea>
                        <div>
                            <label for="" class="form-label">Наближення до результату«{{$answer->rezult}} фентезі»:
                                {{ $answer->size }}
                        </div>
                        <div class='text-end'>
                            <button type="submit" class=" btn-remove-el-inserted-kpz">
                                Видалити</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>
    
@endsection
	