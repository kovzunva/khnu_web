<?php

// app/Http/Controllers/BlogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\BlogService;

class BlogController extends Controller
{
    protected $service;

    public function __construct(BlogService $service)
    {
        $this->service = $service;
    }
    
    public function index(Request $request, $page=1){
        $items = $this->service->GetItems($request);
        $categories = $this->service->GetCategories();

        return view('client.blogs', 
        [
            'title' => "Блоги",
            'blogs' => $items,
            'service' => $this->service,
            'categories' => $categories,
        ]);
    }

    public function profileBlogs($id=null){
        $userId = $id? $id : auth()->user()->id;
        $blogs = Blog::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        $profile = $id? User::find($id) : auth()->user();

        return view('profile.blogs', [
            'profile' => $profile,
            'blogs' => $blogs,
            'title' => "Блоги користувача ".$profile->name,
        ]);
    }


    public function emptyForm(){
        if (auth()->user()->isBanned()) abort(403,'Доступ заборонено');

        $categories = BlogCategory::orderBy('name')->get();
        return view('client.blog-form', 
        [
            'blog' => null,
            'categories' => $categories,
            'title' => "Додавання блогу",
        ]);
    }

    public function add(Request $request){
        if (auth()->user()->isBanned()) abort(403,'Доступ заборонено');

        $error = '';
        try {
            $blog = Blog::create([
                'name' => $request['name'],
                'content' => $request['content'],
                'category_id' => $request['category_id'],
                'user_id' => auth()->id(),
            ]);
        }
        catch (\Exception $e) {
            $error = 'Помилка при вставці даних. ';
        }

        if ($error!='') return redirect()->back()->with('error', $error);
        else {
            return redirect()->route('blog', ['id' => $blog->id])->with('success', 'Блог успішно створено!');
        }
    }

    public function show($id){
        $blog = Blog::findOrFail($id);

        return view('client.blog', [
            'blog' => $blog,
            'title' => $blog->name,
        ]);
    }

    public function edit(Request $request, $id){
        if (auth()->user()->isBanned()) abort(403,'Доступ заборонено');

        $blog = Blog::findOrFail($id);

        if (!$blog) {
            return redirect()->back()->with('error', 'Блог не знайдено.');
        }

        if ($request->isMethod('post')) {
            try {
                $blog->update([
                    'name' => $request->input('name'),
                    'content' => $request->input('content'),
                    'category_id' => $request->input('category_id'),
                ]);

                return redirect()->route('blog', ['id' => $blog->id])->with('success', 'Зміни внесено успішно.');

            }
            catch (\Exception $e) {
                return redirect()->back()->with('error', 'Помилка при збереженні змін.');
            }
        }
        else {
            $categories = BlogCategory::orderBy('name')->get();
            return view('client.blog-form', [
                'blog' => $blog,
                'categories' => $categories,
                'title' => "Редагування блогу",
            ]);
        }
    }

    public function destroy($id){
        $blog = Blog::findOrFail($id);
        if ($blog->user_id !== auth()->user()->id) {
            if (auth()->user()->role === 'moderator' && request()->has('reason')){
                $reason = request()->input('input');
                $user = User::find($blog->user_id);
                $user->myNotify('Модератор видалив Ваш блог «'.$blog->name.'». '.$reason, 
                null,'moderator');
            }
            else return redirect()->back()->with('error', 'Ви не можете видалити цей відгук.');
        }
        $blog->delete();

        return redirect()->route('my-blogs')->with('success', 'Блог успішно видалено!');
    }   

    public function search(Request $request) {
        $term = $request->input('term');
    
        if ($term) {
            $term = '%' . $term . '%';
    
            $results = DB::select("SELECT id, name as name FROM blogs WHERE REPLACE(name, '''', '') LIKE REPLACE(?, '''', '')", [$term]);
    
            return response()->json($results);
        } else {
            return null;
        }
    }    
}

