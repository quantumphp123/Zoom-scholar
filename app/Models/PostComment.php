<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    protected function replies()
    {
        return $this->belongsTo(CommentReply::class);
    }

    public function users() {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}