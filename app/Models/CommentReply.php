<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_comment_id',
    ];

    // protected function getCreatedAtAttribute($value) {
    //     return date('d-M-Y', strtotime($value));
    // }

    // protected function getUpdatedAtAttribute($value) {
    //     return date('d-M-Y', strtotime($value));
    // }

    public function users() {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}