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
                                <td>Заявки</td>
                                <td><a href="{{ route('my-profile-user-requests') }}" class="underline">{{ $statistic->data->user_requests }}</a></td>
                            </tr>
                        </tbody>
                    </table>
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

    @endif
    
@endsection	

@section('scripts')
	<script>
		let ratings_statistic_data = {!! json_encode($statistic->ratings) !!};
	</script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    @vite(['resources/js/charts.js'])
@endsection