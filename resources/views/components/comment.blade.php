<div class="comment-item" data-comment-id="{{ $comment->id }}" id="comment{{ $comment->id }}">
    <div class="user-content-box mb-3 rel">
        <div class="user-part">
            @include('components.user-item-big',['user' => $comment->user])
            <div class="mb-1">{{ \Carbon\Carbon::parse($comment->created_at)->format('d.m.y H:i') }}</div>
            @if ($comment->updated_at)
                <div>змінено</div>
            @endif							
        </div>
        <div class="content-part">
            <div class="comment-text h-100 w-100">
                <div class="flex-column h-100">
                    {{-- Якщо це відповідь --}}
                    @if ($comment->comment)	
                        <a href="#comment{{$comment->comment->id}}" class="to-answer-link">					
                            <div class="to-answer-text mb-1">
                                {{ Str::limit($comment->comment->content, 100) }}
                            </div>
                        </a>	
                    @endif
                
                    <div>
                        <p>{{ $comment->content }}</p>
                        @if (!Auth::user()->isBanned())
                        @endif
                    </div>
    
                    <div class="to-bottom text-end">										
                        <a class="answer-comment-btn underline" data-comment-id="{{ $comment->id }}">Відповісти</a>
                    </div>
                </div>
            </div>
    
            {{-- редагування коментаря --}}
            @if ($comment->user_id === auth()->id())	
                <form class="edit-comment-form hide mt-2 w-100" action="{{ route('comment.edit',$comment->id) }}" method="POST" style="display: none;">
                    @csrf
                    <textarea name="edit_content">{{ $comment->content }}</textarea>
                    <div class="text-end mt-2">
                        <button type="submit" class="base-btn">Зберегти</button>
                    </div>
                </form>
            @endif
        
        </div>
           
        <!-- Кнопка налаштувань -->	
        @if (auth()->check() && $comment->user_id === auth()->id())						
            <div class="options-btn">
                <div class="custom-dropdown-btn">
                    <img src="{{ asset('svg/elipsis.svg') }}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    @if (!Auth::user()->isBanned())
                    <a class="dropdown-item edit-comment-btn " data-comment-id="{{ $comment->id }}">Редагувати</a>
                    @endif
                    <a class="dropdown-item confirm-link " href="/comment/{{$comment->id}}/del"
                    data-message="Ви впевнені, що хочете видалити цей коментар?">Видалити</a>
                </div>
            </div>
        <!-- Кнопка налаштувань для модератора -->
        @elseif (auth()->check() && auth()->user()->haspermission('moderate'))												
            <div class="options-btn">
                <div class="custom-dropdown-btn">
                    <img src="{{ asset('svg/elipsis.svg') }}" class="icon">
                </div>
                <div class="custom-dropdown-menu">
                    <a class="dropdown-item input-link " href="/comment/{{$comment->id}}/del"
                        data-message="Введіть причину видалення:">Видалити</a>
                </div>
            </div>
        @endif	
    </div>
    
    {{-- відповідь на коментар --}}
    <form class="answer-comment-form hide mt-1 mb-3 w-100" action="/comment/add" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item_id }}">
        <input type="hidden" name="item_type" value="{{$type}}"> {{-- Тип --}}
        <input type="hidden" name="answer_to" value="{{ $comment->id }}">
        <textarea class="mt-2" name="content"></textarea>
        <div class="text-end mt-1">
            <button type="submit" class="base-btn">Відповісти</button>
        </div>
    </form>
</div>