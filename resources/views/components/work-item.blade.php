<a href="{{ route('work',$item->id) }}" class="">
    <div class="with-image-box row mb-3">	
        <div class="col-sm-12 col-md-12 col-lg-2 d-flex align-items-center justify-content-center">
            <div class="rel with-image-box-imgs">
                @if ($item->img)						
                    <img src="{{asset($item->img)}}" alt="Зображення видання">
                @else
                    <div class="work-without-img">
                        <span>{!! $item->avtors !!} «{{$item->name}}»</span>						
                    </div>
                @endif	
            </div>
        </div>				
        <div class="col-sm-12 col-md-12 col-lg-10 d-flex align-items-center">	
            <div class="pl-3 pr-3">								
                <h5>{{ $item->avtors }} «{{ $item->name }}»</h5>
                <hr>
                {{ Str::limit($item->anotation, 160) }}
                <div class="mt-1 mb-1">
                    <b>{{ $item->options_info }}</b>
                </div>
                <div class="rating-container mt-3"
                    @if (isset($item->rating)) data-rating="{{$item->rating}}"  @endif>
                    Рейтинг: {{isset($item->rating)? number_format($item->rating, 2, '.', ''):'0'}}
                    @if(isset($item->orientator_rating))
                     | Рейтинг орієнтира: <b>{{number_format($item->orientator_rating, 2, '.', '')}}</b>
                    @endif
                </div>
            </div>
        </div>	
    </div>
</a>