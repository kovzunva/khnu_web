@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Підтвердження електронної адреси</div>
        <div class="card-body">
            @if (auth()->user()->email_verified_at)
                Верифікацію пройдено успішно
            @else                    
                <div class="alert alert-success mb-3" role="alert">
                    Посилання для верифікації було надіслано на вашу електронну адресу.
                </div>
                <form class="text-end" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn">Тицьніть, щоб переслати ще раз</button>
                </form>
            @endif
        </div>
    </div>
@endsection
