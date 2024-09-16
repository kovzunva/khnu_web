<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReviewService extends BaseItemService 
{
    public $table = "review";
    public $sort_options = [
        2 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];

    public function GetReviews($work_id, Request $request = null, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        $user_id = auth()->user()? auth()->user()->id:null;
        if ($user_id){
            $this->sort_options = [
                1 => ['name' => 'За користувачем', 'sql' => 'is_current_user'],
                2 => ['name' => 'За датою додавання', 'sql' => 'id'],
            ];            
        }

        $this->sort = $this->GetOrderBy($request);
        $this->filter = $this->GetReviewsFilters($work_id,$request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page,-1,[ 'scroll-to' => 'work_tabs']):null;

        $query = "SELECT r.*, rt.value as rating "
            .($user_id? 
            ", CASE 
                WHEN r.user_id = $user_id THEN true 
                ELSE false 
            END as is_current_user " : "")
            ."FROM review r
            LEFT JOIN rating rt 
            ON r.user_id = rt.user_id AND r.w_id = rt.w_id "
            .$this->filter->sql
            .$this->sort->sql
            .$this->paginator->sql;

        $items = DB::select($query);
        
        foreach ($items as $item) {
            $item->user = User::find($item->user_id);
        }

        return $items;
    }

    public function GetCount(){
        $result = DB::select("select count(*) as count from $this->table r ".($this->filter? $this->filter->sql:""));
        return $result && count($result)>0? $result[0]->count:0; 
    }

    public function GetReviewsFilters($work_id, Request $request){
        $filter = new \stdClass();
        $user_id = auth()->user()? auth()->user()->id : null;
        $filter->sql = "where r.w_id = $work_id and (is_public = true ".($user_id? "or r.user_id = ".$user_id:"").")";
        return $filter;
    }
    
}
