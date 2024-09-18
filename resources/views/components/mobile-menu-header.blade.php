<div class="no-base-menu-link mobile-menu-header">        
    @if (auth()->check())
        @include('components.user-item',['user' => auth()->user()])
    @else    
    <div class="text-center">        
        <a class="inline me-2" href="{{ route('login') }}">Вхід</a> |
        <a class="inline" href="{{ route('register') }}">Рестрація</a>
    </div>
    @endif
</div>