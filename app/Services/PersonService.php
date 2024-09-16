<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ImageService;
use App\Services\DateService;
use App\Models\User;

class PersonService extends BaseItemService 
{
    public $table = "person";
    public $sort_options = [
        1 => ['name' => 'За іменем', 'sql' => 'name'],
        2 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];
    public $take_columns = "id, name, is_avtor, is_translator, is_illustrator, is_designer, bio";

    public function __construct(ImageService $imageService, DateService $dateService)
    {
        $this->imageService = $imageService;
        $this->dateService = $dateService;
    }

    // об'єкт фільтрування
    public function GetFilters(Request $request){
        $filter = new \stdClass();

        $filter->sql = null;
        $filter->person_types = [];
        $person_types = [];

        $conditions = [];   
        if ($request->has('is_avtor')) {
            $conditions[] = 'is_avtor = 1';
            $person_types[] = 'автор';
        }
        if ($request->has('is_translator')) {
            $conditions[] = 'is_translator = 1';
            $person_types[] = 'перекладач';
        }
        if ($request->has('is_illustrator')) {
            $conditions[] = 'is_illustrator = 1';
            $person_types[] = 'ілюстратор';
        }
        if ($request->has('is_designer')) {
            $conditions[] = 'is_designer = 1';
            $person_types[] = 'дизайнер';
        }
        if (!empty($conditions)) {
            $filter->sql = 'WHERE ' . implode(' AND ', $conditions);
        }
        $filter->person_types = implode(', ', $person_types);

        return $filter;
    }

    public function GetItem($id, Request $request = null){
        $user = auth()->user();
        $user_id = $user? $user->id:"''";
        $item = DB::select("SELECT p.*, GROUP_CONCAT(a.name SEPARATOR ', ') AS alt_names
                            FROM person p
                            LEFT JOIN p_alt_names a ON p.id = a.person_id
                            WHERE p.id = $id
                            GROUP BY p.id;"); 
                  
        if (!$item) return abort(404);
        $item = $item[0];
      
        $item->birthdate = $this->dateService->formatDateFromInt($item->birthdate);
        $item->deathdate = $this->dateService->formatDateFromInt($item->deathdate); 
        $item->img = $this->imageService->getImg('person',$id);  

        // Твори
        $item->works = null;
        if ($item->is_avtor){
            $item->works = DB::select("SELECT w.id, w.name, ed.id as main_edition, ed.main_img as main_img
                FROM work as w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                LEFT JOIN edition as ed on ed.id = w.main_edition
                WHERE aw.av_id = $id");
        }
        
        // Переклади
        $item->translated = null;
        if ($item->is_translator){
            $item->translated = DB::select("SELECT w.id, w.name, w.avtors, p.name as publisher, ed.year as year
                FROM translator_item as tr
                INNER JOIN edition_item as ei on ei.id = tr.it_id
                INNER JOIN edition as ed on ed.id = ei.ed_id
                INNER JOIN publisher as p on p.id = ed.publisher_id
                INNER JOIN work_simple_view AS w ON w.id = ei.w_id
                WHERE tr.tr_id = $id");
        }
        
        // Ілюстровано
        $item->illustrated = null;
        if ($item->is_illustrator){
            $item->illustrated = DB::select("SELECT ed.id, ed.year, ed.main_img
                FROM person_edition as pe
                INNER JOIN edition as ed on ed.id = pe.ed_id
                WHERE pe.p_id = $id AND type = 4");
        }
        if ($item->illustrated)
        foreach ($item->illustrated as $edition){
            $edition->img = $this->imageService->getImg('edition',$edition->id,$edition->main_img);
        }
        
        // Дизайн обкладинок
        $item->designed = null;
        if ($item->is_designer){
            $item->designed = DB::select("SELECT ed.id, ed.year, ed.main_img
                FROM person_edition as pe
                INNER JOIN edition as ed on ed.id = pe.ed_id
                WHERE pe.p_id = $id AND type = 3");
        }
        if ($item->designed)
        foreach ($item->designed as $edition){        
            $edition->img = $this->imageService->getImg('edition',$edition->id,$edition->main_img);
        }

        return $item;
    }
    
}
