@extends('layouts.profile')

@section('page_title', 'Книжкові полиці')

@section('inner_content')

    @if (Auth::user() && Auth::user()->id==$profile->id )            
        <form action="{{route('shelf.add')}}" method="POST" class="validate-form mb-3">
            @csrf
            <div class="input-group mb-3">
                <input type="text" class="input-with-btt" name='name'>
                <button class="btt-with-input" name="submit" type="submit">Додати</button>
            </div>
            <hr>
        </form>
    @endif

    @foreach ($shelves as $shelf)

    <a href="{{ route('shelf', $shelf->id) }}" class="">
        <div class="light-box row mb-2">	
            <div class="col">
                {{ $shelf->name }}
            </div>
            <div class="col-auto">
                (книг: {{ $shelf->works_count }})
            </div>
             
        </div>
    </a>

    @endforeach
    
@endsection	