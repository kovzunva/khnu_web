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

        $profilesData = DB::select('SELECT u.id AS id, u.name AS name FROM users u
            GROUP BY u.id, u.name'
            .$this->sort->sql
            .$this->paginator->sql);
        $items = User::hydrate($profilesData);

        return $items;
    }
    
}
