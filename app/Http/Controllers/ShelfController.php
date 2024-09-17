<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ImageService;

class ShelfController extends Controller
{
    public function workToShelf(Request $request, $id){
        $error = '';

        try {
            if (!$request->has('sh_id')){
                $sh_id = DB::table('shelf')->insertGetId([
                    'name' =>  $request->input('name'),
                    'user_id' =>  Auth::user()->id,
                ]);
            }
            else $sh_id = $request->input('sh_id');

            $existingLink = DB::table('work_shelf')
                ->where('w_id', $id)
                ->where('sh_id', $sh_id)
                ->exists();

            if ($existingLink) {
                // Видалення з полиці
                DB::table('work_shelf')
                    ->where('w_id', $id)
                    ->where('sh_id', $sh_id)
                    ->delete();
            }
            else {
                // Додавання на полицю
                DB::table('work_shelf')->insert([
                    'w_id' => $id,
                    'sh_id' => $sh_id,
                ]);
            }
        } catch (\Exception $e) {
            $error = 'Помилка при виконанні операції. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->route('work', ['id' => $id])->with('success', 'Книжкову полицю оброблено успішно');  
    }

    public function addToShelf(Request $request){
        $error = '';

        try {
            DB::table('work_shelf')->insert([
                'w_id' => $request->input('w_id'),
                'sh_id' => $request->input('sh_id'),
            ]);
        }
        catch (\Exception $e) {
            $error = 'Помилка при виконанні операції. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Книгу додано до полиці успішно');  
    }

    public function add(Request $request){
        $error = '';

        try {
            $sh_id = DB::table('shelf')->insertGetId([
                'name' =>  $request->input('name'),
                'user_id' =>  Auth::user()->id,
            ]);

        } catch (\Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Книжкову полицю додано успішно');  
    }

    public function edit(Request $request, $id){
        $error = '';
        $shelf = DB::table('shelf')->where('id', $id)->first();
        if ($shelf->user_id !== auth()->user()->id)
        return abort(404);

        try {
            DB::table('shelf')->where('id', $id)->update([
                'name' => $request->input('name'),
            ]);

        } catch (\Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Книжкову полицю відредаговано успішно');  
    }

    public function del($id){   
        $shelf = DB::table('shelf')->where('id', $id)->first();
        if ($shelf->user_id !== auth()->user()->id)
        return abort(404);

        DB::table('work_shelf')->where('sh_id', $id)->delete();
        DB::table('shelf')->where('id', $id)->delete();

        return redirect()->route('my-profile-shelves')->with('success', 'Книжкову полицю видалено успішно.');
    }

    public function delWork($id, $w_id){
        $error = '';

        try {
            $shelf = DB::select("SELECT * FROM shelf WHERE id = ?", [$id])[0];
            if ($shelf->user_id==auth()->user()->id)
                DB::table('work_shelf')
                    ->where('w_id', $w_id)
                    ->where('sh_id', $id)
                    ->delete();
            else $error = 'Помилка при виконанні операції. ';
        } catch (\Exception $e) {
            $error = 'Помилка при виконанні операції. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Книгу прибрано з полиці успішно');  
    }

    public function profileShelves($id=null){
        $user_id = $id? $id:auth()->user()->id;
        $shelves = DB::select("SELECT s.*, 
                        (SELECT COUNT(*) FROM work_shelf WHERE sh_id = s.id) AS works_count
                        FROM shelf AS s
                        WHERE user_id = ?
                        ORDER BY name", [$user_id]);

        $profile = User::find($user_id);
        
        return view('profile.shelves',[
            'title' => 'Книжкові полиці '.$profile->name,
            'shelves' => $shelves,
            'profile' => $profile,
        ]);
    }   

    public function show($id){
        $shelf = DB::select("SELECT * FROM shelf WHERE id = ?", [$id]);
        if (!$shelf) return abort(404);
        $shelf = $shelf[0];

        if (!auth()->user() || auth()->user()->id!=$shelf->user_id)
        return abort(404);

        $shelf->user = User::find($shelf->user_id);
        $user_id = auth()->user()? auth()->user()->id:null;

        $shelf->works = DB::select("SELECT w.id as id, w.name as name, w.main_edition as main_edition,
                            av.avtors AS avtors, SUBSTRING_INDEX(GROUP_CONCAT(an.text ORDER BY an.id ASC SEPARATOR '|'), '|', 1) AS anotation,
                            MAX(CASE WHEN ra.user_id = ? THEN ra.value ELSE NULL END) AS rating
                            FROM work as w
                            INNER JOIN work_shelf as ws on w.id = ws.w_id  
                            LEFT JOIN anotation AS an ON w.id = an.work_id 
                            LEFT JOIN edition as ed on ed.id = w.main_edition
                            LEFT JOIN rating AS ra ON w.id = ra.w_id
                            LEFT JOIN (SELECT w.id AS work_id, GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ', ') AS avtors
                                FROM work AS w
                                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                                INNER JOIN person AS p ON p.id = aw.av_id
                                GROUP BY w.id
                            ) AS av ON w.id = av.work_id
                            WHERE ws.sh_id = ?
                            GROUP BY w.id
                            ORDER BY rating DESC", [$user_id, $id]);
                            
        foreach ($shelf->works as $work){
            if (!$work->main_edition) {
                $firstAddedEdition = DB::select("SELECT e.id, e.name, e.main_img from edition as e
                                    INNER JOIN edition_item AS ei ON e.id = ei.ed_id
                                    where ei.w_id = $work->id
                                    limit 1"); 
                if ($firstAddedEdition){    
                    $firstAddedEdition = $firstAddedEdition[0];        
                    $work->main_edition = $firstAddedEdition->id;   
                    $work->main_img = $firstAddedEdition->main_img;   
                }

                if(auth()->user() && auth()->user()->id != $user_id){
                    $work->my_rating = DB::select("SELECT value from rating
                                                    where user_id = ".auth()->user()->id." and w_id = $work->w_id");
                    if ($work->my_rating) $work->my_rating = $work->my_rating[0];
                }
            }            
            $work->img = ImageService::getImg('edition', $work->main_edition, $work->main_img);
        }
        
        return view('client.shelf',[
            'title' => 'Книжкова полиця «'.$shelf->name.'»',
            'shelf' => $shelf,
        ]);
    }   
}

