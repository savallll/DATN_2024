<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //
    public function store(Request $request,Post $post){
        if(!$this->canComment($request->user_id)){
            return back();
        }

        auth()->user()->comments()->create([
            'body' => $request->body,
            'post_id' => $post->id,
        ]);
        return back();

    }

    private function canComment($userId){
        $currentUser = Auth::user();

        if($currentUser->id == $userId || $currentUser->is_friends_with($userId)){
            return true;
        }
        return false;
    }

    public function like(Request $request, Comment $comment)
    {
        $user = Auth::user();
        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false, 'likes_count' => $comment->likes()->count()]);
        } else {
            $comment->likes()->create([
                'user_id' => $user->id,
                'like' => 1
            ]);
            return response()->json(['liked' => true, 'likes_count' => $comment->likes()->count()]);
        }
    }

    public function update(Request $request, Comment $comment){
        $user = Auth::user();
        $data = $request->body;
        // dd($comment);

        if($comment->user_id == $user->id){
            $comment->body = $data;
            $comment->save();
        }

        return back();

    }

    public function delete(Comment $comment){
        $user = Auth::user();

        if($comment->user_id == $user->id){
            $comment->delete();
        }

        return back();
    }
}
