<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\DateService;
use App\Services\ImageService;
use App\Services\ReviewService;

class ReviewController extends Controller
{

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }
    
    public function profileReviews($id=null){
        $user_id = $id? $id:auth()->user()->id;
        $reviews = DB::select("SELECT r.*, w.name as w_name, w.main_edition as w_main_edition,
                            avtors.avtors AS w_avtors,
                            MAX(CASE WHEN ra.user_id = ? THEN ra.value ELSE NULL END) AS w_rating
                            FROM review as r
                            INNER JOIN work as w on w.id = r.w_id      
                            LEFT JOIN edition as ed on ed.id = w.main_edition
                            LEFT JOIN rating AS ra ON w.id = ra.w_id
                            LEFT JOIN (SELECT w.id AS work_id, GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ', ') AS avtors
                                FROM work AS w
                                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                                INNER JOIN person AS p ON p.id = aw.av_id
                                GROUP BY w.id
                            ) AS avtors ON w.id = avtors.work_id
                            WHERE r.user_id = ?
                            GROUP BY r.id
                            ORDER BY r.id DESC", [$user_id, $user_id]);

                            
        foreach ($reviews as $review){
            if (!$review->w_main_edition) {
                $firstAddedEdition = DB::select("SELECT e.id, e.name, e.main_img from edition as e
                                    INNER JOIN edition_item AS ei ON e.id = ei.ed_id
                                    where ei.w_id = $review->w_id
                                    limit 1"); 
                if ($firstAddedEdition){    
                    $firstAddedEdition = $firstAddedEdition[0];        
                    $review->w_main_edition = $firstAddedEdition->id;   
                    $review->w_main_img = $firstAddedEdition->main_img;   
                }

                if(auth()->user() && auth()->user()->id != $user_id){
                    $review->my_w_rating = DB::select("SELECT value from rating
                                                    where user_id = ".auth()->user()->id." and w_id = $review->w_id");
                    if ($review->my_w_rating) $review->my_w_rating = $review->my_w_rating[0];
                }
            }            
            $review->w_img = ImageService::getImg('edition',$review->w_main_edition,$review->w_main_img);
        }

        $profile = User::find($user_id);


        
        return view('profile.reviews',[
            'title' => 'Відгуки користувача '.$profile->name,
            'reviews' => $reviews,
            'profile' => $profile,
            'service' => $this->service,
        ]);
    }    

    public function add(Request $request){

        $error = '';
        try {
            $isPublic = !auth()->user()->isBanned() && $request->input('is_public') == 'on' ? true : false;
            $insertedId = DB::table('review')->insertGetId([
                'user_id' => auth()->user()->id,
                'w_id' => $request->input('w_id'),
                'text' => $request->input('text'),
                'is_public' => $isPublic,
                'created_at' => now(),
            ]);
        }
        catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error);
        else  {
            // надсилання сповіщень підписникам
            if ($isPublic){
                $work = DB::select("SELECT w.id, w.name,
                    GROUP_CONCAT(p.name SEPARATOR ', ') as avtors
                    FROM work AS w
                    INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                    INNER JOIN person AS p ON p.id = aw.av_id
                    WHERE w.id = ".$request->input('w_id')."
                    GROUP BY w.id, w.name");   
                if ($work){      
                    $work = $work[0]; 
                }
            }
            return redirect()->back()->with('success', 'Відгук додано успішно')->with('scroll-to', 'work_tabs');    
        }
    }
    
    public function edit(Request $request, $id){        
        $review = DB::table('review')->where('id', $id)->first(); 
        // dd($request->input('is_public'));
        $isPublic = !auth()->user()->isBanned() && ($request->input('is_public') || $review->is_public) ? true : false;    
        if ($review && $request->has('submit') && auth()->user()->id===$review->user_id){
            $error = '';
            try {
                if ($request->input('text'))
                    DB::table('review')->where('id', $id)->update([
                        'text' => $request->input('text'),
                        'is_public' => $isPublic,
                    ]);
                else DB::table('review')->where('id', $id)->delete();
            }
            catch (Exception $e) {
                $error = 'Помилка при вставці даних. ';
            }

            if ($error!='') return redirect()->back()->with('error', $error);
            else{
                if ($isPublic){
                    $work = DB::select("SELECT w.id, w.name,
                        GROUP_CONCAT(p.name SEPARATOR ', ') as avtors
                        FROM work AS w
                        INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                        INNER JOIN person AS p ON p.id = aw.av_id
                        WHERE w.id = ".$request->input('w_id')."
                        GROUP BY w.id, w.name");
                    if ($work){
                        $work = $work[0];
                    }
                }
                return redirect()->back()->with('success', 'Зміни внесено успішно.')->with('scroll-to', 'review_'.$review->id);
            }
        }
        else abort(404);
    }

    public function del($id){        
        $review = DB::table('review')->where('id', $id)->first(); 
        if ($review->user_id !== auth()->user()->id) {
            if (auth()->user()->role === 'moderator' && request()->has('reason')){
                $work = DB::select("SELECT w.id, w.name,
                GROUP_CONCAT(p.name SEPARATOR ', ') as avtors
                FROM work AS w
                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                INNER JOIN person AS p ON p.id = aw.av_id
                WHERE w.id = ".$review->w_id."
                GROUP BY w.id, w.name");   
                if ($work){
                    $work = $work[0]; 
                    $reason = request()->input('input');
                    $user = User::find($review->user_id);
                    $user->myNotify('Модератор видалив Ваш відгук до книги '.$work->avtors.' «'.$work->name.'». '.$reason, 
                    null,'moderator');
                }
            }
            else return redirect()->back()->with('error', 'Ви не можете видалити цей відгук.');
        }

        DB::table('review')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Відгук видалено успішно.');
    }
}
