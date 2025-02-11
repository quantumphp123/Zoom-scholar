<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    
    public function user()
{
    return $this->belongsTo(User::class);
}

  protected $hidden = [
        
        'question_id',
        
    ];

}
