<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DoubleController extends Controller
{
    public function index(){
        $doubles = DB::select(" SELECT all_doubles.id1, all_doubles.id2, all_doubles.name1, all_doubles.name2, all_doubles.type FROM (
                (SELECT DISTINCT p1.id AS id1, p2.id AS id2, p1.name AS name1, p2.name AS name2, 'person' AS type
                FROM person p1
                INNER JOIN person p2 ON p1.id < p2.id AND (p1.name = p2.name OR p1.name LIKE CONCAT('%', p2.name, '%') OR p2.name LIKE CONCAT('%', p1.name, '%')))
                
                UNION ALL
                
                (SELECT DISTINCT w1.id AS id1, w2.id AS id2, w1.name AS name1, w2.name AS name2, 'work' AS type
                FROM work w1
                INNER JOIN work w2 ON w1.id < w2.id AND (w1.name = w2.name OR w1.name LIKE CONCAT('%', w2.name, '%') OR w2.name LIKE CONCAT('%', w1.name, '%')))
                
                UNION ALL
                
                (SELECT DISTINCT e1.id AS id1, e2.id AS id2, e1.name AS name1, e2.name AS name2, 'edition' AS type
                FROM edition e1
                INNER JOIN edition e2 ON e1.id < e2.id AND (e1.name = e2.name OR e1.name LIKE CONCAT('%', e2.name, '%') OR e2.name LIKE CONCAT('%', e1.name, '%')))
                
                UNION ALL
                
                (SELECT DISTINCT p1.id AS id1, p2.id AS id2, p1.name AS name1, p2.name AS name2, 'publisher' AS type
                FROM publisher p1
                INNER JOIN publisher p2 ON p1.id < p2.id AND (p1.name = p2.name OR p1.name LIKE CONCAT('%', p2.name, '%') OR p2.name LIKE CONCAT('%', p1.name, '%')))
            ) AS all_doubles
            LEFT JOIN not_doubles nd ON (nd.id1 = all_doubles.id1 AND nd.id2 = all_doubles.id2 AND nd.type = all_doubles.type)
            WHERE nd.id1 IS NULL
            ORDER BY all_doubles.name1
        ");

        return view('content-maker.doubles',[
            'title' => 'Майстерня - Дублі',
            'doubles' => $doubles,
        ]);
    }

    public function addNotADouble(Request $request){

        $error = '';
        try {
            $insertedId = DB::table('not_doubles')->insertGetId([
                'id1' => $request->input('id1'),
                'id2' => $request->input('id2'),
                'type' => $request->input('type'),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Виняток "не дубль" додано успішно');    
    }   

    public function unite(Request $request){ 
        $error = '';   
        $type = $request->input('type');
        $main_id = $request->input('main_id');
        $id = $request->input('id');        
        
        if (!$id || !$main_id)
        return abort(404);

        if ($id==$main_id){
            $error = 'Ви справді вважаєте дублем один об\'єкт???';
            return redirect()->back()->with('error', $error);
        }

        try {
            DB::beginTransaction();

            switch ($type){
                case 'person': 
                    // Заміна
                    DB::table('avtor_work')->where('av_id', $id)->update(['av_id' => $main_id]);
                    DB::table('person_edition')->where('p_id', $id)->update(['p_id' => $main_id]);
                    DB::table('translator_item')->where('tr_id', $id)->update(['tr_id' => $main_id]);
                    // Видалення
                    DB::table('p_alt_names')->where('person_id', $id)->delete();
                    break;

                case 'work': 
                    // Заміна
                    DB::table('classificator')->where('w_id', $id)->update(['w_id' => $main_id]);
                    DB::table('edition_item')->where('w_id', $id)->update(['w_id' => $main_id]);
                    DB::table('quote')->where('w_id', $id)->update(['w_id' => $main_id]);
                    DB::table('rating')->where('w_id', $id)->update(['w_id' => $main_id]);
                    DB::table('review')->where('w_id', $id)->update(['w_id' => $main_id]);
                    DB::table('work_shelf')->where('w_id', $id)->update(['w_id' => $main_id]);
                    // Видалення
                    DB::table('anotation')->where('work_id', $id)->delete();
                    DB::table('avtor_work')->where('w_id', $id)->delete();
                    DB::table('w_alt_names')->where('work_id', $id)->delete();
                    DB::table('work_cycle')->where('w_id', $id)->delete();
                    break;

                case 'edition':
                    // Видалення
                    DB::table('person_edition')->where('ed_id', $id)->delete();
                    $edition_items = DB::table('edition_item')->where('ed_id', $id)->get();
                    foreach ($edition_items as $item)
                        DB::table('translator_item')->where('it_id', $item->id)->delete();
                    DB::table('edition_item')->where('ed_id', $id)->delete();
                    break;

                case 'publisher': 
                    // Заміна
                    DB::table('edition')->where('publisher_id', $id)->update(['publisher_id' => $main_id]);
                    break;
            }
            
            $insertedId = DB::table('doubles')->insertGetId([
                'main_id' => $main_id,
                'id' => $id,
                'type' => $type,
            ]);
            DB::table($type)->where('id', $id)->delete();

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            $error = 'Помилка при об\'єднанні дублю. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Дубль об\'єднано успішно.');
    }
    

}
