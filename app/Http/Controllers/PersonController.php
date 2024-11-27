<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\LogService;
use App\Services\DateService;
use App\Services\ImageService;
use App\Services\PersonService;

class PersonController extends Controller
{
    protected $service;

    public function __construct(PersonService $service)
    {
        $this->service = $service;
    }

    // Клієнт
    public function index(Request $request, $page = 1){
        $items = $this->service->GetItems($request);      
        
        return view('client.persons',[
            'title' => 'Персони',
            'items' => $items,
            'service' => $this->service,
        ]);
    }    

    public function show(Request $request, $id){        
        $item = $this->service->GetItem($id,$request);  
        $can_edit = $this->service->CanEdit($item); 
                                    
        return view('client.person', [
            'title' => $item->name,
            'person' => $item,
            'can_edit' => $can_edit,
        ]);
    }


    // Контент-мейкер
    public function showAll(){
        $items = DB::select('SELECT id, name FROM person WHERE bio IS NULL OR bio = ""');
        
        return view('admin.items',[
            'title' => 'Адмінка - Персони',
            'items' => $items,
            'item_add' => 'персону',
            'item_type' => 'person',
            'item_title' => 'Персони'
        ]);
    } 

    public function showAllMy(){
        $items = DB::select('SELECT id, name FROM person WHERE user_id = ?', [auth()->user()->id]);
        
        return view('content-maker.items',[
            'title' => 'Майстерня - Персони',
            'items' => $items,
            'item_add' => 'персону',
            'item_type' => 'person',
            'item_title' => 'Персони'
        ]);
    }      

    public function emptyForm(Request $request){
            return view('content-maker.person-form',[
            'title' => 'Майстерня - Додавання персони',
            'person' => null,
        ]);
    }

