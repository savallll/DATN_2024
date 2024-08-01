<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //
    public function index(){
        $friendList = Auth::user()->friends();

        $pendingFriendRequests = array_slice(Auth::user()->pending_friend_requests(), 0, 5);

        $posts = Post::where('user_id', auth()->user()->id)
                        ->orWhere('user_id', Auth::user()->friends_ids())
                        ->orWhere('parent_id', Auth::user()->friends_ids())
                        ->orderBy('updated_at', 'desc')
                        ->paginate(2);
        // dd($posts);

        $viewData = [
            'friendList' => $friendList,
            'pendingFriendRequests' => $pendingFriendRequests,
            'posts' => $posts,
        ];

        return view('app', $viewData);
    }
}
