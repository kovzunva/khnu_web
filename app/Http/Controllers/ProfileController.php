<?php

// app/Http/Controllers/BlogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follower;
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

        $orientations = null;
        $statistic = new \stdClass();
        if (!$id || auth()->user() && $id==auth()->user()->id){
            $statistic->data = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM review WHERE user_id = ?) as reviews,
                    (SELECT COUNT(*) FROM rating WHERE user_id = ?) as ratings,
                    (SELECT COUNT(*) FROM blogs WHERE user_id = ?) as blogs,
                    (SELECT COUNT(*) FROM shelf WHERE user_id = ?) as shelves,
                    (SELECT COUNT(*) FROM followers WHERE user_to_follow_id = ?) as followers,
                    (SELECT COUNT(*) FROM followers WHERE user_id = ?) as following,
                    (SELECT COUNT(*) FROM orientator WHERE user_id = ?) as orientators,
                    (SELECT COUNT(*) FROM user_request WHERE user_id = ?) as user_requests
            ", [$profile->id, $profile->id, $profile->id, $profile->id, $profile->id, $profile->id, $profile->id, $profile->id])[0];
        }
        else if (auth()->user()){
            $currentUserId = auth()->user()->id;
            $orientations = DB::select("SELECT
                    COUNT(CASE WHEN ABS(r1.value - r2.value) = 0 THEN 1 END) AS exact_match,
                    COUNT(CASE WHEN ABS(r1.value - r2.value) = 1 THEN 1 END) AS minor_mismatch,
                    COUNT(CASE WHEN ABS(r1.value - r2.value) > 1 THEN 1 END) AS major_mismatch
                FROM rating r1
                INNER JOIN rating r2 ON r1.w_id = r2.w_id AND r1.user_id <> r2.user_id
                WHERE r1.user_id = :userId1 AND r2.user_id = :userId2;
            ", ['userId1' => $currentUserId, 'userId2' => $profile->id]);
            if ($orientations) $orientations = $orientations[0];
            
            // Запит на вибірку 5 творів з найкращим збігом
            $orientations->topMatches = DB::select("
                SELECT r1.w_id, ABS(r1.value - r2.value) AS match_count, w.*, r1.value AS user1_rating, r2.value AS user2_rating
                FROM rating r1
                JOIN rating r2 ON r1.w_id = r2.w_id AND ABS(r1.value - r2.value) < 2
                INNER JOIN work_simple_view AS w ON w.id = r1.w_id
                WHERE r1.user_id = :user1 AND r2.user_id = :user2
                GROUP BY r1.w_id, user1_rating, user2_rating
                ORDER BY match_count ASC, user1_rating DESC
                LIMIT 5
            ", ['user1' => $profile->id, 'user2' => $currentUserId]);

            // Запит на вибірку 5 творів з найгіршим незбігом
            $orientations->worstMismatches = DB::select("
                SELECT r2.w_id, ABS(r1.value - r2.value) AS mismatch_count, w.*, r1.value AS user1_rating, r2.value AS user2_rating
                FROM rating r1
                JOIN rating r2 ON r1.w_id = r2.w_id AND ABS(r1.value - r2.value) > 1
                INNER JOIN work_simple_view AS w ON w.id = r1.w_id
                WHERE r1.user_id = :user1 AND r2.user_id = :user2
                GROUP BY r1.w_id, user1_rating, user2_rating
                ORDER BY mismatch_count DESC
                LIMIT 5
            ", ['user1' => $profile->id, 'user2' => $currentUserId]);

            // dd($orientations);
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
            'orientations' => $orientations,
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

    public function follow($id)
    {
        $currentUser = auth()->user();
        $userToFollow = User::findOrFail($id);

        if (!$currentUser->isFollowing($userToFollow)) {
            $follower = new Follower([
                'user_id' => $currentUser->id,
                'user_to_follow_id' => $userToFollow->id,
            ]);
            $follower->save();
        }

        return redirect()->route('profile', $userToFollow)->with('success', 'Ви тепер стежите за цим користувачем.');
    }

    public function unfollow($id)
    {
        $currentUser = auth()->user();
        $userToUnfollow = User::findOrFail($id);

        if ($currentUser->isFollowing($userToUnfollow)) {
            Follower::where('user_id', $currentUser->id)
                ->where('user_to_follow_id', $userToUnfollow->id)
                ->delete();
        }
        return redirect()->route('profile', $userToUnfollow)->with('success', 'Ви більше не стежите за цим користувачем.');
    }

    public function followers()
    {
        $user = auth()->user();
        $count = $user->followers->count();
        $users = $user->followers;
        return view('profile.users', [
            'title' => 'Профіль - Відстежувачі',
            'count' => $count,
            'users' => $users,
            'title_user_type' => 'Відстежувачі',
            'user_type' => 'відстежувачів',
        ]);
    }

    public function following()
    {
        $user = auth()->user();
        $users = $user->following;
        $count = $users->count();
        return view('profile.users', [
            'title' => 'Профіль - Відстежуються',
            'count' => $count,
            'users' => $users,
            'title_user_type' => 'Відстежуються',
            'user_type' => 'відстежуються',
        ]);
    }

    public function orientators()
    {
        $user = auth()->user();
        $users = $user->orientators();
        // dd($users);
        $count = $users->count();
        return view('profile.users', [
            'title' => 'Профіль - Користувачі-орієнтири',
            'count' => $count,
            'users' => $users,
            'title_user_type' => 'Користувачі-орієнтири',
            'user_type' => 'користувачів-орієнтирів',
        ]);
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

    public function orientator($id)
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($id);

        // Перевірка, чи користувач вже орієнтир
        $isAlreadyorientator = DB::table('orientator')
                                ->where('user_id', $currentUser->id)
                                ->where('user_orientator_id', $user->id)
                                ->exists();

        if ($isAlreadyorientator) {
            // Якщо користувач є орієнтиром, видаліть його зі списку
            DB::table('orientator')
                ->where('user_id', $currentUser->id)
                ->where('user_orientator_id', $user->id)
                ->delete();
            $message = 'Ви викинули цього користувача зі своїх орієнтирів.';
        } else {
            // Якщо користувача немає в списку, додайте його
            DB::table('orientator')->insert([
                'user_id' => $currentUser->id,
                'user_orientator_id' => $user->id,
            ]);
            $message = 'Ви зробили цього користувача своїм орієнтиром.';
        }

        return redirect()->route('profile', $user)->with('success', $message);
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

