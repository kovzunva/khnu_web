<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'message', 'link', 'read_at', 'icon', 'item_id'];
    public static $iconMapping = [
        'message' => '<i class="fa-regular fa-comment"></i>',
        'like' => '<i class="fa-solid fa-heart"></i>',
        'blog' => '<i class="fa-solid fa-scroll"></i>',
        'work' => '<i class="fa-solid fa-book"></i>',
        'moderator' => '<i class="fa-solid fa-circle-exclamation"></i>',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }


    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getIcon()
    {
        return $this->icon? Notification::$iconMapping[$this->icon]:'';
    }
}