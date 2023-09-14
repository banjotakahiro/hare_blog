<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
    /* テーブルから全てのレコードを取得する */
        $thread = Post::query();
        
        
         /* キーワードから検索処理 */
         // 任意の変数に受け取った送信された情報を代入します
         // htmlのinputタグにはname属性に対して'keyword'と設定されているため
         // $keywordへ$requestの中から、nameが'keyword'のinputを代入します

        $keyword = $request->input('keyword');
        if(!empty($keyword)) { //もしも、$keywordの中身が空ではない場合に検索処理実行
            $thread->where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('body', 'LIKE', "%{$keyword}%")
            ->get();
            $posts = $thread->with('user')->latest()->paginate(4);
        }else {
            $posts = Post::with('user')->latest()->paginate(4);
        }
        
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = new Post($request->all());
        $post->user_id = $request->user()->id;

        $file = $request->file('image');
        $post->image = self::createFileName($file);

        // トランザクション開始
        DB::beginTransaction();

        try {
            // 登録
            $post->save();

            // 画像アップロード
            if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('notice', '記事を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // withは紐づいている別の情報も持ってくることができる。今回はユーザーの情報になっている。
        $post = Post::with(['user'])->find($id);
        // コメントに紐づくユーザー情報も取得している
        $comments = $post->comments()->latest()->get()->load(['user']);

        return view('posts.show', compact('post','comments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::find($id);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::find($id);

        if ($request->user()->cannot('update', $post)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');

        if ($file) {
            $delete_file_path = $post->image_path;
            $post->image = self::createFileName($file);
        }

        $post->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $post->save();

            if ($file) {
                // 画像アップロード
                if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }

                if (!Storage::delete($delete_file_path)) {
                    Storage::delete($post->image_path);
                    throw new \Exception('画像ファイルの削除に失敗しました。');
                }
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('notice', '記事を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        // トランザクション開始
        // DBを指定することで仮削除という形になる
        DB::beginTransaction();
        try {
            $post->delete();

            // 画像削除
            // ここで画像が削除される
            if (!Storage::delete($post->image_path)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの削除に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            // データベースには最初は画像名が削除される
            // ロールバックにより復活する
            DB::rollback();
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.index')
            ->with('notice', '記事を削除しました');
    }

    private static function createFileName($file)
    {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
