<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\LogService;
use App\Services\DateService;
use App\Services\WorkService;
use App\Services\ImageService;
use App\Services\ReviewService;
use App\Services\QuoteService;

class WorkController extends Controller
{    
    protected $service;
    protected $imageService;

    public function __construct(WorkService $service, ImageService $imageService, ReviewService $reviewService, QuoteService $quoteService)
    {
        $this->service = $service;
        $this->imageService = $imageService;
        $this->reviewService = $reviewService;
        $this->quoteService = $quoteService;
    }

    public function index(Request $request, $page = 1){
        $items = $this->service->GetItems($request);
        
        return view('client.works',[
            'title' => 'Твори',
            'items' => $items,
            'service' => $this->service,
        ]);
    } 

    public function show(Request $request, $id){
        $item = $this->service->GetItem($id,$request);  
        $can_edit = $this->service->CanEdit($item); 
        $item->reviews = $this->reviewService->GetReviews($id,$request);
        $item->reviewService = $this->reviewService;
        $item->quotes = $this->quoteService->GetQuotes($id,$request);
        $item->quoteService = $this->quoteService;
        
        $this->service->GetClassificatorOptions($id);
        $shelves = $this->service->GetShelves($id);

        // Вкладка
        $tab = "reviews";
        if (request()->has('tab')) $tab = request()->get('tab');
                                
        return view('client.work', [
            'title' => $item->name,
            'work' => $item,
            'service' => $this->service,
            'shelves' => $shelves,
            'can_edit' => $can_edit,
            'tab' => $tab,
        ]);
    }
    
