<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    // Userが入力するものでデータベースに反映されるものを指定する。
    // protectedは小クラスのインスタンスまでであれば参照できる

    protected $fillable = [
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }


}

