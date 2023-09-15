<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreWarningRequest;
use App\Models\Warning;
use App\Models\Post;

class WarningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return view('warnings.index', compact('post'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarningRequest $request, Post $post)
    {
        $warning = new Warning($request->all());
        try {
            $warning->post_id = $post->id;
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
        $warning->save();
        $post = Post::all();
        return redirect()
            ->route('posts.index')
            ->with('notice', '記事を通報しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
