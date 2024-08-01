<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    //
    // public function index(Request $request){

    //     $query = $request->input('key');

    //     $users = User::where('name', 'like', '%' . $query . '%')
    //                   ->orderByDesc('id')
    //                   ->paginate(18);
        
    //     return view('collect.search',compact('users'));
    // }
    public function index(Request $request)
    {
        $query = $request->input('key');

        $users = User::where('name', 'like', '%' . $query . '%')
                      ->orderByDesc('id')
                      ->paginate(18);

        // $user->friendStatus = 'none';
        $authUser = Auth::user();
        foreach ($users as $user) {
            if ($authUser->is_friends_with($user->id)) {
                $user->friendStatus = 'friends';
            } elseif ($authUser->has_pending_friend_request_sent_to($user->id)) {
                $user->friendStatus = 'sent';
            } elseif ($authUser->has_pending_friend_request_from($user->id)) {
                $user->friendStatus = 'received';
            } else {
                $user->friendStatus = 'none';
            }
        }

        return view('collect.search', compact('users'));
    }
}
