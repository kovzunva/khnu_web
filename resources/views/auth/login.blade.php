@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Вхід</div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Електронна адреса</label>
                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Пароль</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Запам'ятати мене
                        </label>
                    </div>
                </div>

                <div class="text-end">                    
                    <a class="btn " href="{{ route('register') }}">Реєстрація</a>
                    @if (Route::has('password.request'))
                        <a class="btn " href="{{ route('password.request') }}">
                            Забули свій пароль?
                        </a>
                    @endif
                    <button type="submit" class="ml-2 base-btn">
                        Увійти
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
