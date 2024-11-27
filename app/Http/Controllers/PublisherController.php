<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogService;
use App\Services\PublisherService;
use App\Services\ImageService;
use App\Models\User;

class PublisherController extends Controller
{   
    protected $service;

    public function __construct(PublisherService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, $page = 1){        
        $items = $this->service->GetItems($request);    
        
        return view('client.publishers',[
            'title' => 'Видавництва',
            'publishers' => $items,
            'service' => $this->service,
        ]);
    }  

    public function show(Request $request, $id){  
        $publisher = $this->service->GetItem($id,$request);  
        $publisher->img = ImageService::getImg('publisher',$publisher->id);   
        $can_edit = $this->service->CanEdit($publisher); 
        
        return view('client.publisher',[
            'title' => $publisher->name,
            'publisher' => $publisher,
            'can_edit' => $can_edit,
        ]);
    }  

    public function showAll(){
        $items = DB::select('SELECT id, name FROM publisher
        WHERE year IS NULL OR country_id is null or city is null or about is null');
        
        return view('admin.items',[
            'title' => 'Адмінка - Видавництва',
            'items' => $items,
            'item_add' => 'видавництво',
            'item_type' => 'publisher',
            'item_title' => 'Видавництва'
        ]);
    }  

    public function showAllMy(){
        $items = DB::select('SELECT id, name FROM publisher WHERE user_id = ?', [auth()->user()->id]);
        
        return view('content-maker.items',[
            'title' => 'Майстерня - Видавництва',
            'items' => $items,
            'item_add' => 'видавництво',
            'item_type' => 'publisher',
            'item_title' => 'Видавництва'
        ]);
    }    

    public function emptyForm(Request $request){
        $countries = DB::select('SELECT * FROM country ORDER BY name');

        return view('content-maker.publisher-form',[
            'title' => 'Майстерня - Додавання видавництва',
            'publisher' => null,
            'countries' => $countries,
        ]);
    }

    public function add(Request $request){
        $error = '';
        try {
            $name = $request->input('name');
            $name = preg_replace('/"/', '«', $name, 1); 
            $name = preg_replace('/"/', '»', $name, 1); 

            $user = auth()->user();
            $insertedId = DB::table('publisher')->insertGetId([
                'name' => $name,
                'country_id' => $request->input('country_id'),
                'year' => $request->input('year'),
                'city' => $request->input('city'),
                'about' => $request->input('about'),
                'notes' => $request->input('notes'),
                'links' => $request->input('links'),
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);
            $error .= LogService::add($insertedId,'publisher','add');
        }
        catch (\Exception $e) {
            $error .= 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            if ($insertedId) return redirect()->route('publisher.editForm', ['id' => $insertedId])->with('error', $error);
            else return redirect()->back()->with('error', $error);
        }
        else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('publisher', ['id' => $insertedId])->with('success', 'Видавництво додано успішно.');
        else return redirect()->route('publisher.editForm', ['id' => $insertedId])->with('success', 'Видавництво додано успішно');
    }
    
    public function edit(Request $request, $id){      
        $publisher = DB::table('publisher as p')
        ->leftJoin('country as c', 'p.country_id', '=', 'c.id')
        ->select('p.*', 'c.name as country_name')
        ->where('p.id', $id)
        ->first();  
 
        if (!$publisher) return abort(404);
        $user = auth()->user();
        $admin = $user->hasPermission('admin');
        $content_make = $user->hasPermission('content-make');   
        if (!($publisher->user_id==$user->id || $admin || $content_make && !$publisher->is_public) ) return abort(403);

        if ($request->has('submit')){ 

            $error = '';
            try {
                DB::table('publisher')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'country_id' => $request->input('country_id'),
                    'year' => $request->input('year'),
                    'city' => $request->input('city'),
                    'about' => $request->input('about'),
                    'notes' => $request->input('notes'),
                    'links' => $request->input('links'),
                    'user_id' => $content_make? $user->id : $publisher->user_id,
                    'is_public' => $content_make? true: false,
                ]);
                
                $error .= LogService::add($id,'publisher','update');
            }
            catch (Exception $e) {
                $error .= 'Помилка при вставці даних. ';
            }

            // Картинка
            try{
                $img = $request->input('img_pass');
                if ($img){
                    if (ImageService::getImg('publisher',$publisher->id))
                        ImageService::delImg(ImageService::getImg('publisher',$publisher->id));
                    $mes = ImageService::saveImg($img,'publisher',$publisher->id);
                    if ($mes!='') $error .= $mes.' ';
                }
            }
            catch (\Exception $e) {
                $error .= 'Помилка при обробці зображення. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else if ($request->input('submit') == "Зберегти та переглянути") return redirect()->route('publisher', ['id' => $id])->with('success', 'Зміни внесено успішно.');
            else return redirect()->back()->with('success', 'Зміни внесено успішно.');
        }
        else {      
            $countries = DB::select('SELECT * FROM country ORDER BY name');   
            $publisher->img_edit = ImageService::getImg('publisher',$publisher->id);   
            return view('content-maker.publisher-form', [
                'title' => 'Майстерня - Редагування видавництва',
                'publisher' => $publisher,
                'countries' => $countries,
            ]);
        }
    }

    public function search(Request $request){
        $term = $request->input('term');
        $user_id = auth()->id() ?? "''";
        $content_make = auth()->id() && auth()->user()->hasPermission('content-make');  

        if ($term){
            $term = '%' . $term . '%';
            $results = DB::select("SELECT id, name FROM publisher 
            WHERE name LIKE ? AND (is_public = 1 OR user_id = ? OR ? = 1)", [$term, $user_id, $content_make]);

            return response()->json($results);
        }
        else return null;
    }

    public function quickAdd(Request $request){
        try {
            $user = auth()->user();
            $name = $request->input('name');
            $name = preg_replace('/"/', '«', $name, 1); 
            $name = preg_replace('/"/', '»', $name, 1); 
            $insertedId = DB::table('publisher')->insertGetId([
                'name' => $name,
                'user_id' => $user->id,
                'is_public' => $user->hasPermission('content-make')? true: false,
                'created_at' => new \DateTime(),
            ]);
            $error = LogService::add($insertedId,'publisher','add');
            $response['success'] = 'Видавництво "'.$request->input('name').'" додано успішно';
            $publisher = new \stdClass();
            $publisher->id = $insertedId;
            $publisher->name = $name;
            $response['publisher'] = $publisher;
        }
        catch (\Exception $e) {
            $response['error'] = 'Помилка при вставці даних. '.$e;
        }
        return response()->json($response);  
    }

    
}