    public function showAll(){
        $items = DB::select('SELECT w.id as id, CONCAT(GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", "), " «", w.name, "»") as name
        FROM work AS w
        INNER JOIN avtor_work AS aw ON w.id = aw.w_id
        INNER JOIN person AS p ON p.id = aw.av_id
        WHERE genre_id is null OR year is null OR language_id is null
        GROUP BY w.id, w.name');
        
        return view('admin.items',[
            'title' => 'Адмінка - Твори',
            'items' => $items,
            'item_add' => 'твір',
            'item_type' => 'work',
            'item_title' => 'Твори'
        ]);
    } 

    public function showAllMy(){
        $items = DB::select('SELECT w.id as id, CONCAT(GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", "), " «", w.name, "»") as name
        FROM work AS w
        INNER JOIN avtor_work AS aw ON w.id = aw.w_id
        INNER JOIN person AS p ON p.id = aw.av_id
        WHERE w.user_id = ?
        GROUP BY w.id, w.name', [auth()->user()->id]);
        
        return view('content-maker.items',[
            'title' => 'Майстерня - Твори',
            'items' => $items,
            'item_add' => 'твір',
            'item_type' => 'work',
            'item_title' => 'Твори'
        ]);
    }    

    public function emptyForm(Request $request){
        $genres = DB::select('SELECT * FROM genre ORDER BY name');
        $languages = DB::select('SELECT * FROM language ORDER BY name');
        return view('content-maker.work-form',[
            'title' => 'Майстерня - Додавання твору',
            'work' => null,
            'genres' => $genres,
            'languages' => $languages,
        ]);
    }

    public function add(Request $request){
        $year = $request->input('year');
        if ($request->input('year_n_e')) $year *= -1;

        $error = '';
        try {
            $user = auth()->user();
            $id = DB::table('work')->insertGetId([
                'name' => $request->input('name'),
                'genre_id' => $request->input('genre_id'),
                'language_id' => $request->input('language_id'),
                'main_edition' => $request->input('main_edition'),
                'year' => $year,
                'notes' => $request->input('notes'),
                'links' => $request->input('links'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);
            $error .= LogService::add($id,'work','add');
        }
        catch (\Exception $e) {
            $error .= 'Помилка при вставці даних. ';
        }        

        // Альтернативні імена
        try{
            $alt_names_add = $request->input('add_w_alt_name');
            if ($alt_names_add)
            foreach ($alt_names_add as $alt_name){
                DB::table('w_alt_names')->insert([
                    'work_id' => $id,
                    'name' => $alt_name,
                ]);
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці альтернативних імен. ';
        }
        
        // Автори
        try{
            $avtors_work_add = $request->input('add_avtor_work');
            if ($avtors_work_add)
            foreach ($avtors_work_add as $avtor_work){
                DB::table('avtor_work')->insert([
                    'w_id' => $id,
                    'av_id' => $avtor_work,
                ]);
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці авторства твору. ';
        }
        
        // Анотації
        try{
            $anotations_add = $request->input('add_anotation');
            if ($anotations_add)
            foreach ($anotations_add as $anotation){
                DB::table('anotation')->insert([
                    'work_id' => $id,
                    'text' => $anotation,
                ]);
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці анотацій. ';
        }

        if ($error!='') {
            if (isset($id)) return redirect()->route('work.editForm', ['id' => $id])->with('error', $error);
            else return redirect()->back()->with('error', $error);
        }
        else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('work', ['id' => $id])->with('success', 'Твір додано успішно');
        else return redirect()->route('work.editForm', ['id' => $id])->with('success', 'Твір додано успішно');
    }
    
    public function edit(Request $request, $id){
        $item = DB::select("SELECT w.* , g.name as genre, l.name as language, ed.id as main_edition, ed.main_img as main_img,
                            CONCAT(ed.name, ' (', ed.year, 'р.)') as main_edition_name,
                            GROUP_CONCAT(alt.name SEPARATOR ', ') AS alt_names,
                            GROUP_CONCAT(CONCAT('<a href=\"/persons/',p.id,'\">', p.name, '</a>') ORDER BY p.name ASC SEPARATOR ', ') as avtors
                            FROM work AS w
                            INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                            INNER JOIN person AS p ON p.id = aw.av_id
                            LEFT JOIN w_alt_names alt ON w.id = alt.work_id
                            LEFT JOIN genre AS g ON g.id = w.genre_id
                            LEFT JOIN language AS l ON l.id = w.language_id
                            LEFT JOIN edition as ed on ed.id = w.main_edition
                            WHERE w.id = $id
                            GROUP BY w.id, w.name");

        
        if (!$item) return abort(404);
        $item = $item[0];
        // dd($item);
        $user = auth()->user();
        $admin = $user->hasPermission('admin');
        $content_make = $user->hasPermission('content-make');   
        if (!($item->user_id==$user->id || $admin || $content_make && !$item->is_public) ) return abort(403);
        
        if ($request->has('submit')){ 
            $year = $request->input('year');
            if ($request->input('year_n_e')) $year *= -1;

            $error = '';
            try {
                DB::table('work')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'genre_id' => $request->input('genre_id'),
                    'language_id' => $request->input('language_id'),
                    'main_edition' => $request->input('main_edition'),
                    'year' => $year,
                    'notes' => $request->input('notes'),
                    'links' => $request->input('links'),
                    'user_id' => $content_make? $user->id : $item->user_id,
                    'is_public' => $content_make? true: false,
                ]);
                
                $error .= LogService::add($id,'work','update');
            }
            catch (Exception $e) {
                $error .= 'Помилка при вставці даних. ';
            }

            // Альтернативні імена
            try{
                $alt_names_del = $request->input('del_w_alt_name');
                if ($alt_names_del)
                foreach ($alt_names_del as $alt_name){
                    DB::table('w_alt_names')->where('id', $alt_name)->delete();
                }
                $alt_names_add = $request->input('add_w_alt_name');
                if ($alt_names_add)
                foreach ($alt_names_add as $alt_name){
                    DB::table('w_alt_names')->insert([
                        'work_id' => $id,
                        'name' => $alt_name,
                    ]);
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці альтернативних імен. ';
            }
            
            // Автори
            try{
                $avtors_work_del = $request->input('del_avtor_work');
                if ($avtors_work_del)
                foreach ($avtors_work_del as $avtor_work){
                    DB::table('avtor_work')
                    ->where('w_id', $id)
                    ->where('av_id', $avtor_work)
                    ->delete();
                }
                $avtors_work_add = $request->input('add_avtor_work');
                if ($avtors_work_add)
                foreach ($avtors_work_add as $avtor_work){
                    DB::table('avtor_work')->insert([
                        'w_id' => $id,
                        'av_id' => $avtor_work,
                    ]);
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці авторства твору. ';
            }

            // Цикли
            try{
                $item_cycle_del = $request->input('del_work_cycle');
                if ($item_cycle_del)
                foreach ($item_cycle_del as $item_cycle){
                    DB::table('work_cycle')
                    ->where('w_id', $id)
                    ->where('c_id', $item_cycle)
                    ->delete();
                }
                $item_cycle_add = $request->input('add_work_cycle');
                if ($item_cycle_add)
                foreach ($item_cycle_add as $item_cycle){
                    DB::table('work_cycle')->insert([
                        'w_id' => $id,
                        'c_id' => $item_cycle,
                    ]);
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці циклів. ';
            }
            
            // Анотації
            try{
                $anotations_del = $request->input('del_anotation');
                if ($anotations_del)
                foreach ($anotations_del as $anotation){
                    DB::table('anotation')
                    ->where('id', $anotation)
                    ->delete();
                }
                $anotations_add = $request->input('add_anotation');
                if ($anotations_add)
                foreach ($anotations_add as $anotation){
                    DB::table('anotation')->insert([
                        'work_id' => $id,
                        'text' => $anotation,
                    ]);
                }
                $anotations_edit_id = $request->input('edit_anotation_id');
                $anotations_edit = $request->input('edit_anotation');
                if ($anotations_edit_id){
                    for ($i=0; $i<count($anotations_edit);$i++){
                        DB::table('anotation')->where('id', $anotations_edit_id[$i])->update([
                            'text' => $anotations_edit[$i],
                        ]);                    
                    }
                }
            }
            catch (Exception $e) {
                $error .= 'Помилка при обробці анотацій. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('work',$id)->with('success', 'Зміни внесено успішно.');
            else return redirect()->route('work.editForm',$id)->with('success', 'Зміни внесено успішно.');
        }
        else {      
            $genres = DB::select('SELECT * FROM genre ORDER BY name');
            $languages = DB::select('SELECT * FROM language ORDER BY name');                
            $alt_names = DB::select("SELECT * from w_alt_names WHERE work_id = $id");       
            $anotations = DB::select("SELECT * from anotation WHERE work_id = $id");

            $avtors_work = DB::table('work as w')
            ->Join('avtor_work as aw', 'w.id', '=', 'aw.w_id')
            ->Join('person as p', 'p.id', '=', 'aw.av_id')
            ->select('p.id as id', 'p.name as name')
            ->where('w.id', $id)
            ->get();

            $item_cycles = DB::table('work as w')
            ->Join('work_cycle as wc', 'w.id', '=', 'wc.w_id')
            ->Join('work as c', 'c.id', '=', 'wc.c_id')
            ->select('c.id as id', 'c.name as name')
            ->where('w.id', $id)
            ->get();

            $item_editions = DB::table('work as w')
            ->Join('edition_item as ei', 'w.id', '=', 'ei.w_id')
            ->Join('edition as ed', 'ed.id', '=', 'ei.ed_id')
            ->select('ed.id as id', 'ed.name as name', 'ed.year as year')
            ->where('w.id', $id)
            ->get();
            if (isset($item->main_edition))
            $item->img = $this->imageService->getImg('edition',$item->main_edition,$item->main_img);

            return view('content-maker.work-form',[
                'title' => 'Майстерня - Редагування твору',
                'work' => $item,
                'genres' => $genres,
                'languages' => $languages,
                'alt_names' => $alt_names,
                'avtors_work' => $avtors_work,
                'anotations' => $anotations,
                'work_cycles' => $item_cycles,
                'work_editions' => $item_editions,
            ]);
        }
    }

    public function searchCycles(Request $request){
        $term = $request->input('term');
        $results = DB::select("SELECT id, name FROM work WHERE name LIKE '%$term%' and genre_id = 9");
        return response()->json($results);
    }

    public function search(Request $request){   
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
        $term = $request->input('term');
        
        if ($term){
            $term = '%' . str_replace("'", "", $term) . '%';
        
            $results = DB::select("SELECT w.id as id,
                    CONCAT(GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ', '), ' «', w.name, '»' ) as name,
                    CONCAT('/work/', w.id) AS url
                FROM work AS w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                INNER JOIN person AS p ON p.id = aw.av_id
                LEFT JOIN w_alt_names AS wan ON w.id = wan.work_id
                WHERE (w.is_public = 1 OR w.user_id = ? OR ? = 1) AND (REPLACE(w.name, '''', '') LIKE REPLACE(?, '''', '') OR REPLACE(wan.name, '''', '') LIKE REPLACE(?, '''', ''))
                GROUP BY w.id
                HAVING name LIKE ?", [$user_id, $content_make, $term, $term, $term]);
        
            return response()->json($results);
        }
        else return null;
    }
    
    
    public function searchWithAvtor(Request $request){
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
        $name = $request->input('name');
        $avtor = $request->input('avtor');
        
        if ($name && $avtor){
            $name = is_string($name) ? "%" . str_replace("'", "", $name) . "%" : "%" . implode('%', array_map(function($n) { return str_replace("'", "", $n); }, $name)) . "%";
            $avtor = is_string($avtor) ? "%" . str_replace("'", "", $avtor) . "%" : "%" . implode('%', array_map(function($a) { return str_replace("'", "", $a); }, $avtor)) . "%";
        
            
            // $results = DB::select("
            //     SELECT w.name AS name, w.id as id, CONCAT('/work/', w.id) AS url
            //     FROM work AS w
            //     INNER JOIN avtor_work AS aw ON w.id = aw.w_id
            //     INNER JOIN person AS p ON p.id = aw.av_id
            //     LEFT JOIN w_alt_names AS wan ON w.id = wan.work_id
            //     LEFT JOIN p_alt_names AS pan ON p.id = pan.person_id
            //     WHERE (REPLACE(w.name, ''', '') LIKE REPLACE(?, '''', '') OR REPLACE(wan.name, '''', '') LIKE REPLACE(?, '''', ''))
            //     AND (REPLACE(p.name, '''', '') LIKE REPLACE(?, '''', '') OR REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', ''))
            //     AND (w.is_public = 1 OR w.user_id = ? OR ? = 1)
            //     GROUP BY w.id, w.name", [$name, $name, $avtor, $avtor, $user_id, $content_make]); 
            //     return response()->json($results);
                $results = DB::select("
                    SELECT w.name AS name, w.id as id, CONCAT('/work/', w.id) AS url
                    FROM work AS w
                    INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                    INNER JOIN person AS p ON p.id = aw.av_id
                    LEFT JOIN w_alt_names AS wan ON w.id = wan.work_id
                    LEFT JOIN p_alt_names AS pan ON p.id = pan.person_id
                    WHERE w.name LIKE ? OR wan.name LIKE ?
                    AND p.name LIKE ? OR pan.name LIKE?
                    AND (w.is_public = 1 OR w.user_id = ? OR ? = 1)
                    GROUP BY w.id, w.name", [$name, $name, $avtor, $avtor, $user_id, $content_make]); 
                    return response()->json($results);
        }
        else {
            return null;
        }
    }           
    
    public function quickAdd(Request $request){ 
        $response['error'] = '';
        try {
            $user = auth()->user();
            $id = DB::table('work')->insertGetId([
                'name' => $request->input('name'),
                'genre_id' => $request->input('genre_id'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);  
            $error = LogService::add($id,'work','add');

            // Альтернативна назва
            if ($request->input('alt_name'))
            DB::table('w_alt_names')->insert([
                'work_id' => $id,
                'name' => $request->input('alt_name'),
            ]);

            // Автори
            try {
                $avtors_work_add = $request->input('avtor_id');
                if ($avtors_work_add)
                foreach ($avtors_work_add as $avtor_work){
                    DB::table('avtor_work')->insert([
                        'w_id' => $id,
                        'av_id' => $avtor_work,
                    ]);
                }
            }
            catch (\Exception $e) {
                $response['error'] .= 'Помилка при обробці авторства твору. ';
            }

            // Анотація
            try {
                DB::table('anotation')->insert([
                    'work_id' => $id,
                    'text' => $request->input('anotation'),
                ]);
            }
            catch (\Exception $e) {
                $response['error'] .= 'Помилка при обробці анотації. ';
            }
            $response['success'] = 'Твір "'.$request->input('name').'" додано успішно | '.$request->input('genre_id');
            $work = new \stdClass();
            $work->id = $id;
            $work->name = $request->input('name');
            $response['work'] = $work;
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при вставці даних. '.$e;
        }
        return response()->json($response);  
    }

    public function rate(Request $request, $id){
        try {
            $user_id = auth()->user()->id;

            DB::table('rating')
            ->where('w_id', $id)
            ->where('user_id', $user_id)
            ->delete();

            DB::table('rating')->insert([
                'w_id' => $id,
                'user_id' => $user_id,
                'value' => $request->input('value'),
                'created_at' => now(),
            ]);  

            $averageRating = DB::table('rating')
            ->where('w_id', $id)
            ->avg('value');

            $response['success'] = 'Оцінку виставлено успішно';
            $response['average_rating'] = number_format($averageRating, 2); 
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при виставленні оцінки. '.$e;
        }
        return response()->json($response);  
    }

    public function cancelRate(Request $request, $id){
        try {
            $user_id = auth()->user()->id;

            DB::table('rating')
            ->where('w_id', $id)
            ->where('user_id', $user_id)
            ->delete();
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при скасуванні оцінки';
            return response()->json($response);
        }

        $response['success'] = 'Оцінку скасовано успішно';
        return response()->json($response);
    }

    public function dateRead(Request $request, $id) {
        try {
            $user_id = auth()->user()->id;
            $date_read = $request->input('date_read');

            if ($date_read) $date_read = DateService::formatDateToYYYYMMDD($date_read);
    
            $date_read_exists = DB::table('date_read')
                ->where('w_id', $id)
                ->where('user_id', $user_id)
                ->first();
    
            if ($date_read_exists) {
                if ($date_read) {
                    DB::table('date_read')
                        ->where('w_id', $id)
                        ->where('user_id', $user_id)
                        ->update([
                            'date_read' => $date_read,
                        ]);
                    $response['success'] = 'Дату прочитання виставлено успішно';
                }
                else {
                    DB::table('date_read')
                        ->where('w_id', $id)
                        ->where('user_id', $user_id)
                        ->delete();
                    $response['success'] = 'Дату прочитання скасовано успішно';
                }
            }
            else if ($date_read){                
                DB::table('date_read')->insert([
                    'w_id' => $id,
                    'user_id' => $user_id,
                    'date_read' => $date_read,
                    'created_at' => now(),
                ]);    
                $response['success'] = 'Дату прочитання виставлено успішно';
            }
            else $response['success'] = 'Успішно нічого не зроблено';
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при виставленні дати прочитання. '.$e->getMessage();
        }
    
        return response()->json($response);
    }    

    public function cancelDateRead(Request $request, $id) {
        try {
            $user_id = auth()->user()->id;    
            DB::table('date_read')
                ->where('w_id', $id)
                ->where('user_id', $user_id)
                ->delete();
            $response['success'] = 'Дату прочитання скасовано успішно';
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при скасуванні дати прочитання. ' . $e->getMessage();
        }
    
        return response()->json($response);
    } 

    public function classificate(Request $request, $id){
        $error = '';
        try {
            $user_id = auth()->user()->id;
            $inputData = $request->except('_token');
            
            DB::table('classificator')
            ->where('w_id', $id)
            ->where('user_id', $user_id)
            ->delete();

            foreach($inputData as $option){
                DB::table('classificator')->insert([
                    'w_id' => $id,
                    'user_id' => $user_id,
                    'option_id' => $option,
                ]);
            }
        }
        catch (\Exception $e) {
            $error = 'Помилка при класифікації твору. '.$e;
        }
        
        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Твір класифіковано успішно');    
    }

    public function recommendations(Request $request, $page = 1){
        if (count(auth()->user()->orientators())==0) $items = null;
        else $items = $this->service->GetRecommendations($request);
        
        return view('client.recommendations',[
            'title' => 'Щопочитайка',
            'works' => $items,
            'service' => $this->service,
            'has_orientators' => true,
        ]);
    } 

    public function addSimilar(Request $request){        

        $error = '';
        try {
            $user = auth()->user();
            $id = DB::table('similar_works')->insertGetId([
                'w_name' => $request->input('w_name'),
                'w_similar' => $request->input('w_similar'),
                'reason' => $request->input('reason'),
                'user_id' => $user->id,
                'created_at' => new \DateTime(),
            ]);
        }
        catch (\Exception $e) {
            $error .= 'Помилка при додаванні схожого твору. ';
        }        


        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Схожий твір додано успішно');    
    }

    public function delSimilar($w_id,$w_similar_id){   
        $error = '';
        $similar = DB::table('similar_works')->where('w_id', $w_id)->where('w_similar_id', $w_similar_id)->first(); 

        if (auth()->user()->id != $similar->user_id) abort(404);

        try{
            DB::table('similar_works')->where('w_id', $w_id)->where('w_similar_id', $w_similar_id)->delete();
        }
        catch (\Exception $e) {
            $error .= 'Помилка при видаленні схожого твору. ';
        }  
        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Схожий твір видалено успішно');
    }
}