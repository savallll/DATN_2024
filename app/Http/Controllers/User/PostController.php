<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
        // dd(Auth::user()->id);
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        if($request->has('parent_id')){
            $data['parent_id'] = $request->input('parent_id');

        }

        // dd($data);

        $post = Post::create($data);

        return redirect()->back()->with(['message' => 'create Post success']);
    }


    public function show(string $id)
    {
        //
    }


    public function removeTag(string $id)
    {
        //
        $post = Post::findOrFail($id);
        if($post->parent_id == Auth::user()->id){
            $post->parent_id = null ;
        }

        $post->save();
        return redirect()->back();

    }


    public function update(Request $request, string $id)
    {
        //
        $data = $request->all();
        $post = Post::findOrFail($id);

        if($post->user_id == Auth::user()->id || $post->parent_id == Auth::user()->id){
            $post->update($data);
        }
        
        return redirect()->back();
    }

    public function delete(string $id)
    {
        //
        $post = Post::findOrFail($id)->delete();
        return redirect()->back();
    }

    public function like(Request $request, Post $post)
    {
        $user = Auth::user();
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false, 'likes_count' => $post->likes()->count()]);
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
                'like' => 1
            ]);
            return response()->json(['liked' => true, 'likes_count' => $post->likes()->count()]);
        }
    }

    public function loadMorePosts(Request $request)
    {
        if ($request->ajax()) {
            $posts = Post::where('user_id', auth()->user()->id)
                            ->orWhere('user_id', Auth::user()->friends_ids())
                            ->orWhere('parent_id', Auth::user()->friends_ids())
                            ->orderBy('updated_at', 'desc')
                            ->paginate(2);

            return view('layouts.posts', compact('posts'))->render();
        }
    }
}
