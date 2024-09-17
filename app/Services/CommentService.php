<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CommentService extends BaseItemService 
{
    public $table = "review";
    public $sort_options = [
        1 => ['name' => 'За датою додавання', 'sql' => 'id'],
    ];
    
}
