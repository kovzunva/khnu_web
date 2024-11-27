@extends('layouts.app')

@section('aside')    

@endsection

@section('content')    

    <div class="header-box row">
        <h1 class="col">{{ $blog ? __('Форма редагування блогу') : __('Форма додавання блогу') }}</h1>
        @if ($blog)                
            <div class="col-auto align-center underline">
                <a href="{{ route('blog',$blog->id) }}" class="light-tippy" title="{{ __('Переглянути') }}">
                    <img src="{{ asset('svg/eye.svg') }}" alt="{{ __('Переглянути') }}" class="icon">
                </a>
            </div>
        @endif
    </div>

    <form action="{{ $blog ? route('blog.edit', $blog->id) : route('blog.add') }}" method="POST" class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">{{ __('Назва') }}</label>
            <input type="text" name="name" value="{{ $blog ? $blog->name : '' }}" class="required">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">{{ __('Категорія') }}</label>
            <div class="base-select">
                <div class="select-box">
                    <span class="selected-option d-flex align-items-center">
                        {{ $blog && $blog->category ? $blog->category->name : __('Інше') }}
                    </span>
                    <ul class="options hide">
                        <li data-value="">{{ __('Інше') }}</li>
                        @foreach ($categories as $category)
                            <li data-value="{{ $category->id }}">{{ $category->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <input type="hidden" name="category_id" value="{{ $blog ? $blog->category_id : '' }}">
            </div>     
        </div>

        <div class="mb-3">
            <label for="" class="form-label">{{ __('Текст') }}</label>
            <textarea name="content" class="text-editor">{{ $blog ? $blog->content : '' }}</textarea>
        </div>                             

        <div class='content-end gap-1'>
            <input type="submit" name="submit" value="{{ __('Зберегти') }}">
            <input type="submit" name="submit" class="base-btn" value="{{ __('Зберегти та переглянути') }}">
        </div>
    </form>    

@endsection

@section('styles')
    @vite(['resources/css/summernote-lite.min.css'])   
@endsection

@section('scripts')
    @vite(['resources/js/summernote-lite.min.js', 'resources/js/specified.js'])
@endsection