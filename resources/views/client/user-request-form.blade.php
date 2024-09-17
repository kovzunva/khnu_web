@extends('layouts.app')

@section('aside')	
@endsection

@section('content')	

    <div class="header-box">
        <h1>Форма зворотного зв'язку</h1>
    </div>

    <form action="{{ route('user-request.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Тема заявки</label>
            <div class="error-text hide" id="error_name">Заповніть поле</div>
            <input type="text" name="name" class="required">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Текст заявки</label>
            <textarea name="text" rows="15"></textarea>
        </div>                             

        <div class='text-end'>
            <input type="submit" name="submit" class="base-btn" value="Надіслати">
        </div>
    </form>    

@endsection
	