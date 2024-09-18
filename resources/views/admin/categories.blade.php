@extends('layouts.admin')

@section('inner_content')	

<div class="header-box">
    <h1>Категорії</h1>
</div>

{{-- Жанри --}}
<details id="genre_details">
    <summary><b>Жанри</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','genre')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($genres as $item)
        
        <form action="{{route('category.edit',['genre',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['genre',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

{{-- Мови --}}
<details id="language_details">
    <summary><b>Мови</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','language')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($languages as $item)
        
        <form action="{{route('category.edit',['language',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['language',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

{{-- Країни --}}
<details id="country_details">
    <summary><b>Країни</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','country')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($countries as $item)
        
        <form action="{{route('category.edit',['country',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['country',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

{{-- Категорії блогу --}}
<details id="blog_categories_details">
    <summary><b>Категорії блогу</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','blog_categories')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($blog_categories as $item)
        
        <form action="{{route('category.edit',['blog_categories',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['blog_categories',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

{{-- Тематика форуму --}}
<details id="forum_tematics_details">
    <summary><b>Тематика форуму</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','forum_tematics')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($forum_tematics as $item)
        
        <form action="{{route('category.edit',['forum_tematics',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['forum_tematics',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

{{-- Типи обкладинки --}}
<details id="type_of_cover_details">
    <summary><b>Типи обкладинки</b></summary>                
    <div class="mb-3 mt-1">
        <form action="{{route('category.add','type_of_cover')}}" method="POST" class="validate-form">
            @csrf
            <div class="input-group mb-4">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
        </form>
            
        @foreach ($types_of_cover as $item)
        
        <form action="{{route('category.edit',['type_of_cover',$item->id])}}" method="POST" class="validate-form">
            @csrf
            <div class="el-inserted input-group mb-1">
                <input readonly type="text" class="hide" name="rezult_id[]" value="{{ $item->id }}">
                <input type="text" class="form-control input-with-btt" name="name" value="{{ $item->name }}">
                <button type="submit" name="submit" class="border-radius-0 br-none">Редагувати</button>
                <a class=" confirm-link" href="{{route('category.del',['type_of_cover',$item->id])}}"
                data-message="Ви впевнені, що хочете видалити категорію «{{$item->name}}»?">
                    <button type="button" class="btt-with-input">Видалити</button>                            
                </a>
            </div>
        </form>

        @endforeach
    </div>
</details>

<script>
    let detailsElement = document.getElementById("{{ session('table') }}_details");
</script>
@endsection
	