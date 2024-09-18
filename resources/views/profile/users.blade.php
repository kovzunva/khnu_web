@extends('layouts.profile')

@section('page_title', $title_user_type)

@section('inner_content')

	<header class="mb-3">
		Всього {{ $user_type }}: {{$count}}
	</header>			
	@foreach ($users as $user)
		<a href="{{ route('profile', $user->id) }}" class="light-box mb-3 ">
			@include('components.user-item', ['user' => $user, 'another_link' => 1])
		</a>
	@endforeach

@endsection
