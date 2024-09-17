<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class QuoteController extends Controller
{
    public function add(Request $request){

        $error = '';
        try {
            $insertedId = DB::table('quote')->insertGetId([
                'user_id' => auth()->user()->id,
                'w_id' => $request->input('w_id'),
                'text' => $request->input('text'),
                'created_at' => now(),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Цитату додано успішно')->with('scroll-to', 'work_tabs');    
    }
    
    public function edit(Request $request, $id){        
        $quote = DB::table('quote')->where('id', $id)->first(); 
        if ($quote && $request->has('submit') && auth()->user()->id===$quote->user_id){
            $error = '';
            try {
                DB::table('quote')->where('id', $id)->update([
                    'text' => $request->input('text'),
                ]);
            }
            catch (Exception $e) {
                $error = 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else return redirect()->back()->with('success', 'Зміни внесено успішно.')->with('scroll-to', 'quote_'.$quote->id);
        }
        else abort(404);
    }

    public function del($id){        
        $quote = DB::table('quote')->where('id', $id)->first(); 
        if ($quote->user_id !== auth()->user()->id) {
            if (auth()->user()->role === 'moderator' && request()->has('reason')){
                $work = DB::select("SELECT w.id, w.name,
                GROUP_CONCAT(p.name SEPARATOR ', ') as avtors
                FROM work AS w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                INNER JOIN person AS p ON p.id = aw.av_id
                WHERE w.id = ".$quote->w_id."
                GROUP BY w.id, w.name");   
                if ($work){
                    $work = $work[0]; 
                    $reason = request()->input('input');
                    $user = User::find($quote->user_id);
                    $user->notify('Модератор видалив Вашу цитату до книги '.$work->avtors.' «'.$work->name.'». '.$reason, 
                    null,'moderator');
                }
            }
            else return redirect()->back()->with('error', 'Ви не можете видалити цю цитату.');
        }

        DB::table('quote')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Цитату видалено успішно.');
    }
}
