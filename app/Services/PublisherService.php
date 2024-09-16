<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ImageService;
use App\Services\DateService;
use App\Models\User;

class PublisherService extends BaseItemService 
{
    public $table = "publisher";
    public $sort_options = [
        1 => ['name' => 'За іменем', 'sql' => 'name'],
        2 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];
    public $take_columns = "id, name";

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function GetItem($id, Request $request = null){        
        $item = DB::select('SELECT p.*, c.name as country_name FROM publisher as p
            LEFT JOIN country as c on p.country_id = c.id
            WHERE p.id = ?', [$id]);

        if (!$item) return abort(404);
        $item = $item[0];

        $item->editions = DB::select("SELECT id, name, main_img, year FROM edition
            WHERE publisher_id = $id
            ORDER BY year DESC, id DESC"); 
        foreach ($item->editions as $edition){
            $edition->img = $this->imageService->getImg('edition',$edition->id,$edition->main_img);
        }

        return $item;
    }
    
}
