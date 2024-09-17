<?php

// app/Http/Controllers/BlogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use app\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ProfileService;
use App\Services\ImageService;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, $page = 1)
    {   
        $items = $this->service->GetItems($request);     

        return view('client.profiles', [
            'title' => 'Користувачі',
            'profiles' => $items,
            'service' => $this->service,
        ]);
    }
    
    public function verificationNotice()
    {
        return view('auth.verify', [
            'title' => 'Підтвердження пошти',
        ]);
    }

    public function profile($id = null)
    {
        if (!$id && !auth()->check()) return redirect('/login');
        $profile = $id? User::find($id):auth()->user();

        $statistic = new \stdClass();
        if (!$id || auth()->user() && $id==auth()->user()->id){
            $statistic->data = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM review WHERE user_id = ?) as reviews,
                    (SELECT COUNT(*) FROM rating WHERE user_id = ?) as ratings,
                    (SELECT COUNT(*) FROM blogs WHERE user_id = ?) as blogs,
                    (SELECT COUNT(*) FROM shelf WHERE user_id = ?) as shelves,
                    (SELECT COUNT(*) FROM user_request WHERE user_id = ?) as user_requests
            ", [$profile->id, $profile->id, $profile->id, $profile->id, $profile->id])[0];
        }

        $ratingsCount = DB::select("
            SELECT value, COUNT(*) AS count
            FROM rating
            WHERE user_id = :userId
            GROUP BY value
            ORDER BY value
        ", ['userId' => $profile->id]);
        $statistic->ratings = [];
        foreach ($ratingsCount as $row) {
            $statistic->ratings[$row->value] = $row->count;
        }

        return view('profile.basic', [
            'title' => 'Профіль',
            'profile' => $profile,
            'statistic' => $statistic,
        ]);
    }

    public function edit(Request $request){
        $profile = auth()->user();
        
        $error = '';
        if ($request->has('submit')) {
            try {

                DB::table('users')->where('id', $profile->id)->update([
                    'about' => $request->input('about'),
                ]);
                
                // Картинка
                try{
                    $img = $request->input('img_pass');
                    if ($img){
                        if (ImageService::getImg('profile',$profile->id))
                        ImageService::delImg(ImageService::getImg('profile',$profile->id));
                        $mes = ImageService::saveImg($img,'profile',$profile->id);
                        if ($mes!='') $error .= $mes.' ';
                    }
                }
                catch (\Exception $e) {
                    $error .= 'Помилка при обробці зображення. ';
                }

                return redirect()->route('my-profile')->with('success', 'Зміни внесено успішно.');
            }
            catch (\Exception $e) {
                return redirect()->back()->with('error', 'Помилка при збереженні змін.');
            }
        }
        else {
            $img_edit = ImageService::getImg('profile',$profile->id);
            return view('profile.form', [
                'profile' => $profile,
                'img_edit' => $img_edit,
                'title' => "Редагування профілю",
            ]);
        }
    }

    public function ban(Request $request, $id){
        $error = '';
        try {
            $user = User::find($id);
            if ($user && auth()->user() && auth()->user()->hasPermission('moderate')){
                if ($request->has('input') && is_numeric($request->input('input'))) {
                    $days = intval($request->input('input'));
                    if ($days<1) $days = 3;
                }
                else {
                    $days = 3;
                }
                $user->ban($days);
                $user->myNotify("Модератор вас забанив. Кінцева дата: ".$user->banned_until, null, 'moderator');
            }
            else abort (404);
        }
        catch (\Exception $e) {
            $error = 'Помилка при баненні користувача. '.$e;
        } 
        
        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Користувача забанено успішно. Кількість днів: '.$days);  
    }

    public function unban(Request $request, $id){
        $error = '';
        try {
            $user = User::find($id);
            if ($user && auth()->user() && auth()->user()->hasPermission('moderate')){
                $user->unban();
                $user->myNotify("Модератор вас розбанив", null, 'moderator');
            }
            else abort (404);
        }
        catch (\Exception $e) {
            $error = 'Помилка при розбаненні користувача. '.$e;
        } 
        
        if ($error!='') return redirect()->back()->with('error', $error);
        else return redirect()->back()->with('success', 'Користувача розбанено успішно. ');  
    }

    public function search(Request $request) {
        $term = $request->input('term');
    
        if ($term) {
            $term = '%' . $term . '%';
    
            $results = DB::select("SELECT id, name as name FROM users WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')", [$term]);
    
            return response()->json($results);
        } else {
            return null;
        }
    }  
    
    public function myRatings(){
        $profile = auth()->user();
        $user_id = $profile->id;
        $ratings = DB::select("SELECT r.*, w.name as w_name, avtors.avtors AS w_avtors
                            FROM rating as r
                            INNER JOIN work as w on w.id = r.w_id      
                            LEFT JOIN (SELECT w.id AS work_id, GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ', ') AS avtors
                                FROM work AS w
                                INNER JOIN avtor_work AS aw ON w.id = aw.w_id
                                INNER JOIN person AS p ON p.id = aw.av_id
                                GROUP BY w.id
                            ) AS avtors ON w.id = avtors.work_id
                            WHERE r.user_id = ?
                            ORDER BY r.created_at DESC", [$user_id]);
                            
        
        return view('profile.my-ratings',[
            'title' => 'Профіль - Оцінки',
            'ratings' => $ratings,
            'profile' => $profile,
        ]);
    } 


}

