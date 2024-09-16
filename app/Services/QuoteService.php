<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class QuoteService extends BaseItemService 
{
    public $table = "quote";
    public $sort_options = [
        2 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];

    public function GetQuotes($work_id, Request $request = null, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        $user_id = auth()->user()? auth()->user()->id:null;
        if ($user_id){
            $this->sort_options = [
                1 => ['name' => 'За користувачем', 'sql' => 'is_current_user'],
                2 => ['name' => 'За датою додавання', 'sql' => 'id'],
            ];            
        }

        $this->sort = $this->GetOrderBy($request);
        $this->filter = $this->GetQuotesFilters($work_id,$request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page,-1,[ 'scroll-to' => 'work_tabs']):null;

        $query = "SELECT * "
            .($user_id? 
            ", CASE 
                WHEN user_id = $user_id THEN true 
                ELSE false 
            END as is_current_user " : "")
            ."FROM quote "
            .$this->filter->sql
            .$this->sort->sql
            .$this->paginator->sql;

        $items = DB::select($query);
        
        foreach ($items as $item) {
            $item->user = User::find($item->user_id);
        }

        return $items;
    }

    public function GetQuotesFilters($work_id, Request $request){
        $filter = new \stdClass();
        $filter->sql = "where w_id = $work_id ";
        return $filter;
    }
    
}
