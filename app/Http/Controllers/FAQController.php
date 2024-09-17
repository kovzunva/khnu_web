<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogService;
use App\Models\User;

class FAQController extends Controller
{   
    public function index(){
        $items = DB::select('SELECT * FROM faq ORDER BY sort_index, name');
        
        return view('client.faqs',[
            'title' => 'Довідка',
            'items' => $items,
        ]);
    }  

    public function show($id){ 
        $faq = DB::table('faq as f')
            ->where('f.id', $id)
            ->first();  
        
        if (!$faq) return abort(404);
        
        $currentId = $faq->id;
        
        $currentFaq = DB::table('faq')
            ->where('id', $currentId)
            ->first();

        $previous = DB::table('faq')
            ->select('id', 'name')
            ->where('sort_index', '<=', $currentFaq->sort_index)
            ->where('id', '<>', $currentFaq->id)
            ->orderBy('sort_index', 'desc')
            ->orderBy('name', 'desc')
            ->first();

        $next = DB::table('faq')
            ->select('id', 'name')
            ->where('sort_index', '>=', $currentFaq->sort_index)
            ->where('id', '<>', $currentFaq->id)
            ->orderBy('sort_index', 'asc')
            ->orderBy('name', 'asc')
            ->first();
    
        return view('client.faq',[
            'title' => 'Довідкова стаття «'.$faq->name.'»',
            'faq' => $faq,
            'previous' => $previous,
            'next' => $next,
        ]);
    }
    

    public function showAll(){
        $items = DB::select('SELECT * FROM faq ORDER BY sort_index, name');
        
        return view('admin.faqs',[
            'title' => 'Адмінка - Довідка',
            'items' => $items,
        ]);
    }  

    public function emptyForm(Request $request){
        return view('admin.faq-form',[
            'title' => 'Адмінка - Додавання довідкової статті',
            'faq' => null,
        ]);
    }

    public function add(Request $request){

        $error = '';
        try {
            $user = auth()->user();
            $insertedId = DB::table('faq')->insertGetId([
                'name' => $request->input('name'),
                'keywords' => $request->input('keywords'),
                'content' => $request->input('content'),
                'sort_index' => $request->input('sort_index'),
                'user_id' => $user->id,
                'created_at' => new \DateTime(),
            ]);
            $error .= LogService::add($insertedId,'faq','add');
        }
        catch (\Exception $e) {
            $error .= 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            if ($insertedId) return redirect()->route('faq.editForm', ['id' => $insertedId])->with('error', $error);
            else return redirect()->back()->with('error', $error);
        }
        else return redirect()->route('faq.showAll', ['id' => $insertedId])->with('success', 'Довідкову інформацію додано успішно');    
    }
    
    public function edit(Request $request, $id){      
        $faq = DB::table('faq as f')
        ->where('f.id', $id)
        ->first();  
 
        if (!$faq) return abort(404);

        if ($request->has('submit')){ 

            $error = '';
            try {
                DB::table('faq')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'keywords' => $request->input('keywords'),
                    'content' => $request->input('content'),
                    'sort_index' => $request->input('sort_index'),
                ]);
                
                $error .= LogService::add($id,'faq','update');
            }
            catch (Exception $e) {
                $error .= 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else return redirect()->route('faq.showAll', ['id' => $id])->with('success', 'Зміни внесено успішно.');
        }
        else {        
            return view('admin.faq-form', [
                'title' => 'Адмінка - Редагування довідкової статті',
                'faq' => $faq,
            ]);
        }
    }

    public function search(Request $request){
        $term = $request->input('term');
        if ($term){
            $results = DB::select("SELECT id, name FROM faq 
            WHERE name LIKE '%$term%' or keywords LIKE '%$term%'");

            return response()->json($results);
        }
        else return null;
    }    
}
