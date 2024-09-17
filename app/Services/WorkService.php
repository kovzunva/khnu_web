<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\ImageService;

class WorkService extends BaseItemService 
{
    public $table = "work";
    public $sort_options = [
        1 => ['name' => 'За рейтингом', 'sql' => 'rating'],
        2 => ['name' => 'За назвою', 'sql' => 'name'],
        3 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];

    public $classificator_groups = null;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function GetItems(Request $request = null, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        $this->sort = $this->GetOrderBy($request);
        $this->filter = $this->GetFilters($request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page):null;
        $this->GetClassificatorOptions();

        $query = 'SELECT w.id, w.name, w.main_edition, ed.main_img AS main_img, avtors.avtors AS avtors,
                AVG(COALESCE(r.value, 0)) AS rating, 
                SUBSTRING_INDEX(GROUP_CONCAT(an.text ORDER BY an.id ASC SEPARATOR "|"), "|", 1) AS anotation,
                ucc.user_count AS classificator_users_count,
                GROUP_CONCAT(DISTINCT 
                    CASE WHEN opt_p.opt_group_id IN (2, 6) AND opt_p.opt_percentage>='.$this->filter->percent.' 
                    THEN CONCAT("#", opt_p.opt_name) 
                    ELSE NULL 
                    END 
                    ORDER BY opt_p.opt_id SEPARATOR " ") AS options_info
            FROM work AS w      
            LEFT JOIN edition AS ed ON ed.id = w.main_edition
            LEFT JOIN rating AS r ON w.id = r.w_id 
            LEFT JOIN anotation AS an ON w.id = an.work_id 
            LEFT JOIN user_classification_count AS ucc ON w.id = ucc.w_id
            LEFT JOIN option_percentage AS opt_p ON w.id = opt_p.w_id
            LEFT JOIN (SELECT w.id AS work_id, GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", ") AS avtors
                FROM work AS w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                INNER JOIN person AS p ON p.id = aw.av_id
                GROUP BY w.id
            ) AS avtors ON w.id = avtors.work_id
            WHERE w.is_public = 1 
            GROUP BY w.id, w.name, ed.id, ed.main_img, avtors.avtors, ucc.user_count, w.main_edition '
            .$this->filter->sql
            .$this->sort->sql
            .$this->paginator->sql;

        $items = DB::select($query);
        
        // Обкладинки
        foreach ($items as $item){
            if (!$item->main_edition) {
                $firstAddedEdition = DB::select("SELECT e.id, e.name, e.main_img from edition as e
                                    INNER JOIN edition_item AS ei ON e.id = ei.ed_id
                                    where ei.w_id = $item->id
                                    limit 1"); 
                if ($firstAddedEdition){    
                    $firstAddedEdition = $firstAddedEdition[0];        
                    $item->main_edition = $firstAddedEdition->id;   
                    $item->main_img = $firstAddedEdition->main_img;   
                    $item->img = ImageService::getImg('edition',$item->main_edition,$item->main_img);
                }
                else $item->img = null;
            }            
        }  

        return $items;
    }

