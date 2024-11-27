@php
    $profile = $profile ?? Auth::user();
@endphp

<header class="with-image-box row mb-4">	
    <div class="col-sm-12 col-md-12 col-lg-1 d-flex align-items-center justify-content-center">
        <div class="rel with-image-box-imgs">		
            @if ($profile->ava()!="")                    
                <img src="{{asset($profile->ava())}}" alt="{{ mb_substr($profile->name,0,1) }}">
            @else
                <div class="work-without-img square text-42">{{mb_substr($profile->name,0,1)}}</div>
            @endif
            @if (auth()->user() && auth()->user()->id == $profile->id)
                <a href="{{ route('profile.editForm', $profile->id) }}">
                    <img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
                </a>
            @endif 
        </div>
    </div>				
    <div class="col-sm-12 col-md-12 col-lg-11 d-flex align-items-center">	
        <div class="pl-3 pr-2 w-100 rel">		
            <h1 class="align-center gap-10">
                {{$profile->name}}
                @if(!empty(trim($__env->yieldContent('page_title'))))                    
                    <span class="subtitle2">>> @yield('page_title')</span>
                @endif                
            </h1>
            @if (isset($profile) && auth()->user()->id!=$profile->id)
                <!-- Кнопка навігації -->							
                <div class="col-auto options-btn r-0">
                    <div class="custom-dropdown-btn">
                        <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
                    </div>
                    <div class="custom-dropdown-menu">
                        <a href="{{ route('profile',$profile->id) }}" class="dropdown-item">Профіль</a>
                        <a href="{{ route('profile-reviews',$profile->id) }}" class="dropdown-item">Відгуки</a>
                        <a href="{{ route('profile-blogs',$profile->id) }}" class="dropdown-item">Блоги</a>
                    </div>
                </div>	

                <!-- Бан для модератора -->
                @if (auth()->check() && auth()->user()->hasPermission('moderate'))	
                    <div class="text-end">
                        @if (Auth::user() && $profile->isBanned())
                            <a href="{{route('unban',$profile->id)}}" class="btn ml-1 ">Розбанити</a>
                        @else
                            <a href="{{route('ban',$profile->id)}}" class="btn ml-1 input-link " data-message="Введіть кількість днів:" data-input="3">Забанити</a>
                        @endif
                    </div>
                @endif	
            @else
                <!-- Кнопка навігації -->							
                <div class="col-auto options-btn r-0">
                    <div class="custom-dropdown-btn">
                        <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
                    </div>
                    <div class="custom-dropdown-menu">
                        @foreach(config('menu.profile_links') as $link)
                            <a href="{{ $link['url'] }}" class="dropdown-item">{{ $link['text'] }}</a>
                        @endforeach
                        @if (auth()->check() && auth()->user()->hasPermission('admin'))
                            <a href="{{ route('adminka') }}" class="dropdown-item">Адмінка</a>
                        @endif
                    </div>
                </div>	
            @endif
        </div>
    </в>	
</header>

{{-- <header class="row mb-3 rel">
    <!--  Чужий профіль -->
    @if (isset($profile) && auth()->user()->id!=$profile->id)
        <div class="col-auto p-none">           
            @if ($profile->ava()!="")                    
                <img src="{{asset($profile->ava())}}" class="profile-img" alt="{{ mb_substr($profile->name,0,1) }}">
            @else
                <div class="profile-img profile-no-img-letter">{{mb_substr($profile->name,0,1)}}</div>
            @endif                        
        </div>
        <div class="col p-none mt-auto">  	
            <div class="row">
                <div class="col pl-2">
                    <h4 class="">
                        {{$profile->name}}
                        @if(!empty(trim($__env->yieldContent('page_title'))))
                            >>
                        @endif
                        @yield('page_title')
                    </h4>
                </div>
                <div class="col-auto text-end d-flex align-items-center justify-content-center"> 

                    @if (Auth::user() && 
                    (!Auth::user()->isBanned() || (Auth::user()->isFollowing($profile) && $profile->isFollowing(Auth::user()))))	
                    <a href="/chat/to/{{$profile->id}}" class="mr-1">Написати</a>
                    @endif
                    
                    @if (Auth::user() && Auth::user()->isFollowing($profile))
                        @if (Auth::user()->isFollowing($profile))
                            <a href="/unfollow/{{$profile->id}}" class="btn ml-1 ">Не стежити</a>
                        @else
                            <a href="/follow/{{$profile->id}}" class="btn ml-1 ">Стежити</a>
                        @endif
                    @endif

                    <!-- Бан для модератора -->
                    @if (auth()->check() && auth()->user()->hasPermission('moderate'))	
                        @if (Auth::user() && $profile->isBanned())
                            <a href="{{route('unban',$profile->id)}}" class="btn ml-1 ">Розбанити</a>
                        @else
                            <a href="{{route('ban',$profile->id)}}" class="btn ml-1 input-link "
                                data-message="Введіть кількість днів:" data-input="3">Забанити</a>
                        @endif
                    @endif	
                
                </div>
            </div>   

            <!-- Кнопка навігації -->							
            <div class="col-auto options-btn r-0">
                <div class="custom-dropdown-btn">
                    <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    <a href="{{ route('profile',$profile->id) }}" class="dropdown-item">Профіль</a>
                    <a href="{{ route('profile-reviews',$profile->id) }}" class="dropdown-item">Відгуки</a>
                    <a href="{{ route('profile-blogs',$profile->id) }}" class="dropdown-item">Блоги</a>
                </div>
            </div>	
            <hr class="profile-line">
        </div>      
          
    @else <!--  Профіль поточного користувача -->
        <div class="col-auto p-none rel">                             
            @if (Auth::user()->ava()!="")                    
                <img src="{{asset(Auth::user()->ava())}}" class="profile-img" alt="{{ mb_substr(Auth::user()->name,0,1) }}">
            @else
                <div class="profile-img profile-no-img-letter">{{mb_substr(Auth::user()->name,0,1)}}</div>
            @endif    
            <a href="{{ route('profile.editForm', Auth::user()->id) }}">
                <img src="{{asset('/svg/edit.svg')}}" class="icon admin-icon">
            </a>                 
        </div>
        <div class="col p-none mt-auto">  	
            <div class="row">
                <div class="col pl-2">
                    <h4>
                        {{Auth::user()->name}}
                        @if(!empty(trim($__env->yieldContent('page_title'))))
                            >>
                        @endif
                        @yield('page_title')
                    </h4>
                </div>
            </div>  

            <!-- Кнопка навігації -->							
            <div class="col-auto options-btn r-0">
                <div class="custom-dropdown-btn">
                    <img src="{{asset('/svg/elipsis.svg')}}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    @foreach(config('menu.profile_links') as $link)
                        <a href="{{ $link['url'] }}" class="dropdown-item">{{ $link['text'] }}</a>
                    @endforeach
                    @if (auth()->check() && auth()->user()->hasPermission('admin'))
                        <a href="{{ route('adminka') }}" class="dropdown-item">Адмінка</a>
                    @endif
                </div>
            </div>	
            <hr class="profile-line">
        </div>
    @endif
</header> --}}