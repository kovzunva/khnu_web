<?php

// app/Http/Controllers/BlogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContentMakerController extends Controller
{
    public function index(){
        $user_id = auth()->id();
        $content_maker_activity_on_types = DB::select("SELECT 
                    (SELECT COUNT(*) FROM person WHERE user_id = ?) AS person_count, 'Персони' AS person_label,
                    (SELECT COUNT(*) FROM work WHERE user_id = ?) AS work_count, 'Твори' AS work_label,
                    (SELECT COUNT(*) FROM edition WHERE user_id = ?) AS edition_count, 'Видання' AS edition_label,
                    (SELECT COUNT(*) FROM publisher WHERE user_id = ?) AS publisher_count, 'Видавництва' AS publisher_label
                ", [$user_id, $user_id, $user_id, $user_id])[0];
        $content_maker_activity_on_types = [
            'Персони' => $content_maker_activity_on_types->person_count,
            'Твори' => $content_maker_activity_on_types->work_count,
            'Видання' => $content_maker_activity_on_types->edition_count,
            'Видавництва' => $content_maker_activity_on_types->publisher_count
        ];

        // dd($content_maker_activity_on_types);
        return view('content-maker.basic', [
            'title' => 'Майстерня',
            'content_maker_activity_on_types' => $content_maker_activity_on_types,
        ]);
    }

    public function itemsToApply()
    {        
        $items = DB::select(" SELECT id, name, 'person' AS type, created_at FROM person WHERE is_public != 1
            UNION ALL
            SELECT id, name, 'work' AS type, created_at FROM work WHERE is_public != 1
            UNION ALL
            SELECT id, name, 'edition' AS type, created_at FROM edition WHERE is_public != 1
            UNION ALL
            SELECT id, name, 'publisher' AS type, created_at FROM publisher WHERE is_public != 1
            ORDER BY name;        
        ");

        return view('content-maker.items-to-apply',[
            'title' => 'Майстерня - Матеріали для затвердження',
            'items' => $items,
        ]);
    }
    
}

