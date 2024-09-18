@extends('layouts.content-maker')

@section('inner_content')	

	<div class="header-box">
		<h1>Майстерня</h1>
	</div>

	<div>
		<p>Вас вітає Майстерня! Якщо на Щопочитайці немає авторів, творів, видань чи видавництв, які вас цікавлять <i>(але вони існують не тільки у вашій голові)</i>, то ви можете додати їх власноруч.</p>
		<p>Але якщо ви потрапили сюди вперше і ще не читали довідкову інформацію про це, то одумайтесь і прочитайте (будь ласка, будь ласочка, хоч краєм ока).
			<a href="{{ route('faq',6) }}">Тицяйте сюди</a>.</p>
		<ul>
			<li><a href="{{ route('person.showAllMy') }}">Персони</a></li>
			<li><a href="{{ route('work.showAllMy') }}">Твори</a></li>
			<li><a href="{{ route('edition.showAllMy') }}">Видання</a></li>
			<li><a href="{{ route('publisher.showAllMy') }}">Видавництва</a></li>
		</ul>
		@if (!auth()->user()->hasPermission('content-make'))
            <div class="div-info">
                Оскільки ви не Укладач вмісту, то створені вами матеріали будуть непублічні й видимі лише для вас, поки їх не затвердить Укладач вмісту.
            </div>
        @endif
	</div>

	<section class="small-section">
		<header>
			<h5>Ваша укладацька активність</h5>
		</header>
		<div class="chart-container">
			<canvas id="content_maker_activity_on_types" class="chart"></canvas>
		</div>
	</section>

@endsection
	
@section('scripts')
	<script>
		let content_maker_activity_on_types_data = {!! json_encode($content_maker_activity_on_types) !!};
	</script>
	{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    @vite(['resources/js/charts.js'])
@endsection