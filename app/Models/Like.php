<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'item_id', 'item_type', 'user_id',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