    // об'єкт фільтрування
    public function GetFilters(Request $request){
        $filter = new \stdClass();

        $filter->options = [];
        $filter->no_options = [];
        $filter->no_option_names = null;
        $filter->option_names = null;
        $filter->sql = '';
        $filter->percent = !$request->input('percent') || $request->input('percent')>100 || $request->input('percent')<0? 25:$request->input('percent');

        $conditions = [];   
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'no_opt') === 0) {
                $optionId = str_replace('no_opt', '', $key);
                $conditions[] = "MAX(CASE WHEN opt_p.opt_id = $optionId THEN opt_p.opt_percentage ELSE 0 END) < $filter->percent";
                $filter->no_options[] = $optionId;
            } elseif (strpos($key, 'opt') === 0) {
                $optionId = str_replace('opt', '', $key);
                $conditions[] = "MAX(CASE WHEN opt_p.opt_id = $optionId THEN opt_p.opt_percentage ELSE 0 END) >= $filter->percent";
                $filter->options[] = $optionId;
            }
        }
        
        if (!empty($conditions)) {
            $filter->sql = 'HAVING ' . implode(' AND ', $conditions);
        }

        // Назви опцій фільтрування, щоб користувач бачив, який запит зробив
        if (!empty($filter->no_options)) {
            $filter->no_option_names = DB::table('classificator_option')->whereIn('id', $filter->no_options)->pluck('name')->implode(', ');
        }    
        if (!empty($filter->options)) {
            $filter->option_names = DB::table('classificator_option')->whereIn('id', $filter->options)->pluck('name')->implode(', ');
        }

        return $filter;
    }

    // опції класифікування
    public function GetClassificatorOptions($w_id = null){
        if (!$w_id || !auth()->user()){
            $options = DB::select("SELECT co.*, cg.name as group_name, cg.radio as radio, co.change_id as change_id 
                from classificator_option as co
                LEFT JOIN classificator_group AS cg ON cg.id = co.group_id
                ORDER BY cg.sort_index, cg.id, co.change DESC, co.sort_index, co.name"); 
        }
        else {
            $options = DB::select("SELECT co.*, cg.name as group_name, cg.radio as radio, co.change_id as change_id,
                    CASE WHEN c.option_id IS NOT NULL THEN true ELSE false END as is_selected
                FROM classificator_option as co
                LEFT JOIN classificator_group AS cg ON cg.id = co.group_id
                LEFT JOIN classificator AS c ON c.option_id = co.id AND c.user_id = :user_id AND c.w_id = :id
                ORDER BY cg.sort_index, cg.id, co.change DESC, co.sort_index, co.name
            ", ['user_id' => auth()->user()->id, 'id' => $w_id]);
        }

        $groups = [];
        foreach ($options as $option) {
            // Розподіл по групах
            $group = $option->group_name;
            if (!isset($groups[$group])) {
                $groups[$group] = new \stdClass();
                $groups[$group]->options = [];
            }

            // Розподіл підопцій
            if ($option->change_id && isset($groups[$group]->options[$option->change_id])) {
                $groups[$group]->options[$option->change_id]->suboptions[$option->id] = $option;
            }
            else {
                $groups[$group]->options[$option->id] = $option;
            }
        }

        $this->classificator_groups = $groups;
    }

    // класифікатор конкретного твору
    public function GetClassificator($item){
        $id = $item->id;
        $item->classificator_users_count = DB::select("SELECT COUNT(DISTINCT user_id) AS count 
            FROM classificator WHERE w_id = $id");
        if ($item->classificator_users_count){
            $item->classificator_users_count = $item->classificator_users_count[0]->count;
            $item->classificator = DB::select("SELECT co.name, cg.name as group_name,
                (COUNT(CASE WHEN option_id = co.id THEN 1 ELSE NULL END) / $item->classificator_users_count) * 100 AS percentage
                FROM classificator c
                INNER JOIN classificator_option co ON c.option_id = co.id
                INNER JOIN classificator_group cg ON co.group_id = cg.id
                WHERE c.w_id = $id
                GROUP BY co.name, co.id
                ORDER BY cg.sort_index
            ");

            if (!empty($item->classificator)) {
                $item->classificator_groups = [];
            
                foreach ($item->classificator as $option) {
                    $group_name = $option->group_name;
                    if (!isset($option->classificator_groups[$group_name])) {
                        $item->classificator_groups[$group_name] = (object)[
                            'name' => $group_name,
                            'options' => '',
                        ];
                    }
            
                    $percentage = $option->percentage;
                    if ($percentage == (int)$percentage) {
                        $percentage = (int)$percentage;
                    } else {
                        $percentage = number_format($percentage, 1);
                    }
                    if (!empty($item->classificator_groups[$group_name]->options)) {
                        $item->classificator_groups[$group_name]->options .= ', ';
                    }
                    $item->classificator_groups[$group_name]->options .= $option->name . ' (' . $percentage . '%)';
                }
                $item->classificator_groups = array_values($item->classificator_groups);
            }
        }
    }

    // кількість
    public function GetCount(){
        $result = DB::select("select count(*) as count from work as w
        LEFT JOIN option_percentage AS opt_p ON w.id = opt_p.w_id
        LEFT JOIN user_classification_count AS ucc ON w.id = ucc.w_id
        WHERE w.is_public = 1 
        GROUP BY w.id, ucc.user_count 
        ".($this->filter? $this->filter->sql:""));
        return $result && count($result)>0? count($result):0; 
    }

    public function GetItem($id, Request $request = null){
        $user = auth()->user();
        $user_id = $user? $user->id:"''";
        $item = DB::select("SELECT w.*, g.name AS genre, l.name AS language, ed.id AS main_edition, ed.main_img AS main_img,
            GROUP_CONCAT(alt.name SEPARATOR ', ') AS alt_names,
            avtors.avtors AS avtors, AVG(r.value) AS average_rating,
            GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') AS shelves"
            .($user_id ? ", MAX(CASE WHEN r.user_id = $user_id THEN r.value ELSE NULL END) AS rating" : '')
            .($user_id ? ", MAX(CASE WHEN dr.user_id = $user_id THEN dr.date_read ELSE NULL END) AS date_read" : '')."
            FROM work AS w
            LEFT JOIN w_alt_names alt ON w.id = alt.work_id
            LEFT JOIN genre AS g ON g.id = w.genre_id
            LEFT JOIN language AS l ON l.id = w.language_id
            LEFT JOIN edition AS ed ON ed.id = w.main_edition
            LEFT JOIN rating AS r ON w.id = r.w_id
            LEFT JOIN date_read AS dr ON w.id = dr.w_id
            LEFT JOIN (
                SELECT w.id AS work_id,
                GROUP_CONCAT(CONCAT('<a href=\"/persons/', p.id, '\">', p.name, '</a>') ORDER BY p.name ASC SEPARATOR ', ') AS avtors
                FROM work AS w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                INNER JOIN person AS p ON p.id = aw.av_id
                WHERE w.id = $id
                GROUP BY w.id
            ) AS avtors ON w.id = avtors.work_id
            LEFT JOIN work_shelf AS ws ON ws.w_id = w.id
            LEFT JOIN shelf AS s ON s.id = ws.sh_id AND s.user_id = $user_id
            WHERE w.id = $id 
            GROUP BY w.id, w.name;"); 
                  
        if (!$item) return abort(404);
        $item = $item[0];

        if($item->date_read) $item->date_read = DateService::formatDateToDDMMYYYY($item->date_read);

        // Обкладинка
        if (!$item->main_edition) {
            $firstAddedEdition = DB::select("SELECT e.id, e.name, e.main_img from edition as e
                                INNER JOIN edition_item AS ei ON e.id = ei.ed_id
                                where ei.w_id = $id
                                limit 1"); 
            if ($firstAddedEdition){    
                $firstAddedEdition = $firstAddedEdition[0];        
                $item->main_edition = $firstAddedEdition->id;   
                $item->main_img = $firstAddedEdition->main_img;  
                $item->img = $this->imageService->getImg('edition',$item->main_edition,$item->main_img); 
            }
            else $item->img = null;
        }
      
        $item->anotations = DB::select("SELECT text from anotation where work_id = $id");
        $item->editions = DB::select("SELECT e.id, e.name, e.main_img, e.year from edition as e
                                    INNER JOIN edition_item AS ei ON e.id = ei.ed_id
                                    where ei.w_id = $id"); 
        foreach ($item->editions as $edition)
            $edition->img = $this->imageService->getImg('edition',$edition->id,$edition->main_img);

        // Відгуки
        // if ($user)    
        // $item->reviews_my = DB::select("SELECT * from review where w_id = $id and user_id = $user_id ORDER BY id DESC"); 
        // $item->reviews = DB::select("SELECT * from review where w_id = $id and is_public = true"
        // .($user_id? " and user_id != $user_id":'').' ORDER BY id DESC'); 
        // foreach ($item->reviews as $review) {
        //     $review->user = User::find($review->user_id);
        // }

        // Цитати
        // if ($user)    
        // $item->quotes_my = DB::select("SELECT * from quote where w_id = $id and user_id = $user_id ORDER BY id DESC"); 
        // $item->quotes = DB::select("SELECT * from quote where w_id = $id"
        // .($user_id? " and user_id != $user_id":'').' ORDER BY id DESC'); 
        // foreach ($item->quotes as $quote) {
        //     $quote->user = User::find($quote->user_id);
        // }

        // Схожі твори        
        $item->similars = DB::select("SELECT w.* 
            FROM similar_works AS sw
            INNER JOIN work_simple_view AS w 
                ON (w.id = sw.w_similar_id AND sw.w_id = ?)
                OR (w.id = sw.w_id AND sw.w_similar_id = ?)
            ORDER BY w.id DESC", [$id,$id]);
        foreach ($item->similars as $similar) {
            $similar->user = User::find($similar->user_id);
        }

        $this->GetClassificator($item);
        
        return $item;
    }

    // Книжкові полиці
    public function GetShelves($id){
        $user = auth()->user();
        $user_id = $user? $user->id:"''";
        if (!$user_id) return null;

        $result = DB::select("SELECT s.*, COUNT(ws.w_id) as is_added FROM shelf as s
            LEFT JOIN work_shelf as ws ON s.id = ws.sh_id AND ws.w_id = $id
            WHERE s.user_id = $user_id
            GROUP BY s.id
            ORDER BY is_added DESC");
        return $result; 
    }
    
}
