<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) && $title!=null && $title!=''? $title.' - ':'' }}Щопочитайка</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Лого.png') }}">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">
    @vite(['resources/css/my_style.css'])   
    @yield('styles')
    <?php
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    ?>
</head>
<body>

    <div id="app" class="container h-100">
        <div class="container-fixed-width h-100 base-container" id="layout">
            <div class="overlay" id="overlay"></div>
            <div class="overlay" id="overlay-modal"></div>
            <div class="overlay" id="overlay-loading"><div class="loading-spinner"></div></div>

            {{-- Меню для малого екрану --}}
            <div class="md-yes" id="mobile_menu">

                {{-- Кнопочки --}}
                <div class="row mobile-nav">
                    <a href="/" class="col text-center icon-btn">
                        <img src="{{asset('/svg/home-light.svg')}}" class="small-icon">
                    </a>
                    <a href="{{ route('notifications') }}" class="col text-center icon-btn rel">
                        <img src="{{asset('/svg/notification-light.svg')}}" class="small-icon">
                        @if (Auth::user() && Auth::user()->hasUnreadNotifications())
                            <div class="has-notifications">{{Auth::user()->unreadNotificationsCount()}}</div>
                        @endif
                    </a>
                    @if (auth()->check())                                
                        <button class="col text-center show-menu-btn icon-btn" data-show-id="mobile-profile-menu">
                            <img src="{{asset('/svg/profile-light.svg')}}" class="small-icon">
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="col text-center icon-btn">
                            <img src="{{asset('/svg/profile-light.svg')}}" class="small-icon">
                        </a>                            
                    @endif
                    @if(!empty(trim($__env->yieldContent('aside'))) || isset($links))
                        <button class="col text-center show-menu-btn icon-btn" data-show-id="mobile-this-menu">
                            <img src="{{asset('/svg/menu-special-light.svg')}}" class="small-icon">
                        </button>
                    @endif
                    <button class="col text-center show-menu-btn icon-btn" data-show-id="mobile-main-menu">
                        <img src="{{asset('/svg/menu-light.svg')}}" class="small-icon">
                    </button>                        
                </div>

                {{-- Головне меню --}}
                <nav class="hide menu overlay-menu" id="mobile-main-menu">
                    @include('components.mobile-menu-header')
                    @foreach(config('menu.menu_links') as $link)
                        <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                        <div class="line"></div>
                    @endforeach
                    <a href="{{ route('logout') }}" class=""
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Вихід
                    </a>
                </nav>
                {{-- Поточне меню --}}
                <nav class="hide menu overlay-menu" id="mobile-this-menu">
                    @include('components.mobile-menu-header')
                    @if (isset($links))    
                        @foreach($links as $link)
                            <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                            <div class="line"></div>
                        @endforeach
                    @endif
                    @yield('aside')
                </nav>
                {{-- Профільне меню --}}
                <nav class="hide menu overlay-menu" id="mobile-profile-menu">
                    @include('components.mobile-menu-header')
                    @foreach(config('menu.profile_links') as $link)
                        <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                        <div class="line"></div>
                    @endforeach
                    @if (auth()->check() && auth()->user()->hasPermission('admin'))
                        <a href="{{ route('adminka') }}" class="">Адмінка</a>
                        <div class="line"></div>
                    @endif
                    <a href="{{ route('logout') }}" class=""
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Вихід
                    </a>
                </nav>
            </div>

            {{-- Для великого екрану --}}
            <div class="row h-100">

                {{-- Боковий блок --}}
                <aside class="w-320 col-auto md-no scroll-visible" id="aside">
                    <div class="p-3">
                        {{-- Лого і кнопочки --}}
                        <div id="round_buttons">
                            <a href="/"><img src="{{asset('images/Лого.png')}}" id="logo"></a>
                            <img src="{{asset('images/btn_profile.png')}}" id="profile_menu_btn" 
                            {{ (auth()->check())? 'data-user="logined"':'' }}>
                            <img src="{{asset('images/btn_menu.png')}}" id="main_menu_btn">
                            @if (Auth::user() && Auth::user()->hasUnreadNotifications())
                                <div class="has-notifications">{{Auth::user()->unreadNotificationsCount()}}</div>
                            @endif
                        </div>
                        <div class="line"></div>
                        <div class="line"></div>

                        {{-- Поточне меню --}}
                        <div class="menu" id="this_menu">
                            @if (isset($links))      
                                @foreach($links as $link)
                                    <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                                    <div class="line"></div>
                                @endforeach
                            @endif
                            @yield('aside')
                        </div>
                        {{-- Меню --}}
                        <div class="menu hide" id="main_menu">
                            @foreach(config('menu.menu_links') as $link)
                                <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                                <div class="line"></div>
                            @endforeach
                        </div>
                        {{-- Профільне меню --}}
                        <div class="menu hide" id="profile_menu">
                            @foreach(config('menu.profile_links') as $link)
                                <a href="{{ $link['url'] }}" class="">{{ $link['text'] }}</a>
                                <div class="line"></div>
                            @endforeach
                            @if (auth()->check() && auth()->user()->hasPermission('admin'))
                                <a href="{{ route('adminka') }}" class="">Адмінка</a>
                            @endif
                        </div>

                        {{-- Футер --}}
                        <footer class="text-center">
                            @if (auth()->check())
                                Вітаємо, {{Auth::user()->name}}!
                                <a href="{{ route('logout') }}" class="underline"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Вихід
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                {{-- <div class="line"></div>
                                <div>
                                    <a href="{{ route('user-request.form') }}">Форма зворотного зв'язку</a>
                                </div> --}}
                            @else
                                <a class="underline me-2" href="{{ route('login') }}">Вхід</a> |
                                <a class="underline" href="{{ route('register') }}">Рестрація</a>
                            @endif
                            <div class="d-flex gap-1 content-center mt-1 mb-3">
                                <div>© 2024 Щопочитайка</div>
                                <div>
                                    @php
                                        $currentLocale = App::currentLocale();
                                    @endphp
                                    <a href="{{ route('set-locale', 'uk') }}" @if($currentLocale == 'uk') class="underline" @endif>укр</a> |
                                    <a href="{{ route('set-locale', 'en') }}" @if($currentLocale == 'en') class="underline" @endif>en</a>
                                </div>
                            </div>
                        </footer>
                    </div>
                </aside>

                {{-- Головний блок --}}
                <main class="col h-100 rel" id="main">
                    <div id="toast" class="toast">
                        <div class="icon-box"></div>
                        <span class="message">Якесь повідомлення</span>
                    </div>
                    <img src="{{asset('images/BG.jpg')}}" class="bg-img">
                    <div class="h-100 scroll-visible rel" id="main_scroll"> 
                        <div class="p-4 mb-3 rel">                            
                            @yield('content')
                        </div>   
                    </div>
                </main>
            </div>

        </div>
    </div> 
    

    {{-- скрипти --}}
    <script src="https://kit.fontawesome.com/c04e65d013.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    @vite(['resources/js/most_used.js', 'resources/js/client.js'])   
    @yield('scripts')
    
    <script>
        //спливні сповіщення
        @if(session('error'))
            let session_error = "{{ session('error') }}";
        @endif
    
        @if(session('success'))
            let session_success = "{{ session('success') }}";
        @endif

        // початковий скрол до елемента
        @if(session('scroll-to'))
            let session_scroll_to = "{{ session('scroll-to') }}";
        @endif        
    </script>    
    
</body>
</html>