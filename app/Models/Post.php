<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['userId', 'title', 'body'];

    //a post has many commnets
    public function comments()
    {
        return $this->hasMany(Comment::class, 'postId');
    }

    //the post belongs to the user
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
