@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Реєстрація</div>

        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">Псевдонім</label>

                    <input id="name" type="text" class="@error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Електронна адреса</label>

                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Пароль</label>

                    <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Підтвердіть пароль</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="text-end">
                    <a class="btn " href="{{ route('login') }}">Вхід</a>
                    <button type="submit" class="ml-2 base-btn">
                        Зареєструватися
                    </button>
                </div>
            </form>
        </div>
    </div> 
@endsection
