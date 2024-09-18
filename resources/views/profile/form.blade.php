@extends('layouts.profile')

@section('page_title', 'Редагування профілю')

@section('inner_content')

    <form action="/profile/edit" method="POST" id="profile-form"
        class="validate-form">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Псевдонім</label>
            <input readonly type="text" value="{{ Auth::user() ? Auth::user()->name : '' }}">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Електронна пошта</label>
            <input readonly type="text" value="{{ Auth::user() ? Auth::user()->email : '' }}">
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Про Вас</label>
            <textarea name="about" rows="4">{{ Auth::user() ? Auth::user()->about : '' }}</textarea>
        </div>  

        {{-- Картинка --}}
        <div class="mb-3">
            <label for="" class="form-label">Аватарка</label>

            <div class="row">
                <div class="img_preview col-auto" id="container_img">                                   
                    <img src="{{ Auth::user() && $img_edit? asset($img_edit):'' }}" id='edit_img' alt="{{ mb_substr(Auth::user()->name,0,1) }}">
                </div>
                <div class="col">
                    <div class="mb-3">
                        <input type="file" id="file_img_1" accept="image/*">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="input-with-btt enter_btn" id="url_img_1" placeholder="URL зображення">
                        <button class="btn btn-outline-secondary btt-with-input" type="button" id="btn_url_img_1">Додати</button>
                    </div>
                </div>
            </div>

            <div class="container mb-3">
                <input type="hidden" name="img_pass" value="" id="img_pass">       
            </div>
        </div>                        

        <div class='text-end'>
            <input type="submit" name="submit" class="base-btn" value="Внести зміни">
        </div>
    </form>    

<script src="https://cdn.tiny.cloud/1/tatyegaul1dl88btgari4jz7c7st2hz44mxb4kck1c4rvzip/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> 


@endsection
	