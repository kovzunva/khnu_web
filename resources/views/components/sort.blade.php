{{-- Сортування --}}
<div class="col-auto pl-1 pr-1">	
    <div class="row">							
        <div class="col">
            <div class="base-select w-auto">
                <div class="select-box">
                <span class="selected-option d-flex align-items-center">{{ $service->sort->name ? $service->sort->name : 'За рейтингом' }}</span>
                <ul class="options hide">									
                    @foreach($service->sort_options as $index => $option)
                        <li data-value="{{ $index }}" class="change-to-submit">{{ $option["name"] }}</li>
                    @endforeach
                </ul>
                </div>
                <input type="hidden" name="sort_id" value="{{ $service->sort->id ? $service->sort->id : '1' }}">
            </div> 
        </div>						
        <div class="col-auto">
            <div class="input-group">
                <input type="radio" name="sort_direction" id="sort_direction_asc" value="ASC" {{$service->sort->direction=="ASC"? 'checked':''}}
                class="radio-sort change-to-submit">
                <label for="sort_direction_asc" class="label-sort">⭡</label>
                <input type="radio" name="sort_direction" id="sort_direction_desc" value="DESC" {{$service->sort->direction=="DESC"? 'checked':''}}
                class="radio-sort change-to-submit">
                <label for="sort_direction_desc" class="label-sort">⭣</label>
            </div>
        </div>
    </div>  
</div>