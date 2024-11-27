@extends('layouts.admin')

@section('inner_content')	

    <div class="header-box">
        <h1>Форма {{ $faq ? 'редагування' : 'додавання' }} довідкової статті</h1>
    </div>

    <form action="{{ $faq ? route('faq.edit',$faq->id): route('faq.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Назва</label>
            <input type="text" name="name" value="{{ $faq ? $faq->name : '' }}" class="required">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Ключові слова</label>
            <input type="text" name="keywords" value="{{ $faq ? $faq->keywords : '' }}">
        </div>

        <div class="mb-3 align-center gap-1">
            <label for="" class="form-label">Порядковий індекс</label>
            <input type="text" name="sort_index" class="number" value="{{ $faq ? $faq->sort_index : '900' }}" maxlength="3">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Вміст</label>
            <textarea name="content" class="text-editor">{{ $faq ? $faq->content : '' }}</textarea>
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