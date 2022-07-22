<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends ApiController
{
    // auth middleware will be applied to store and update methods in this class
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return $this->successResponse($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
            'user_id' => 'required',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->getMessageBag(), 422);
        }
        $slug = Str::slug($request->title);

        // upload cover
        $cover = $request->file('cover');
        $coverName = $slug . '.' . $cover->getClientOriginalExtension();
        $cover->move(public_path('images'), $coverName);
        $coverPath = 'images/' . $coverName;

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
            'cover' => $coverPath,
            'slug' => $slug,
        ]);

        return $this->successResponse($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $comments = $post->comments;
        return $this->successResponse([
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->getMessageBag(), 422);
        }
        $slug = Str::slug($request->title);

        // upload cover
        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverName = $slug . '.' . $cover->getClientOriginalExtension();
            $cover->move(public_path('images'), $coverName);
            $coverPath = 'images/' . $coverName;
            $post->cover = $coverPath;
        }

        $post->title = $request->title;
        $post->content = $request->content;
        $post->category_id = $request->category_id;
        $post->user_id = $request->user_id;
        $post->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->successResponse($post);
    }
}
