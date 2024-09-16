<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function add($item_id, $item_type, $action){
        $error = null;
        try {
            DB::table('log')->insert([
                'item_id' => $item_id,
                'item_type' => $item_type,
                'action' => $action,
                'user_id' => Auth::user()->id,
                'created_at' => new \DateTime(),
            ]);
        }        
        catch (Exception $e) {
            $error = 'Помилка при логуванні. ';
        }
        return $error;
    }
    
}
