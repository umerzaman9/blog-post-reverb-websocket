<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['postId', 'userId', 'body'];

    //the comment belongs to this user 
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    //the comment belongs to this post
    public function post()
    {
        return $this->belongsTo(Post::class, 'postId');
    }
}
