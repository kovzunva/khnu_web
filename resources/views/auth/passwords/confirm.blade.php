@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Підтвердження пароль</div>

        <div class="card-body">
            Для продовження підтвердіть ваш пароль.

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Пароль</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            Підтвердити
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                Забули пароль?
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
