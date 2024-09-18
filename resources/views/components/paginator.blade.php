{{-- Пагінатор --}}
@php    
    $get_parameters = $paginator->request->query() + $paginator->params; 
    unset($get_parameters['page']);
    $params = '&' . http_build_query($get_parameters);
    $url = request()->url();

    if ($paginator->page < 1) $cur_page = 1;
    else if ($paginator->page > $paginator->pages) $cur_page = $paginator->pages;
    else $cur_page = $paginator->page;
    $start = max($cur_page - $paginator->num_links, 1);
    $end = min($cur_page + $paginator->num_links, $paginator->pages);
@endphp

@if($paginator->pages > 1)
    <div class="paginator">
        {{-- Посилання на першу сторінку --}}
        @if($cur_page > ($paginator->num_links + 1))
            <a href="{{ $url . '?page=1' . $params }}" title="Перша сторінка"> << </a>
        @endif

        {{-- Посилання на попередню сторінку --}}
        @if($cur_page != 1)
            <a href="{{ $url . '?page='. ($cur_page - 1) . $params }}" title="Попередня сторінка"><</a>
        @endif

        {{-- Посилання на сторінки --}}
        @for($loop = $start; $loop <= $end; $loop++)
            @if($cur_page == $loop)
                <b>{{ $loop }}</b>
            @else
                <a href="{{ $url . '?page='. $loop . $params }}" class="page-link">{{ $loop }}</a>
            @endif
        @endfor

        {{-- Посилання на наступну сторінку --}}
        @if($cur_page < $paginator->pages)
            <a href="{{ $url . '?page='. ($cur_page + 1) . $params }}" title="Наступна сторінка">></a>
        @endif

        {{-- Посилання на останню сторінку --}}
        @if(($cur_page + $paginator->num_links) < $paginator->pages)
            <a href="{{ $url . '?page='. $paginator->count . $params }}" title="Остання сторінка"> >> </a> 
            <i title="Всього сторінок">({{ $paginator->pages }})</i>
        @endif
    </div>
@endif
