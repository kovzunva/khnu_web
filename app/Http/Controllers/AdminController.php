<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ImageService;


class AdminController extends Controller
{
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function basic(){
        $data = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM users) as users,
                (SELECT COUNT(*) FROM review) as reviews,
                (SELECT COUNT(*) FROM rating) as ratings,
                (SELECT COUNT(*) FROM blogs) as blogs,
                (SELECT COUNT(*) FROM person) as persons,
                (SELECT COUNT(*) FROM work) as works,
                (SELECT COUNT(*) FROM edition) as editions,
                (SELECT COUNT(*) FROM publisher) as publishers
        ")[0];

        return view('admin.basic',[
            'title' => 'Адмінка',
            'data' => $data,
        ]);
    }

    public function delImg($del_img){
        $this->imageService->delImg($del_img);
    }

    public function categories(){
        $genres = DB::select("SELECT * FROM genre ORDER BY name");
        $languages = DB::select("SELECT * FROM language ORDER BY name");
        $countries = DB::select("SELECT * FROM country ORDER BY name");
        $blog_categories = DB::select("SELECT * FROM blog_categories ORDER BY name");
        $types_of_cover = DB::select("SELECT * FROM type_of_cover ORDER BY name");

        return view('admin.categories',[
            'title' => 'Адмінка - Категорії',
            'genres' => $genres,
            'languages' => $languages,
            'countries' => $countries,
            'blog_categories' => $blog_categories,
            'types_of_cover' => $types_of_cover,
        ]);
    }

    public function categoryAdd(Request $request, $table){

        $error = '';
        try {
            $insertedId = DB::table($table)->insertGetId([
                'name' => $request->input('name'),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error)->with('table', $table);
        else return redirect()->back()->with('success', 'Категорію додано успішно')->with('table', $table);    
    }    
    public function categoryEdit(Request $request, $table, $id){        
        $category = DB::table($table)->where('id', $id)->first();  
    
        if ($category && $request->has('submit')){
            $error = '';
            try {
                DB::table($table)->where('id', $id)->update([
                    'name' => $request->input('name'),
                ]);
            }
            catch (\Exception $e) {
                $error = 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error)->with('table', $table);
            else return redirect()->back()->with('success', 'Зміни внесено успішно.')->with('table', $table);
        }
        else abort(404);
    }

    public function categoryDel(Request $request, $table, $id){        
        $category = DB::table($table)->where('id', $id)->first();  
    
        if ($category){
            $error = '';
            try {
                DB::table($table)->where('id', $id)->delete();
            }
            catch (\Exception $e) {
                $error = 'Помилка при видаленні даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error)->with('table', $table);
            else return redirect()->back()->with('success', 'Категорію видалено успішно.')->with('table', $table);
        }
        else abort(404);
    }
    
    public function classificator(){
        $classificator_options = DB::select("SELECT co.*, cg.name as group_name, cg.radio as radio, co.change_id as change_id, 
            coc.name as change_option from classificator_option as co
            LEFT JOIN classificator_group AS cg ON cg.id = co.group_id
            LEFT JOIN classificator_option as coc on coc.id = co.change_id

            ORDER BY cg.sort_index, cg.id, co.change DESC, co.sort_index, co.name"); 
        $classificator_groups = DB::select("SELECT * FROM classificator_group ORDER BY sort_index");
        $groups = [];
        foreach ($classificator_groups as $group){            
            $groups[$group->name] = new \stdClass();
            $groups[$group->name]->options = [];
            $groups[$group->name]->id = $group->id;
        }

        foreach ($classificator_options as $option) {
            $group = $option->group_name;
            // Розподіл підопцій
            if ($option->change_id && isset($groups[$group]->options[$option->change_id])) {
                $groups[$group]->options[$option->change_id]->suboptions[$option->id] = $option;
            }
            else {
                $groups[$group]->options[$option->id] = $option;
            }
        }
        
        $classificator_options_change = DB::select("SELECT * FROM classificator_option as co WHERE co.change = 1 ORDER BY sort_index, name");

        return view('admin.classificator',[
            'title' => 'Адмінка - Класифікатор',
            'classificator_groups' => $classificator_groups,
            'groups' => $groups,
            'classificator_options_change' => $classificator_options_change,
        ]);
    }

    public function classificatorGroupAdd(Request $request){

        $error = '';
        try {
            $insertedId = DB::table('classificator_group')->insertGetId([
                'name' => $request->input('name'),
                'radio' => $request->input('radio')=='1'? 1:0,
                'sort_index' => $request->input('sort_index'),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error)->with('table', 'classificator_group');
        else return redirect()->back()->with('success', 'Категорію додано успішно')->with('table', 'classificator_group');    
    }    
    public function classificatorGroupEdit(Request $request, $id){        
        $classificator_group = DB::table('classificator_group')->where('id', $id)->first();  
    
        if ($classificator_group && $request->has('submit')){
            $error = '';
            try {
                DB::table('classificator_group')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'radio' => $request->input('radio')=='1'? 1:0,
                    'sort_index' => $request->input('sort_index'),
                ]);
            }
            catch (\Exception $e) {
                $error = 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error)->with('table', 'classificator_group');
            else return redirect()->back()->with('success', 'Зміни внесено успішно.')->with('table', 'classificator_group');
        }
        else abort(404);
    }

    public function classificatorOptionAdd(Request $request){

        $error = '';
        try {
            $insertedId = DB::table('classificator_option')->insertGetId([
                'name' => $request->input('name'),
                'group_id' => $request->input('group_id'),
                'change_id' => $request->input('change_id'),
                'change' => $request->input('change')=='1'? 1:0,
                'sort_index' => $request->input('sort_index'),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error)->with('table', 'classificator_option'.$request->input('group_id'));
        else return redirect()->back()->with('success', 'Категорію додано успішно')->with('table', 'classificator_option'.$request->input('group_id'));    
    }    
    public function classificatorOptionEdit(Request $request, $id){        
        $option = DB::table('classificator_option')->where('id', $id)->first();  
    
        if ($option && $request->has('submit')){
            $error = '';
            try {
                DB::table('classificator_option')->where('id', $id)->update([
                    'name' => $request->input('name'),
                    'group_id' => $request->input('group_id'),
                    'change_id' => $request->input('change_id'),
                    'change' => $request->input('change')=='1'? 1:0,
                    'sort_index' => $request->input('sort_index'),
                ]);
            }
            catch (Exception $e) {
                $error = 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error)->with('table',  'classificator_option'.$option->group_id);
            else return redirect()->back()->with('success', 'Зміни внесено успішно.')->with('table', 'classificator_option'.$option->group_id);
        }
        else abort(404);
    }

}
