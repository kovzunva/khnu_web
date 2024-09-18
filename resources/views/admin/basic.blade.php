@extends('layouts.admin')

@section('inner_content')	

	<div class="header-box">
		<h1>Адмінка</h1>
	</div>

	<p>Вас вітає Адмінка! Якщо ви сюди потрапили, то тут одне з двох: ви або обрані, або потрапили сюди випадково (вийди звідси, зловмисник).</p>

	<section class="small-section">
		<header>
			<h5>Статистична інформація по сайту</h5>
		</header>
		<table class="two-center">
			<thead>					
				<th>Сутності</th>
				<th>Кількість</th>
			</thead>
			<tbody>
				<tr>
					<td>Користувачі</td>
					<td>{{ $data->users }}</td>
				</tr>
				<tr>
					<td>Відгуки</td>
					<td>{{ $data->reviews }}</td>
				</tr>
				<tr>
					<td>Оцінки</td>
					<td>{{ $data->ratings }}</td>
				</tr>
				<tr>
					<td>Блоги</td>
					<td>{{ $data->blogs }}</td>
				</tr>
				<tr>
					<td>Персони</td>
					<td>{{ $data->persons }}</td>
				</tr>
				<tr>
					<td>Твори</td>
					<td>{{ $data->works }}</td>
				</tr>
				<tr>
					<td>Видання</td>
					<td>{{ $data->editions }}</td>
				</tr>
				<tr>
					<td>Видавництва</td>
					<td>{{ $data->publishers }}</td>
				</tr>
			</tbody>
		</table>
	</section>
@endsection
	