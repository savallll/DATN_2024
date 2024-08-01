<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    //
    public function index($id){

        $user = User::findOrFail($id);
        $posts = Post::where('user_id', $id)
                        ->orWhere('parent_id', $id)
                        ->orderBy('updated_at', 'desc')
                        ->paginate(2);

        // $authUser = Auth::user();
        if (Auth::user()->is_friends_with($user->id)) {
            $user->friendStatus = 'friends';
        } elseif (Auth::user()->has_pending_friend_request_sent_to($user->id)) {
            $user->friendStatus = 'sent';
        } elseif (Auth::user()->has_pending_friend_request_from($user->id)) {
            $user->friendStatus = 'received';
        } else {
            $user->friendStatus = 'none';
        }
        

        return view('profile.index', compact('user','posts'));
    }

    public function edit(){

        $user = User::findOrFail(Auth::user()->id);

        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request){

        $user = User::findOrFail(Auth::user()->id);
        $data = $request->all();

        $user->update($data);

        return redirect()->back();
    }

    public function updateImage(Request $request){

        $user = User::findOrFail(Auth::user()->id);
        if($request->input('avatar')){
            $user->avatar = $avatar;
            $user->save();
        }
        if($request->input('coverImg')){
            $user->coverImg = $coverImg;
            $user->save();
        }
        

        return redirect()->back();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'oldPassWord' => 'required',
            'newPassWord' => 'required',
            'confirmPassWord' => 'required|same:newPassWord',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->oldPassWord, $user->password)) {
            throw ValidationException::withMessages([
                'oldPassWord' => 'Mật khẩu cũ không đúng.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->newPassWord),
        ]);

        return redirect()->back()->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }
    
}