    public function add(Request $request){
        $birthdate = DateService::formatDateToInt($request->input('birthdate'), $request->input('birthdate_n_e'));
        $deathdate = DateService::formatDateToInt($request->input('deathdate'), $request->input('deathdate_n_e'));

        $error = '';
        try {
            $user = auth()->user();
            $insertedId = DB::table('person')->insertGetId([
                'name' => $request->input('name'),
                'birthdate' => $birthdate,
                'deathdate' => $deathdate,
                'bio' => $request->input('bio'),
                'notes' => $request->input('notes'),
                'links' => $request->input('links'),
                'is_avtor' => $request->input('is_avtor'),
                'is_translator' => $request->input('is_translator'),
                'is_designer' => $request->input('is_designer'),
                'is_illustrator' => $request->input('is_illustrator'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);
            $error .= LogService::add($insertedId,'person','add');
        }
        catch (\Exception $e) {
            $error .= 'Помилка при вставці даних. ';
        }

        // Альтернативні імена
        try{
            $alt_names_add = $request->input('add_p_alt_name');
            if ($alt_names_add)
            foreach ($alt_names_add as $alt_name){
                DB::table('p_alt_names')->insert([
                    'person_id' => $insertedId,
                    'name' => $alt_name,
                ]);
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці альтернативних імен. ';
        }

        // Картинка
        try{
            $img = $request->input('img_pass');
            if ($img){
                if (ImageService::getImg('person',$person->id))
                ImageService::delImg(ImageService::getImg('person',$person->id));
                $mes = ImageService::saveImg($img,'person',$person->id);
                if ($mes!='') $error .= $mes.' ';
            }
        }
        catch (\Exception $e) {
            $error .= 'Помилка при обробці зображення. ';
        }

        if ($error!='') {
            if (isset($insertedId) && $insertedId) return redirect()->route('person.editForm', ['id' => $insertedId])->with('error', $error);
            else return redirect()->back()->with('error', $error);
        }
        else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('person', ['id' => $insertedId])->with('success', 'Персону додано успішно');
        else return redirect()->route('person.editForm', ['id' => $insertedId])->with('success', 'Персону додано успішно');    
    }
    
    public function edit(Request $request, $id){
        $person = DB::select("SELECT * from person WHERE id = $id")[0];     
    
        if (!$person) return abort(404);
        $user = auth()->user();
        $admin = $user->hasPermission('admin');
        $content_make = $user->hasPermission('content-make');   
        if (!($person->user_id==$user->id || $admin || $content_make && !$person->is_public) ) return abort(403);

        if ($request->has('submit')){ 
            $birthdate = DateService::formatDateToInt($request->input('birthdate'), $request->input('birthdate_n_e'));
            $deathdate = DateService::formatDateToInt($request->input('deathdate'), $request->input('deathdate_n_e'));

            $error = '';
            try {
                DB::table('person')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'birthdate' => $birthdate,
                    'deathdate' => $deathdate,
                    'bio' => $request->input('bio'),
                    'notes' => $request->input('notes'),
                    'links' => $request->input('links'),
                    'is_avtor' => $request->input('is_avtor'),
                    'is_translator' => $request->input('is_translator'),
                    'is_designer' => $request->input('is_designer'),
                    'is_illustrator' => $request->input('is_illustrator'),
                    'user_id' => $content_make? $user->id : $person->user_id,
                    'is_public' => $content_make? true: false,
                ]);
                $error .= LogService::add($id,'person','update');
            }
            catch (Exception $e) {
                $error .= 'Помилка при вставці даних. ';
            }

            // Альтернативні імена
            try{
                $alt_names_del = $request->input('del_p_alt_name');
                if ($alt_names_del)
                foreach ($alt_names_del as $alt_name){
                    DB::table('p_alt_names')->where('id', $alt_name)->delete();
                }
                $alt_names_add = $request->input('add_p_alt_name');
                if ($alt_names_add)
                foreach ($alt_names_add as $alt_name){
                    DB::table('p_alt_names')->insert([
                        'person_id' => $id,
                        'name' => $alt_name,
                    ]);
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці альтернативних імен. ';
            }

            // Картинка
            try{
                $img = $request->input('img_pass');
                if ($img){
                    if (ImageService::getImg('person',$person->id))
                        ImageService::delImg(ImageService::getImg('person',$person->id));
                    $mes = ImageService::saveImg($img,'person',$person->id);
                    if ($mes!='') $error .= $mes.' ';
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці зображення. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('person',['id' => $id])->with('success', 'Зміни внесено успішно.');
            else return redirect()->route('person.editForm',['id' => $id])->with('success', 'Зміни внесено успішно.');
        }
        else {      
            $birthdate = DateService::formatDateFromInt($person->birthdate);
            $deathdate = DateService::formatDateFromInt($person->deathdate);
            $alt_names = DB::select("SELECT * from p_alt_names WHERE person_id = $id");  
            $img_edit = ImageService::getImg('person',$person->id);  
            return view('content-maker.person-form', [
                'title' => 'Майстерня - Редагування персони',
                'person' => $person,
                'img_edit' => $img_edit,
                'birthdate' => $birthdate,
                'deathdate' => $deathdate,
                'alt_names' => $alt_names,
            ]);
        }
    }

    public function search(Request $request) {
        $term = $request->input('term');
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
    
        if ($term) {
            $term = '%' . str_replace("'", "", $term) . '%';
    
            $results = DB::select("
                SELECT id, name 
                FROM person
                WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')
                AND (is_public = 1 OR user_id = ? OR ? = 1)
                UNION
                SELECT p.id, p.name
                FROM p_alt_names pan
                JOIN person p ON pan.person_id = p.id
                WHERE REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', '')
                AND (p.is_public = 1 OR p.user_id = ? OR ? = 1)
            ", [$term, $user_id, $content_make, $term, $user_id, $content_make]);
    
            return response()->json($results);
        } else {
            return null;
        }
    }
    
    public function searchAvtors(Request $request){
        $term = $request->input('term');
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
    
        if ($term) {
            $term = '%' . str_replace("'", "", $term) . '%';
    
            $results = DB::select("
                SELECT id, name 
                FROM person 
                WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')
                AND is_avtor = true
                AND (is_public = 1 OR user_id = ? OR ? = 1)
                UNION
                SELECT p.id, p.name 
                FROM p_alt_names pan
                JOIN person p ON pan.person_id = p.id
                WHERE REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', '')
                AND p.is_avtor = true
                AND (p.is_public = 1 OR p.user_id = ? OR ? = 1)
            ", [$term, $user_id, $content_make, $term, $user_id, $content_make]);
    
            return response()->json($results);
        }
        else {
            return null;
        }
    }
    
    public function searchDesigners(Request $request){
        $term = $request->input('term');
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
    
        if ($term) {
            $term = '%' . str_replace("'", "", $term) . '%';
    
            $results = DB::select("
                SELECT id, name 
                FROM person 
                WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')
                AND is_designer = true
                AND (is_public = 1 OR user_id = ? OR ? = 1)
                UNION
                SELECT p.id, p.name 
                FROM p_alt_names pan
                JOIN person p ON pan.person_id = p.id
                WHERE REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', '')
                AND p.is_designer = true
                AND (p.is_public = 1 OR p.user_id = ? OR ? = 1)
            ", [$term, $user_id, $content_make, $term, $user_id, $content_make]);
    
            return response()->json($results);
        }
        else {
            return null;
        }
    }
    
    public function searchIllustrators(Request $request){
        $term = $request->input('term');
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
    
        if ($term) {
            $term = '%' . str_replace("'", "", $term) . '%';
    
            $results = DB::select("
                SELECT id, name 
                FROM person 
                WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')
                AND is_illustrator = true
                AND (is_public = 1 OR user_id = ? OR ? = 1)
                UNION
                SELECT p.id, p.name 
                FROM p_alt_names pan
                JOIN person p ON pan.person_id = p.id
                WHERE REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', '')
                AND p.is_illustrator = true
                AND (p.is_public = 1 OR p.user_id = ? OR ? = 1)
            ", [$term, $user_id, $content_make, $term, $user_id, $content_make]);
    
            return response()->json($results);
        }
        else {
            return null;
        }
    }
    
    public function searchTranslators(Request $request){
        $term = $request->input('term');
        $user = auth()->user();
        $user_id = $user ? $user->id : "''";
        $content_make = $user && $user->hasPermission('content-make');
    
        if ($term) {
            $term = '%' . str_replace("'", "", $term) . '%';
    
            $results = DB::select("
                SELECT id, name 
                FROM person 
                WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')
                AND is_translator = true
                AND (is_public = 1 OR user_id = ? OR ? = 1)
                UNION
                SELECT p.id, p.name 
                FROM p_alt_names pan
                JOIN person p ON pan.person_id = p.id
                WHERE REPLACE(pan.name, '''', '') LIKE REPLACE(?, '''', '')
                AND p.is_translator = true
                AND (p.is_public = 1 OR p.user_id = ? OR ? = 1)
            ", [$term, $user_id, $content_make, $term, $user_id, $content_make]);
    
            return response()->json($results);
        }
        else {
            return null;
        }
    }
    

    public function quickAdd(Request $request){

        try {
            $user = auth()->user();
            $insertedId = DB::table('person')->insertGetId([
                'name' => $request->input('name'),
                'is_avtor' => $request->input('is_avtor'),
                'is_translator' => $request->input('is_translator'),
                'is_designer' => $request->input('is_designer'),
                'is_illustrator' => $request->input('is_illustrator'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);   
            $error = LogService::add($insertedId,'person','add');
            
            if ($request->input('alt_name'))
            DB::table('p_alt_names')->insert([
                'person_id' => $insertedId,
                'name' => $request->input('alt_name'),
            ]);  

            $response['success'] = 'Персону "'.$request->input('name').' | '.$request->input('is_avtor').'" додано успішно';
            $person = new \stdClass();
            $person->id = $insertedId;
            $person->name = $request->input('name');
            $response['person'] = $person;
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при вставці даних. '.$e;
        }
        return response()->json($response);  
    }
    
}
