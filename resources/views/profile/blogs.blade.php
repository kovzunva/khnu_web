@extends('layouts.profile')

@section('page_title', 'Блоги')

@section('inner_content')

	<header>
		@if ($profile==Auth::user())
			@if (!Auth::user()->isBanned())
				<a href="/profile/blog" class="transparent-btn mb-3 ">
					Додати блог
				</a>
			@else
				<div class="div-error mb-3">
					Вас забанено. Ви не можете створювати блоги до {{Auth::user()->banned_until}}
				</div>
			@endif
		@endif
	</header>			
	@foreach ($blogs as $blog)
		<div class="light-box mb-3">
			<header class="rel">
				<a href="{{ route('blog',$blog->id) }}" class="">
					<div class="subtitle2 me-5 mb-2">{{ $blog->name }}</div>
				</a>
				<!-- Кнопка налаштувань -->		
				@if ($profile==Auth::user())								
					<div class="options-btn">
						<div class="custom-dropdown-btn">
							<img src="{{ asset('svg/elipsis.svg') }}" class="icon">
						</div>
						<div class="custom-dropdown-menu">
							@if (!Auth::user()->isBanned())
							<a class="dropdown-item " href="{{route('blog.editForm',$blog->id)}}">Редагувати</a>
							@endif
							<a class="dropdown-item  confirm-link" href="{{route('blog.del',$blog->id)}}"
							data-message="Ви впевнені, що хочете видалити блог «{{ $blog->title }}»?">Видалити</a>
						</div>
					</div>
				@endif	
                <!-- Кнопка налаштувань для модератора -->	
                @if (auth()->check() && auth()->user()->hasPermission('moderate') && $profile->id != auth()->id())												
                    <div class="options-btn">
                        <div class="custom-dropdown-btn">
                            <img src="{{ asset('svg/elipsis.svg') }}" class="icon">
                        </div>
                        <div class="custom-dropdown-menu">
                            <a class="dropdown-item  input-link" href="{{route('blog.del',$blog->id)}}"
                                data-message="Введіть причину видалення:">Видалити</a>
                        </div>
                    </div>
                @endif	
			</header>
			
			<a href="/blog/{{$blog->id}}" class="">
				<div class="pb-2">
					{!! Str::limit(strip_tags($blog->content), 200) !!}

					@if (strlen($blog->content) > 200)
						<span>Читати більше</span>
					@endif
				</div>
				<div>
					<span>{{ $blog->created_at->format('d.m.Y H:i:s') }}</span>
					<b class="ms-1">{{ $blog->category? '#'.$blog->category->name:'' }}</b>
				</div>
			</a>	
		</div>
	@endforeach

@endsection
