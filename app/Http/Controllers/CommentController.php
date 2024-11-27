<?php

// app/Http/Controllers/CommentController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\Chat;
use App\Models\Notification;

class CommentController extends Controller
{
    // типи коментарів
    // 1 - блог
    // 2 - чат

    public function add(Request $request)
    {
        $user_id = auth()->user()->id;

        DB::table('comments')->insert([
            'item_id' => $request->input('item_id'),
            'item_type' => $request->input('item_type'),
            'answer_to' => $request->input('answer_to'),
            'content' => $request->input('content'),
            'user_id' => $user_id,
            'created_at' => now(),
        ]);
        
         if($request->input('answer_to')){
            $comment_to_answer = Comment::find($request->input('answer_to'));
            $user = $comment_to_answer->user;
            if ($user!=auth()->user()){
            switch($request->input('item_type')){
                case 1: $page = 'blog'; break;
                // case 2: $page = 'forum'; break;
            }
            $user->myNotify('Користувач '.auth()->user()->name.' відповів на Ваше повідомлення.', 
            route($page,$request->input('item_id')),'message',$request->input('item_id'));
            }
        }


        return redirect()->back()->with('success', 'Повідомлення додано успішно.');
    }

    public function edit(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'Ви не можете редагувати це повідомлення.');
        }
        
        $comment->content = $request->input('edit_content');
        if ($comment->content){
            $comment->save();
            return redirect()->back()->with('success', 'Повідомлення відредаговано успішно.');
        }
                
        $comment->delete();
        return redirect()->back()->with('success', 'Повідомлення видалено успішно.');
    }

    public function del($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== auth()->user()->id) {
            if (auth()->user()->role == 'moderator' && request()->has('input')) {
                $reason = request()->input('input');
                $text = substr($comment->content, 0, 100);
                $textLength = strlen($text);
                $ellipsis = ($textLength < strlen($comment->content)) ? '...' : '';
        
                $comment->user->myNotify("Модератор видалив Ваше повідомлення: «".$text.$ellipsis."». $reason", null, 'moderator');
            } else {
                return redirect()->back()->with('error', 'Ви не можете видалити це повідомлення.');
            }
        }
        $comment->delete();        
            
        if ($comment->answer_to) {
            $comment_to_answer = Comment::find($comment->answer_to);
            $user = $comment_to_answer->user;
            $user->deleteNotification('message',$comment->item_id);
        }

        return redirect()->back()->with('success', 'Повідомлення видалено успішно.');
    }
}

