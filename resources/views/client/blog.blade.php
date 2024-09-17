@extends('layouts.app')

@section('content')    

    <div class="header-box rel">
        <h1>{{ $blog->name }}</h1> 
        <div class="row align-center">
            <div class="col">
                <div>{{ $blog->created_at->format('d.m.Y H:i:s') }}</div> 
                <div class="ms-1">{{ $blog->category ? '#'.$blog->category->name : '' }}</div> 
            </div>
            <div class="col-auto">
                @include('components.user-item',['user' => $blog->user])
            </div>
        </div>

        {{-- Кнопка налаштувань --}}
        @if ($blog->user_id === auth()->id())
            <div class="options-btn">                    
                <div class="custom-dropdown-btn">
                    <img src="{{ asset('svg/elipsis.svg') }}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    @if (!Auth::user()->isBanned())
                    <a class="dropdown-item" href="{{route('blog.editForm',$blog->id)}}">{{ __('Редагувати') }}</a>
                    @endif
                    <a class="dropdown-item confirm-link" href="{{route('blog.del',$blog->id)}}"
                    data-message="{{ __('Ви впевнені, що хочете видалити блог «'.$blog->title.'»?') }}">{{ __('Видалити') }}</a>
                </div>
            </div>
        @endif  
        
        <!-- Кнопка налаштувань для модератора -->    
        @if (auth()->check() && auth()->user()->hasPermission('moderate') && $blog->user_id != auth()->id())                                                
            <div class="options-btn">
                <div class="custom-dropdown-btn">
                    <img src="{{ asset('svg/elipsis.svg') }}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    <a class="dropdown-item input-link" href="{{route('blog.del',$blog->id)}}"
                        data-message="{{ __('Введіть причину видалення:') }}">{{ __('Видалити') }}</a>
                </div>
            </div>
        @endif  
    </div>

    <div class="text-from-editor mt-3 classic-links">
        {!! $blog->content !!} 
    </div>

    <section class="mt-5">
        <header class="header-box row">
            <h2 class="subtitle1 col">{{ __('Коментарі') }}</h2>
            <div class="col-auto">
                {{-- Вподобайки та кількість коментів --}}
                <div class="text-end">        
                    <div class="like-group">
                        
                        <span class="count-span" title="{{ __('Коментарі') }}">
                            <span>{{ $blog->comments->count() }}</span> 
                            <i class="@if (Auth::check() && $blog->comments->where('user_id', Auth::user()->id)->count() > 0) fa-solid @else fa-regular @endif fa-comment me-2"></i>
                        </span>

                        <input type="hidden" name="item_id" value="{{ $blog->id }}">
                        <input type="hidden" name="item_type" value="blog">
                        @php
                            $liked = Auth::check() && $blog->likes->where('user_id', Auth::user()->id)->where('item_type', 'blog')->count() > 0;
                        @endphp
                        <span class="count-span" title="@if ($liked) {{ __('Зняти вподобайку') }} @else {{ __('Вподобати') }} @endif">
                            <span class="likes-count">{{ $blog->likes->count() }}</span> 
                            <i class="@if ($liked) fa-solid @else fa-regular @endif fa-heart like-btn"></i>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <form method="POST" action="/comment/add" class="validate-form mb-4">
            @csrf
            <input type="hidden" name="item_id" value="{{ $blog->id }}">
            <input type="hidden" name="item_type" value="1">

            <div class="form-group">
                <div class="error-text hide" id="error_content">{{ __('Заповніть поле') }}</div>
                <textarea class="required" id="comment" name="content" rows="4"></textarea>
            </div>
            
            <div class="text-end mt-2">                            
                <button type="submit" class="btn base-btn">{{ __('Опублікувати коментар') }}</button>
            </div>
        </form>

        <!-- Виведення коментарів -->
        {{ count($blog->comments) > 0 ? '' : __('Ще нема коментарів') }}
        @foreach ($blog->comments as $comment)
            @include('components.comment',['type' => 1, 'item_id' => $blog->id])
        @endforeach
    </section>
    
@endsection
