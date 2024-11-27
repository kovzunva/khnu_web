<?php

// app/Models/Blog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'content', 'category_id', 'user_id'];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'item_id')
            ->where('item_type', 1)
            ->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'item_id')
            ->where('item_type', 'blog');
    }
}
