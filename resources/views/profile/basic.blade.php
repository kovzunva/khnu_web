@extends('layouts.profile')

@section('inner_content')
    @if ($profile->isBanned() && (auth()->user()==$profile || auth()->user()->hasPermission('moderate')))
    <div class="div-error mb-3">
        Забанено до {{$profile->banned_until}}
    </div>
    @endif
    <p>{{$profile->about}}</p>
    @if (!$profile->about)
        <p>Користувач шифрується і нічого про себе не розказує.</p>
    @endif

    @if ($profile->id==auth()->user()->id)        
        <section class="small-section">
            <header>
                <h5>Статистична інформація</h5>
            </header>
            <div class="row gap-10">
                <div class="col-auto">
                    <table class="two-center">
                        <thead>					
                            <th>Сутності</th>
                            <th>Кількість</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Відгуки</td>
                                <td><a href="{{ route('my-profile-reviews') }}" class="underline">{{ $statistic->data->reviews }}</a></td>
                            </tr>
                            <tr>
                                <td>Оцінки</td>
                                <td><a href="{{ route('my-ratings') }}" class="underline">{{ $statistic->data->ratings }}</a></td>
                            </tr>
                            <tr>
                                <td>Блоги</td>
                                <td><a href="{{ route('my-blogs') }}" class="underline">{{ $statistic->data->blogs }}</a></td>
                            </tr>
                            <tr>
                                <td>Полички</td>
                                <td><a href="{{ route('my-profile-shelves') }}" class="underline">{{ $statistic->data->shelves }}</a></td>
                            </tr>
                            <tr>
                                <td>Відстежувачі</td>
                                <td><a href="{{ route('followers') }}" class="underline">{{ $statistic->data->followers }}</a></td>
                            </tr>
                            <tr>
                                <td>Відстежуються</td>
                                <td><a href="{{ route('following') }}" class="underline">{{ $statistic->data->following }}</a></td>
                            </tr>
                            <tr>
                                <td>Орієнтири</td>
                                <td><a href="{{ route('orientators') }}" class="underline">{{ $statistic->data->orientators }}</a></td>
                            </tr>
                            <tr>
                                <td>Заявки</td>
                                <td><a href="{{ route('my-profile-user-requests') }}" class="underline">{{ $statistic->data->user_requests }}</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col text-center">
                    <hr>
                    <hr>
                    <a href="{{ route('recommendations') }}" class="base-btn ">Отримати рекомендації</a>
                    <hr>
                    <hr>
                </div>
            </div>
        </section>

        <section class="small-section">
            <header>
                <h5>Діаграма за оцінками</h5>
            </header>
            <div class="max-width-800">
                <canvas id="ratings_statistic"></canvas>
            </div>

        </section>
    @else             
        <section class="small-section">
            <header>
                <h5>Орієнтирність</h5>
            </header>
            <table class="two-center">
                <thead>					
                    <th></th>
                    <th>Значення</th>
                    <th>Повних збігів</th>
                    <th>Значних не збігів</th>
                    <th>Незначних не збігів</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Кількість</td>
                        <td><b>{{ $orientations->exact_match - $orientations->major_mismatch }}</b></td>
                        <td>{{ $orientations->exact_match }}</td>
                        <td>{{ $orientations->major_mismatch}}</td>
                        <td>{{ $orientations->minor_mismatch }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-2">                
                <a href="{{ route('orientator', $profile->id) }}" class="base-btn ">
                    @if (auth()->user()->isOrientator($profile->id))
                        Викинути з орієнтирів
                    @else
                        Зробити своїм орієнтиром
                    @endif
                    
                </a>
            </div>
        </section>
  
        <section class="small-section">
            <header>
                <h5>Найбільші збіги</h5>
            </header>
            @foreach ($orientations->topMatches as $work)
                <a href="{{ route('work',$work->id) }}" class="light-box mb-3 row">
                    <div class="col">
                        {{ $work->avtors }} «{{ $work->name }}»
                    </div>
                    <div class="col-auto">
                        Оцінка: {{ $work->user1_rating }} | Ваша оцінка: {{ $work->user2_rating }}
                    </div>
                </a>
            @endforeach
            @if (count($orientations->topMatches)==0)
                <p>Немає таких.</p>
            @endif
        </section>
  
        <section class="small-section">
            <header>
                <h5>Найбільші незбіги</h5>
            </header>
            @foreach ($orientations->worstMismatches as $work)
                <a href="{{ route('work',$work->id) }}" class="light-box mb-3 row">
                    <div class="col">
                        {{ $work->avtors }} «{{ $work->name }}»
                    </div>
                    <div class="col-auto">
                        Оцінка: {{ $work->user1_rating }} | Ваша оцінка: {{ $work->user2_rating }}
                    </div>
                </a>
            @endforeach
            @if (count($orientations->worstMismatches)==0)
                <p>Немає таких.</p>
            @endif
        </section>

    @endif
    
@endsection	

@section('scripts')
	<script>
		let ratings_statistic_data = {!! json_encode($statistic->ratings) !!};
	</script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    @vite(['resources/js/charts.js'])
@endsection