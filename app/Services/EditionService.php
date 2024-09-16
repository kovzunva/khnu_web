<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ImageService;
use App\Services\DateService;
use App\Models\User;

class EditionService extends BaseItemService 
{
    public $table = "person";
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
        $edition = DB::table('edition as ed')
        ->leftJoin('publisher as p', 'ed.publisher_id', '=', 'p.id')
        ->leftJoin('type_of_cover as tc', 'ed.type_of_cover_id', '=', 'tc.id')
        ->leftJoin('language as l', 'ed.language_id', '=', 'l.id')
        ->select('ed.*', 'tc.name as type_of_cover', 'p.name as publisher', 'l.name as language')
        ->where('ed.id', $id)
        ->first();        
        
        if (!$edition) return abort(404);
    
        $edition->img = $this->imageService->getImg('edition',$id,$edition->main_img);  

        $persons_edition = DB::table('edition as ed')
        ->Join('person_edition as pe', 'ed.id', '=', 'pe.ed_id')
        ->Join('person as p', 'p.id', '=', 'pe.p_id')
        ->select('p.id as id', 'p.name as base_name', 'pe.type as type', 'pe.name as name')
        ->where('ed.id', $id)
        ->get();    
        $edition->avtors = [];
        $edition->designers = [];
        $edition->illustrators = [];
        foreach ($persons_edition as $person) {
            if ($person->type == 1) {
                $edition->avtors[] = $person;
            }
            elseif ($person->type == 3) {
                $edition->designers[] = $person;
            }
            elseif ($person->type == 4) {
                $edition->illustrators[] = $person;
            }
        } 
        $avtors = $edition->avtors;
        $avtorsLinks = array();
        foreach ($avtors as $avtor) {
            $avtorsLinks[] = '<a href="/persons/' . $avtor->id . '">' . $avtor->name . '</a>';
        }
        $avtorsLinksString = implode(', ', $avtorsLinks);
        $edition->avtors = $avtorsLinksString;
        
        $edition->items = DB::table('edition as ed')
        ->Join('edition_item as ei', 'ed.id', '=', 'ei.ed_id')
        ->Join('work as w', 'w.id', '=', 'ei.w_id')
        ->select('w.id as w_id', 'w.name as base_name', 'ei.id as id', 'ei.name as name', 'ei.pages as pages', 
        'ei.level as level', 'ei.number as number')
        ->where('ed.id', $id)
        ->orderBy('number')
        ->get();  
        foreach ($edition->items as $item){
            $item->translators = DB::table('person as p')
            ->Join('translator_item as ti', 'p.id', '=', 'ti.tr_id')
            ->Join('edition_item as ei', 'ei.id', '=', 'ti.it_id')
            ->select('p.id as id', 'p.name as base_name', 'ti.name as name')
            ->where('ei.id', $item->id)
            ->get();
            $translatorsLinks = array();
            foreach ($item->translators as $translator) {
                $translatorsLinks[] = '<a href="/person/' . $translator->id . '">' . $translator->name . '</a>';
            }
            $translatorsLinksString = implode(', ', $translatorsLinks);
            $item->translators = $translatorsLinksString;
        }

        return $edition;
    }
    
}
