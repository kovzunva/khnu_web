<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseItemService
{
    // константи
    const PER_PAGE_BASE  = 20;
    const NUM_LINKS  = 2;

    // загальне
    protected $table = "";
    protected $take_columns = "id, name";

    // сортування
    public $sort_options = [];
    public $sort = null;

    // фільтрування
    public $filter = null;

    // пагінація
    public $paginator = null;

    public function GetItemsBase(Request $request, bool $paginate = true){
        return DB::table($table)->get();
    }

    public function GetItems(Request $request, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        $this->sort = $this->GetOrderBy($request);
        $this->filter = $this->GetFilters($request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page):null;

        return DB::select("select $this->take_columns from $this->table "
            .($this->filter? $this->filter->sql:"")
            .$this->sort->sql
            .$this->paginator->sql);
    }

    public function GetItem($id, Request $request = null){
        return DB::table($table)->where('id', $id)->first();
    }

    // об'єкт сортування
    public function GetOrderBy(Request $request){
        $id = $request->has('sort_id') ? $request->input('sort_id') : array_key_first($this->sort_options);
        $direction = $request->has('sort_direction') ? $request->input('sort_direction') : 'DESC';
        $sql = ' ORDER BY ';

        if (array_key_exists($id, $this->sort_options)) {
            $sortOption = $this->sort_options[$id];
            $name = $sortOption['name'];
            $sql .= $sortOption['sql'];
        } else {
            $sortOption = reset($this->sort_options);
            $name = $sortOption['name'];
            $sql .= $sortOption['sql'];
        }
        $sql .= ' ' . $direction;

        $sort = new \stdClass();
        $sort->sql = $sql;
        $sort->name = $name;
        $sort->direction = $direction;
        $sort->id = $id;

        return $sort;
    }

    public function GetFilters(Request $request){
        
    }

    // об'єкт пагінації
    public function GetPaginator(Request $request,$per_page,$count=-1,$params = []){
        $paginator = new \stdClass();
        $paginator->request = $request;
        $paginator->num_links = self::NUM_LINKS;
        $paginator->count = $count!=-1? $count:$this->GetCount();
        $paginator->pages = ceil($paginator->count/$per_page);
        if ($paginator->pages<1) $paginator->pages = 1;
        $paginator->params = $params;

        $paginator->page = $request->has('page') ? $request->input('page') : 1;
        if ($paginator->page<1) $paginator->page = 1;
        if ($paginator->page > $paginator->pages) $paginator->page = $paginator->pages;
        $offset = ($paginator->page - 1) * $per_page;
        $paginator->sql = " LIMIT $offset, $per_page ";

        return $paginator;       
    }

    // кількість
    public function GetCount(){
        $result = DB::select("select count(*) as count from $this->table ".($this->filter? $this->filter->sql:""));
        return $result && count($result)>0? $result[0]->count:0; 
    }

    public function CanEdit($item){
        $can_edit = null; 
        $user = auth()->user();
        if ($user){
            $admin = $user->hasPermission('admin');
            $content_make = $user->hasPermission('content-make'); 
            $can_edit = ($item->user_id==$user->id || $admin || $content_make && !$item->is_public); 
        }
        if (!$item->is_public){
            if (!$user) return abort(404);   
            if (!($item->user_id==$user->id || $admin || $content_make && !$item->is_public) ) return abort(404);
        }

        return $can_edit;
    }

    public function StringToSearchSql($term){
        
    }
    
    public function TruncateText($text, $limit = 100) {
        $text = str_replace(["\r\n", "\n", "\r"], ' ', $text);
        
        if (Str::length($text) <= $limit) {
            return $text;
        }
        
        $trimmedText = Str::substr($text, 0, $limit);
        $trimmedText = Str::substr($trimmedText, 0, Str::length($trimmedText) - Str::length(Str::afterLast($trimmedText, ' ')));
        return rtrim($trimmedText) . '...';
    }
    
}
