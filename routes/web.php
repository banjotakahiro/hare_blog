<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[PostController::class,'index'])
    ->name('root');
    // name('root')でルーティングを設定する

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('posts',PostController::class)
    ->only(['create','store','edit','update','destroy'])
    ->middleware('auth');
    // 認証している人だけ見ることができるコマンド。しかしこれだとログインしている人は全く見れなくなる。
    // なのでonlyで認証している人が見ることのできる画面を定義する

Route::resource('posts',PostController::class)
    ->only(['show','index']);

// URLがそれ用に紐づいている。posts.commentsで読み取れる

Route::resource('posts.comments',CommentController::class);

require __DIR__.'/auth.php';
