<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;
    protected $fillable=[
        'body'
    ];
    
    // public function post()
    // {
    //     return $this->belongsTo(Post::class);
    // }
}
