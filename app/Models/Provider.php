<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $table = 'authenticationprovider';
    public $timestamps = false;
    protected $fillable = [
        'user_id','provider_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
