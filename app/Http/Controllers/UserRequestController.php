<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserRequestController extends Controller
{
    public function form(Request $request){
        return view('client.user-request-form',[
            'title' => 'форма зворотного зв\'язку',
        ]); 
    }

    public function add(Request $request){
        $error = '';

        try {
            DB::table('user_request')->insert([
                'name' =>  $request->input('name'),
                'text' =>  $request->input('text'),
                'user_id' =>  Auth::user()->id,
                'created_at' =>  now(),
            ]);

        } catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->route('my-profile-user-requests')->with('success', 'Заявку додано успішно');  
    }

    public function process(Request $request, $id){
        $error = '';
        $user_request = DB::table('user_request')->where('id', $id)->first();

        try {
            DB::table('user_request')->where('id', $id)->update([
                'user_processed_id' => Auth::user()->id,
                'status' => 1,
                'responce' => $request->input('responce'),
                'processed_at' =>  now(),
            ]);
            $user = User::find($user_request->user_id);
            $user->myNotify('Адміністратор обробив Вашу заявку «'.$user_request->name.'». ',
            route('user-request.show-status',$user_request->id),'moderator');

        } catch (Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Заявку оброблено успішно');  
    }

    public function reject(Request $request, $id){
        $error = '';
        $user_request = DB::table('user_request')->where('id', $id)->first();

        try {
            DB::table('user_request')->where('id', $id)->update([
                'user_processed_id' => Auth::user()->id,
                'status' => -1,
                'responce' => $request->input('responce'),
                'processed_at' =>  now(),
            ]);
            $user = User::find($user_request->user_id);
            $user->myNotify('Адміністратор відхилив Вашу заявку «'.$user_request->name.'».',
            route('user-request.show-status'),'moderator');

        } catch (\Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') {
            return redirect()->back()->with('error', $error);
        }
        else return redirect()->back()->with('success', 'Заявку відхилено успішно');  
    }

    public function showAll(){
        $unprocessed_requests = DB::table('user_request')->whereNull('status')->get();
        $processed_requests = DB::table('user_request')->where('status', 1)->get();
        $rejected_requests = DB::table('user_request')->where('status', -1)->get();
        
        return view('admin.user-requests',[
            'title' => 'Адмінка - Заявки користувачів',
            'unprocessed_requests' => $unprocessed_requests,
            'processed_requests' => $processed_requests,
            'rejected_requests' => $rejected_requests,
        ]);
    }   

    public function show($id){
        $user_request = DB::select("SELECT * FROM user_request WHERE id = ?", [$id]);
        if (!$user_request) return abort(404);
        $user_request = $user_request[0];
        $user_request->user = User::find($user_request->user_id);
        if ($user_request->user_processed_id ) $user_request->user_processed = User::find($user_request->user_processed_id);
        
        return view('admin.user-request',[
            'title' => 'Адмінка - Заявка «'.$user_request->name.'»',
            'user_request' => $user_request,
        ]);
    }   

    public function showStatus($id){
        $user_request = DB::select("SELECT * FROM user_request WHERE id = ?", [$id]);
        if (!$user_request) return abort(404);
        $user_request = $user_request[0];
        if ($user_request->user_id != auth()->user()->id) return abort(404);

        $user_request->user = User::find($user_request->user_id);
        if ($user_request->user_processed_id ) $user_request->user_processed = User::find($user_request->user_processed_id);
        
        return view('client.user-request',[
            'title' => 'Заявка «'.$user_request->name.'»',
            'user_request' => $user_request,
        ]);
    }

    public function profileUserRequests(){
        $user_requests = DB::select("SELECT * FROM user_request WHERE user_id = ? ORDER BY id DESC", [auth()->user()->id]);
        
        return view('profile.my-user-requests',[
            'title' => 'Профіль - Заявки',
            'user_requests' => $user_requests,
        ]);
    }  
}

