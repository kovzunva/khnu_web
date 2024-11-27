<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfileService extends BaseItemService 
{
    public $table = "users";
    public $sort_options = [
        2 => ['name' => 'За датою реєстрації', 'sql' => 'id'],
        3 => ['name' => 'За ім\'ям', 'sql' => 'name'],
    ];

    public function GetItems(Request $request = null, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        if (!auth()->user()){
            $this->sort_options[1]['name'] = 'За орієнтирністю (ні)';
            $this->sort_options[1]['sql'] = 'id';
        }
        $this->sort = $this->GetOrderBy($request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page):null;

        if (auth()->check()){
        $profilesData = DB::select('SELECT u.id AS id, u.name AS name,
            COUNT(CASE WHEN ABS(r1.value - r2.value) = 0 THEN 1 END) - COUNT(CASE WHEN ABS(r1.value - r2.value) > 1 THEN 1 END) as orienter
            FROM users u
            LEFT JOIN rating r1 ON u.id = r1.user_id
            LEFT JOIN rating r2 ON r1.w_id = r2.w_id AND r2.user_id = ? AND r1.user_id <> r2.user_id
            GROUP BY u.id, u.name'
            .$this->sort->sql
            .$this->paginator->sql
            , [auth()->user()->id]);
        }
        else{
        $profilesData = DB::select('SELECT u.id AS id, u.name AS name FROM users u
            GROUP BY u.id, u.name'
            .$this->sort->sql
            .$this->paginator->sql);
        }
        $items = User::hydrate($profilesData);

        return $items;
    }
    
}
