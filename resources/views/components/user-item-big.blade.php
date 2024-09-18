@if (!isset($another_link))  
    <a href="{{ route('profile', $user->id)}}" class=" user-item user-item-big"> 
@endif 


@if ($user->ava()!='')    
    <img src="{{ asset($user->ava()) }}" alt="{{ mb_substr($user->name,0,1) }}" class="ava ava-image">
@else
    <div class="ava">{{ mb_substr($user->name,0,1) }}</div>
@endif

<div class="mt-1 mb-1">{{ $user->name }}</div>
    
@if (!isset($another_link))  
    </a>
@endif     