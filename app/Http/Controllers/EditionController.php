<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogService;
use App\Services\EditionService;
use App\Models\User;
use App\Services\ImageService;

class EditionController extends Controller
{
    protected $service;

    public function __construct(EditionService $service)
    {
        $this->service = $service;
    }
    
    public function showAll(){
        $items = DB::select('SELECT ed.id as id, CONCAT(GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", "), " «", ed.name, "»") as name
        FROM edition AS ed
        INNER JOIN person_edition AS pe ON ed.id = pe.ed_id
        INNER JOIN person AS p ON p.id = pe.p_id
        WHERE language_id IS NULL OR year is null
        GROUP BY ed.id, ed.name');
        
        return view('admin.items',[
            'title' => 'Адмінка - Видання',
            'items' => $items,
            'item_add' => 'видання',
            'item_type' => 'edition',
            'item_title' => 'Видання'
        ]);
    }   

    public function showAllMy(){
        $items = DB::select('SELECT ed.id as id, CONCAT(GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", "), " «", ed.name, "»") as name
        FROM edition AS ed
        INNER JOIN person_edition AS pe ON ed.id = pe.ed_id
        INNER JOIN person AS p ON p.id = pe.p_id
        WHERE ed.user_id = ?
        GROUP BY ed.id, ed.name', [auth()->user()->id]);
        
        return view('content-maker.items',[
            'title' => 'Майстерня - Видання',
            'items' => $items,
            'item_add' => 'видання',
            'item_type' => 'edition',
            'item_title' => 'Видання'
        ]);
    }   

    public function show(Request $request, $id){          
        $item = $this->service->GetItem($id,$request);  
        $can_edit = $this->service->CanEdit($item);                                     

        return view('client.edition', [
            'title' => $item->name,
            'edition' => $item,
            'can_edit' => $can_edit,
        ]);
    }  

    public function emptyForm(Request $request){
        $types_of_cover = DB::select('SELECT * FROM type_of_cover ORDER BY name');
        $languages = DB::select('SELECT * FROM language ORDER BY name'); 
        $genres = DB::select('SELECT * FROM genre ORDER BY name');

        return view('content-maker.edition-form',[
            'title' => 'Майстерня - Додавання видання',
            'edition' => null,
            'types_of_cover' => $types_of_cover,
            'languages' => $languages,
            'genres' => $genres,
        ]);
    }

    public function add(Request $request){
        $error = '';
        $id = '';
        try {
            $user = auth()->user();
            $id = DB::table('edition')->insertGetId([
                'name' => $request->input('name'),
                'language_id' => $request->input('language_id'),
                'publisher_id' => $request->input('publisher_id'),
                'year' => $request->input('year'),
                'size' => $request->input('size'),
                'pages' => $request->input('pages'),
                'isbn' => $request->input('isbn'),
                'type_of_cover_id' => $request->input('type_of_cover_id'),
                'format' => $request->input('format'),
                'about' => $request->input('about'),
                'notes' => $request->input('notes'),
                'links' => $request->input('links'),
                'main_img' => $request->input('main_img'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);
            $error .= LogService::add($id,'edition','add');
        }
        catch (\Exception $e) {
            $error .= 'Помилка при вставці даних. ';
        }

        // Автори
        try{
            $avtors_add_id = $request->input('add_avtor_id');
            $avtors_add = $request->input('add_avtor');
            if ($avtors_add){
                for ($i=0; $i<count($avtors_add);$i++){
                    DB::table('person_edition')->insert([
                        'ed_id' => $id,
                        'p_id' => $avtors_add_id[$i],
                        'name' => $avtors_add[$i],
                        'type' => 1, // тип = автор
                    ]);                    
                }
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці авторів видання. ';
        }

        // Дизайнери
        try{
            $designers_add_id = $request->input('add_designer_id');
            $designers_add = $request->input('add_designer');
            if ($designers_add){
                for ($i=0; $i<count($designers_add);$i++){
                    DB::table('person_edition')->insert([
                        'ed_id' => $id,
                        'p_id' => $designers_add_id[$i],
                        'name' => $designers_add[$i],
                        'type' => 3, // тип = дизайнер
                    ]);                    
                }
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці дизайнерів видання. ';
        }

        // Ілюстратори
        try{
            $illustrators_add_id = $request->input('add_illustrator_id');
            $illustrators_add = $request->input('add_illustrator');
            if ($illustrators_add){
                for ($i=0; $i<count($illustrators_add);$i++){
                    DB::table('person_edition')->insert([
                        'ed_id' => $id,
                        'p_id' => $illustrators_add_id[$i],
                        'name' => $illustrators_add[$i],
                        'type' => 4, // тип = ілюстратор
                    ]);                    
                }
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці ілюстраторів видання. ';
        }

        // Вміст
        try{

            $items_add = $request->input('add_item');
            if ($items_add){
                foreach ($items_add as $item){
                    $insertedItemId = DB::table('edition_item')->insertGetId([
                        'ed_id' => $id,
                        'w_id' => $item['w_id'],
                        'name' => $item['name'],
                        'pages' => $item['pages'],
                        'level' => $item['level'],
                        'number' => $item['number'],
                    ]);  
                    if (isset($item['translator']))
                    foreach ($item['translator'] as $translator){
                        try{
                            DB::table('translator_item')->insert([
                                'it_id' => $insertedItemId,
                                'tr_id' => $translator['tr_id'],
                                'name' => $translator['name'],
                            ]);  
                        }
                        catch (Exception $e) {
                            $error .= 'Помилка при обробці перекладачів. ';
                        }
                    }                
                }
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці вмісту видання. ';
        }

        // Картинки 
        try{
            $imgs = $request->input('imgs');
            if ($imgs)
            foreach ($imgs as $img){
                $mes = ImageService::saveImg($img,'edition',$id);
                if ($mes!='') $error .= $mes.' ';
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці зображення. ';
        }

        if ($error!='') {
            if ($id) return redirect()->route('edition.editForm', ['id' => $id])->with('error', $error);
            else return redirect()->back()->with('error', $error);
        }
        else {
            return redirect()->route('edition', ['id' => $id])->with('success', 'Видання додано успішно');    
        }
    }
    
    public function edit(Request $request, $id){ 
        $edition = DB::table('edition as ed')
        ->leftJoin('publisher as p', 'ed.publisher_id', '=', 'p.id')
        ->leftJoin('type_of_cover as tc', 'ed.type_of_cover_id', '=', 'tc.id')
        ->leftJoin('language as l', 'ed.language_id', '=', 'l.id')
        ->select('ed.*', 'tc.name as type_of_cover', 'p.name as publisher', 'l.name as language_name')
        ->where('ed.id', $id)
        ->first();  
 
        if (!$edition) return abort(404);
        $user = auth()->user();
        $admin = $user->hasPermission('admin');
        $content_make = $user->hasPermission('content-make');   
        if (!($edition->user_id==$user->id || $admin || $content_make && !$edition->is_public) ) return abort(403);
    
        if ($request->has('submit')){

            $error = '';
            try {
                DB::table('edition')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'publisher_id' => $request->input('publisher_id'),
                    'language_id' => $request->input('language_id'),
                    'year' => $request->input('year'),
                    'size' => $request->input('size'),
                    'pages' => $request->input('pages'),
                    'isbn' => $request->input('isbn'),
                    'type_of_cover_id' => $request->input('type_of_cover_id'),
                    'format' => $request->input('format'),
                    'about' => $request->input('about'),
                    'notes' => $request->input('notes'),
                    'links' => $request->input('links'),
                    'main_img' => $request->input('main_img'),
                    'user_id' => $content_make? $user->id : $edition->user_id,
                    'is_public' => $content_make? true: false,
                ]);
                
                $error .= LogService::add($id,'edition','update');
            }
            catch (\Exception $e) {
                $error .= 'Помилка при вставці даних. ';
            }

            // Автори
            try{
                $avtors_del = $request->input('del_avtor');
                if ($avtors_del)
                foreach ($avtors_del as $avtor){
                    DB::table('person_edition')
                    ->where('ed_id', $id)
                    ->where('p_id', $avtor)
                    ->where('type', 1)
                    ->delete();
                }
                $avtors_add_id = $request->input('add_avtor_id');
                $avtors_add = $request->input('add_avtor');
                if ($avtors_add){
                    for ($i=0; $i<count($avtors_add);$i++){
                        DB::table('person_edition')->insert([
                            'ed_id' => $id,
                            'p_id' => $avtors_add_id[$i],
                            'name' => $avtors_add[$i],
                            'type' => 1, // тип = автор
                        ]);                    
                    }
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці авторів видання. ';
            }

            // Дизайнери
            try{
                $designers_del = $request->input('del_designer');
                if ($designers_del)
                foreach ($designers_del as $designer){
                    DB::table('person_edition')
                    ->where('ed_id', $id)
                    ->where('p_id', $designer)
                    ->where('type', 3)
                    ->delete();
                }
                $designers_add_id = $request->input('add_designer_id');
                $designers_add = $request->input('add_designer');
                if ($designers_add){
                    for ($i=0; $i<count($designers_add);$i++){
                        DB::table('person_edition')->insert([
                            'ed_id' => $id,
                            'p_id' => $designers_add_id[$i],
                            'name' => $designers_add[$i],
                            'type' => 3, // тип = дизайнер
                        ]);                    
                    }
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці дизайнерів видання. ';
            }

            // Ілюстратори
            try{
                $illustrators_del = $request->input('del_illustrator');
                if ($illustrators_del)
                foreach ($illustrators_del as $illustrator){
                    DB::table('person_edition')
                    ->where('ed_id', $id)
                    ->where('p_id', $illustrator)
                    ->where('type', 4)
                    ->delete();
                }
                $illustrators_add_id = $request->input('add_illustrator_id');
                $illustrators_add = $request->input('add_illustrator');
                if ($illustrators_add){
                    for ($i=0; $i<count($illustrators_add);$i++){
                        DB::table('person_edition')->insert([
                            'ed_id' => $id,
                            'p_id' => $illustrators_add_id[$i],
                            'name' => $illustrators_add[$i],
                            'type' => 4, // тип = ілюстратор
                        ]);                    
                    }
                }
            }
            catch (Exception $e) {
                $error .= 'Помилка при обробці ілюстраторів видання. ';
            }

            // Вміст
            try{
                $items_del = $request->input('del_item');
                if ($items_del)
                foreach ($items_del as $item){
                    DB::table('edition_item')
                    ->where('id', $item['id'])
                    ->delete();
                }

                $items_add = $request->input('add_item');
                if ($items_add){
                    foreach ($items_add as $item){
                        $insertedItemId = DB::table('edition_item')->insertGetId([
                            'ed_id' => $id,
                            'w_id' => $item['w_id'],
                            'name' => $item['name'],
                            'pages' => $item['pages'],
                            'level' => $item['level'],
                            'number' => $item['number'],
                        ]);  
                        if (isset($item['translator']))
                        foreach ($item['translator'] as $translator){
                            try{
                                DB::table('translator_item')->insert([
                                    'it_id' => $insertedItemId,
                                    'tr_id' => $translator['tr_id'],
                                    'name' => $translator['name'],
                                ]);  
                            }
                            catch (\Exception $e) {
                                $error .= 'Помилка при обробці перекладачів. ';
                            }
                        }                
                    }
                }

                $items_edit = $request->input('edit_item');
                if ($items_edit){
                    foreach ($items_edit as $item){
                        DB::table('edition_item')
                        ->where('id', $item['id'])
                        ->update([
                            'name' => $item['name'],
                            'pages' => $item['pages'],
                            'level' => $item['level'],
                            'number' => $item['number'],
                        ]);                 
                    }
                }
            }
            catch (Exception $e) {
                $error .= 'Помилка при обробці вмісту видання. ';
            }

            // Перекладачі
            try{      
                $add_translators = $request->input('add_translator');
                if ($add_translators) {
                    foreach ($add_translators as $translator) {
                        DB::table('translator_item')->insert([
                            'tr_id' => $translator['tr_id'],
                            'it_id' => $translator['it_id'],
                            'name' => $translator['name'],
                        ]);  
                    }
                }
                $del_translators = $request->input('del_translator');
                if ($del_translators) {
                    foreach ($del_translators as $translator) {
                        DB::table('translator_item')
                        ->where('tr_id', $translator['tr_id'])
                        ->where('it_id', $translator['it_id'])
                        ->delete();
                    }
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці перекладачів. ';
            }

            // Картинки 
            try{
                $del_imgs = $request->input('del_imgs');
                if ($del_imgs)
                foreach ($del_imgs as $img){
                    ImageService::delImg($img);
                }
                $imgs = $request->input('imgs');
                if ($imgs)
                foreach ($imgs as $img){
                    $mes = ImageService::saveImg($img,'edition',$id);
                    if ($mes!='') $error .= $mes.' ';
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці зображення. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else return redirect()->route('edition',$id)->with('success', 'Зміни внесено успішно.');
        }
        else {
            $languages = DB::select('SELECT * FROM language ORDER BY name');   
            $types_of_cover = DB::select('SELECT * FROM type_of_cover ORDER BY name');
            $imgs_edit = ImageService::getImgs('edition',$id);  
            $genres = DB::select('SELECT * FROM genre ORDER BY name');

            $persons_edition = DB::table('edition as ed')
            ->Join('person_edition as pe', 'ed.id', '=', 'pe.ed_id')
            ->Join('person as p', 'p.id', '=', 'pe.p_id')
            ->select('p.id as id', 'p.name as base_name', 'pe.type as type', 'pe.name as name')
            ->where('ed.id', $id)
            ->get();    
            $avtors = [];
            $designers = [];
            $illustrators = [];
            
            $items = DB::table('edition as ed')
            ->Join('edition_item as ei', 'ed.id', '=', 'ei.ed_id')
            ->Join('work as w', 'w.id', '=', 'ei.w_id')
            ->select('w.id as w_id', 'w.name as base_name', 'ei.id as id', 'ei.name as name', 'ei.pages as pages', 
            'ei.level as level', 'ei.number as number')
            ->where('ed.id', $id)
            ->orderBy('number')
            ->get();  
            foreach ($items as $item){
                $item->translators = DB::table('person as p')
                ->Join('translator_item as ti', 'p.id', '=', 'ti.tr_id')
                ->Join('edition_item as ei', 'ei.id', '=', 'ti.it_id')
                ->select('p.id as tr_id', 'p.name as base_name', 'ti.name as name')
                ->where('ei.id', $item->id)
                ->get();  
            }

            foreach ($persons_edition as $person) {
                if ($person->type == 1) {
                    $avtors[] = $person;
                }
                elseif ($person->type == 3) {
                    $designers[] = $person;
                }
                elseif ($person->type == 4) {
                    $illustrators[] = $person;
                }
            } 
            return view('content-maker.edition-form', [
                'title' => 'Майстерня - Редагування видання',
                'edition' => $edition,
                'languages' => $languages,
                'types_of_cover' => $types_of_cover,
                'genres' => $genres,
                'imgs_edit' => $imgs_edit,
                'avtors' => $avtors,
                'designers' => $designers,
                'illustrators' => $illustrators,
                'items' => $items,
            ]);
        }
    }

    public function searchOne(Request $request){        
        $user = auth()->user();
        $content_make = auth()->id() && auth()->user()->hasPermission('content-make');  
        $name = $request->input('name');
        $year = $request->input('year');
    
        if ($user && $name && $year) {
            $name = is_string($name) ? "%" . str_replace("'", "", $name) . "%" : "%" . implode('%', array_map('str_replace', ["'", ""], $name)) . "%";
    
            $results = DB::select("
                SELECT id, name 
                FROM edition 
                WHERE name LIKE ? 
                AND year = ? 
                AND (is_public = 1 OR user_id = ? OR ? = 1)", [$name, $year, $user->id, $content_make]);
    
            return response()->json($results);
        } else {
            return null;
        }
    }
    
    
    
}
