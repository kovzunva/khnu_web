@extends('layouts.app')

@section('content')	

	<header class="mb-3 rel">
		<form action="{{ route('works') }}" method="GET">
			<div class="row">	
				<div class="col pl-1 pr-1">
					<input type="search" id="search" data-table="works" placeholder="Пошук твору">
				</div>

				{{-- Сортування --}}
				@include('components.sort')

				{{-- Фільтрування --}}
				<div class="col-auto pl-1 pr-1">					
					<button type="button" class="base-btn" id="filter_btn">Фільтрувати</button>
				</div>
				<div class="w-100 hide" id="filter_container">
					<hr>
					<div class="row">
						<div class="col">								
							<label for="">Відсоток важливості</label>
							<input type="number" name="percent" class="small-number"
							value="{{ isset($service->filter->percent)? $service->filter->percent:'25' }}" min="0" max="100">	
						</div>		
						<div class="col-auto">							
							<button type="button" id="filter_clear_btn" class="ml-auto">Скинути опції</button>
							<button type="button" id="filter_hide_btn">Згорнути</button>
						</div>									
					</div>	
					<hr>

					{{-- Групи класифікації --}}
					@foreach ($service->classificator_groups as $group_name => $group)
					<b>{{$group_name}}</b>
					
					{{-- Опції --}}
					<ul class="ps-0">					
						@foreach ($group->options as $option)
							<li class="option-list"
							{{$option->change_id? 'data-change-id='.$option->change_id.'':''}}
							{{$option->change? 'data-change='.$option->id.'':''}}>	
							
								<div class="no-yes-group">
									<input type="checkbox" class="checkbox-no" id="no_opt{{$option->id}}" name="no_opt{{$option->id}}"
									value="no_opt{{$option->id}}" {{ in_array($option->id, $service->filter->no_options) ? 'checked' : '' }}>
									<label for="no_opt{{$option->id}}" class="checkbox-label"></label>
									
									<input type="checkbox" class="checkbox-yes" id="opt{{$option->id}}" name="opt{{$option->id}}"
									value="opt{{$option->id}}" {{ in_array($option->id, $service->filter->options) ? 'checked' : '' }}>
									<label for="opt{{$option->id}}" class="checkbox-label"></label>
									<label for="opt{{$option->id}}">{{$option->name}}</label>
								</div>	

								{{-- Підопції --}}
								@if (isset($option->suboptions))
									<ul class="ps-2">
									@foreach ($option->suboptions as $option)
									<li class="option-list"
									{{$option->change_id? 'data-change-id='.$option->change_id.'':''}}
									{{$option->change? 'data-change='.$option->id.'"':''}}>		
										<div class="no-yes-group">
											<input type="checkbox" class="checkbox-no" id="no_opt{{$option->id}}" name="no_opt{{$option->id}}"
											data-group="{{$group_name}}" value="no_opt{{$option->id}}">
											<label for="no_opt{{$option->id}}" class="checkbox-label"></label>
											
											<input type="checkbox" class="checkbox-yes" id="opt{{$option->id}}" name="opt{{$option->id}}"
											data-group="{{$group_name}}" value="opt{{$option->id}}">
											<label for="opt{{$option->id}}" class="checkbox-label"></label>
											<label for="opt{{$option->id}}">{{$option->name}}</label>
										</div>
									</li>
									@endforeach
									</ul>
								@endif
							</li>
						@endforeach
					</ul>
					@endforeach
				</div>
			</div>
		</form>
		<hr>
		@if ($service->filter->no_options || $service->filter->options)
			@if ($service->filter->no_options)
				<b>Не містить: </b>{{$service->filter->no_option_names}}<br>
			@endif
			@if ($service->filter->options)
				<b>Містить: </b>{{$service->filter->option_names}}<br>
			@endif
			<hr>
		@endif	
	</header>	
						
	@foreach($items as $item)
		@include('components.work-item',['item' => $item,])
	@endforeach
	{{ $service->paginator->count==0 ? "Нема творів за таким запитом":"" }}

	@include('components.paginator', ["paginator" => $service->paginator])

@endsection
	