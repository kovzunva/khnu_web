<?php

// app/Models/Comment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'user_id', 'item_id', 'type_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'answer_to');
    }

    public function comment()
    {
        return $this->hasOne(Comment::class, 'id', 'answer_to');
    }

}
